<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

class mapping_filtering {

    /** @var array */
    public $_fields;

    /** @var \user_add_filter_form */
    public $_addform;

    /** @var \user_active_filter_form */
    public $_activeform;

    /**
     * Contructor
     * @param array $fieldnames array of visible user fields
     * @param string $baseurl base url used for submission/return, null if the same of current page
     */
    public function __construct() {


        $this->_fields = ['saml_id' => 0, 'course_id' => 1, 'active' => 1, 'blocked' => 1, 'created' => 1, 'modified' => 1];
        
        foreach ($fieldnames as $fieldname => $advanced) {
            if ($field = $this->get_field($fieldname, $advanced)) {
                $this->_fields[$fieldname] = $field;
            }
        }

        $this->_addform = new mapping_add_filter_form($baseurl, ['fields' => $this->_fields]);
        if ($adddata = $this->_addform->get_data()) {
            foreach ($this->_fields as $fname => $field) {
                
            }
            // Clear the form.
            $_POST = array();
            $this->_addform = new mapping_add_filter_form($baseurl, array('fields' => $this->_fields));
        }
    }

    /**
     * Creates known user filter if present
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        global $USER, $CFG, $DB, $SITE;

        switch ($fieldname) {
            case 'saml_id': return new user_filter_text('username', get_string('username'), $advanced, 'username');
            case 'course_id': return new user_filter_text('realname', get_string('fullnameuser'), $advanced, $DB->sql_fullname());
            case 'active': return new user_filter_yesno('confirmed', get_string('confirmed', 'admin'), $advanced, 'confirmed');
            case 'blocked': return new user_filter_yesno('suspended', get_string('suspended', 'auth'), $advanced, 'suspended');
            case 'created': return new user_filter_date('firstaccess', get_string('firstaccess', 'filters'), $advanced, 'firstaccess');
            case 'modified': return new user_filter_date('lastaccess', get_string('lastaccess'), $advanced, 'lastaccess');
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

}
