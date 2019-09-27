<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course mappings filter wrapper class
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/user/filters/text.php');
require_once($CFG->dirroot . '/user/filters/date.php');
require_once($CFG->dirroot . '/user/filters/select.php');
require_once($CFG->dirroot . '/user/filters/simpleselect.php');
require_once($CFG->dirroot . '/user/filters/courserole.php');
require_once($CFG->dirroot . '/user/filters/globalrole.php');
require_once($CFG->dirroot . '/user/filters/profilefield.php');
require_once($CFG->dirroot . '/user/filters/yesno.php');
require_once($CFG->dirroot . '/user/filters/cohort.php');
require_once($CFG->dirroot . '/user/filters/user_filter_forms.php');
require_once($CFG->dirroot . '/user/filters/checkbox.php');
require_once($CFG->dirroot . '/enrol/saml/filters/mapping_filter_form.php');

class mapping_filtering {

    /** @var array */
    public $_fields;

    /** @var \user_add_filter_form */
    public $_addform;

    /** @var \user_active_filter_form */
    public $_activeform;

    /**
     * Contructor
     */
    public function __construct() {
        global $SESSION;

        if (!isset($SESSION->map_filtering)) {
            $SESSION->map_filtering = [];
        }



        $fieldnames = ['saml_id' => 1, 'course_id' => 0, 'blocked' => 1, 'creation' => 1, 'modified' => 1];

        $this->_fields = array();

        foreach ($fieldnames as $fieldname => $advanced) {
            if ($field = $this->get_field($fieldname, $advanced)) {
                $this->_fields[$fieldname] = $field;
            }
        }


        $this->_addform = new mapping_add_filter_form(null, ['fields' => $this->_fields]);
        if ($adddata = $this->_addform->get_data()) {
            foreach ($this->_fields as $fname => $field) {
                $data = $field->check_data($adddata);
                if ($data === false) {
                    continue; // Nothing new.
                }
                if (!array_key_exists($fname, $SESSION->map_filtering)) {
                    $SESSION->map_filtering[$fname] = array();
                }
                $SESSION->map_filtering[$fname][] = $data;
            }
            // Clear the form.
            $_POST = array();
            $this->_addform = new mapping_add_filter_form(null, ['fields' => $this->_fields]);
        }
        
        // Now the active filters.
        $this->_activeform = new mapping_active_filter_form(null, ['fields' => $this->_fields]);
        if ($adddata = $this->_activeform->get_data()) {
            if (!empty($adddata->removeall)) {
                $SESSION->map_filtering = array();

            } else if (!empty($adddata->removeselected) and !empty($adddata->filter)) {
                foreach ($adddata->filter as $fname => $instances) {
                    foreach ($instances as $i => $val) {
                        if (empty($val)) {
                            continue;
                        }
                        unset($SESSION->map_filtering[$fname][$i]);
                    }
                    if (empty($SESSION->map_filtering[$fname])) {
                        unset($SESSION->map_filtering[$fname]);
                    }
                }
            }
            // Clear+reload the form.
            $_POST = array();
            $this->_activeform = new mapping_active_filter_form(null, array('fields' => $this->_fields));
        }
    }

    /**
     * Creates known mapping filter from the ones used 
     * for filtering users
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        global $USER, $CFG, $DB, $SITE;

        switch ($fieldname) {
            case 'saml_id': return new user_filter_text('saml_id', get_string('saml_id', 'enrol_saml'), $advanced, 'saml_id');
            case 'course_id': return new user_filter_text('course_id', get_string('course_id', 'enrol_saml'), $advanced, 'course_id');
            case 'active': return new user_filter_yesno('active', get_string('active', 'enrol_saml'), $advanced, 'active');
            case 'blocked': return new user_filter_yesno('blocked', get_string('blocked', 'enrol_saml'), $advanced, 'blocked');
            case 'creation': return new user_filter_date('creation', get_string('creation', 'enrol_saml'), $advanced, 'creation');
            case 'modified': return new user_filter_date('modified', get_string('modified', 'enrol_saml'), $advanced, 'modified');
            default:
                return null;
        }
    }

    /**
     * Print the add filter form.
     */
    public function display_add() {
        $this->_addform->display();
    }
    
    /**
     * Print the active filter form.
     */
    public function display_active() {
        $this->_activeform->display();
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $obj = & $mform->addElement('select', $this->_name, $this->_label, $this->get_roles());
        $mform->setDefault($this->_name, 0);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name);
        }
    }

    public function get_sql_filter($extra = '', array $params = null) {
        global $SESSION;

        $sqls = array();
        if ($extra != '') {
            $sqls[] = $extra;
        }
        $params = (array) $params;

        if (!empty($SESSION->map_filtering)) {
            foreach ($SESSION->map_filtering as $fname => $datas) {
                if (!array_key_exists($fname, $this->_fields)) {
                    continue; // Filter not used.
                }
                $field = $this->_fields[$fname];
                foreach ($datas as $i => $data) {
                    list($s, $p) = $field->get_sql_filter($data);
                    $sqls[] = $s;
                    $params = $params + $p;
                }
            }
        }
        

        if (empty($sqls)) {
            return array('', array());
        } else {
            $sqls = implode(' AND ', $sqls);
            return array($sqls, $params);
        }
    }

}
