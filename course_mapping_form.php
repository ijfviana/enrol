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
 * Form with fields for a new course mapping or 
 * edit a existing one.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once('locallib.php');
//https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#Passing_parameters_to_the_Form
require_once($CFG->libdir . '/formslib.php');

class course_mapping_editadvanced_form extends moodleform {

    /**
     * Define the form.
     */
    protected function definition() {

        $mform = $this->_form;

        //$strgeneral = get_string('general');
        // Print the required moodle fields first.
        //$mform->addElement('header', 'moodle', $strgeneral);

        $courses = $this->_customdata['courses'];
        $mappingcourse = $this->_customdata['mappingcourse'];

        $c_shortname = array_column($courses, 'shortname');

        $cont = 0;
        if (!empty($mappingcourse)) {

            foreach ($c_shortname as $shortname) {

                if ($shortname == $mappingcourse->lms_course_id) {
                    break;
                }
                $cont++;
            }
        }

        $mform->addElement('autocomplete', 'course_moodle', get_string('course_moodle', 'enrol_saml'), $c_shortname);
        $mform->addRule('course_moodle', null, 'required');
        $mform->addHelpButton('course_moodle', 'course_moodle', 'enrol_saml');
        $mform->setType('course_moodle', PARAM_TEXT);

        $mform->addElement('text', 'saml_course_id', get_string('saml_course_id', 'enrol_saml'), 'size="50"');
        $mform->addHelpButton('saml_course_id', 'saml_course_id', 'enrol_saml');
        $mform->addRule('saml_course_id', null, 'required');
        $mform->setType('saml_course_id', PARAM_TEXT);

        $anios = array("2022-23"=>"2022-23","2023-24"=>"2023-24", "2024-25"=>"2024-25", "2025-26"=>"2025-26");
        $mform->addElement('select', 'saml_course_period', get_string('saml_course_period', 'enrol_saml'), $anios);
        $mform->addRule('saml_course_period', null, 'required');
        $mform->addHelpButton('saml_course_period', 'saml_course_period', 'enrol_saml');
        $mform->setType('saml_course_period', PARAM_TEXT);

        if (!empty($mappingcourse)) {
            $mform->setDefault('saml_course_id', $mappingcourse->saml_course_id);
            $mform->setDefault('course_moodle', $cont);
	    $mform->setDefault('saml_course_period', $mappingcourse->saml_course_period);
        }

        $mform->addElement('advcheckbox', 'blocked', '', get_string('blocked', 'enrol_saml'));
        $mform->addHelpButton('blocked', 'blocked', 'enrol_saml');
        //$mform->setDefault('status', $plugin->get_config('status'));


        $this->add_action_buttons(get_string('savechanges'));
        $this->set_data($this->_customdata['courses']);
    }

    /**
     * Validate incoming form data.
     * @param array $data
     * @param array $files
     * @return array
     */
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $new_mapping = (object) $data;

	//$select = 'lms_course_id = :lms_course_id AND saml_course_id = :saml_course_id';
	$select = 'saml_course_id = :saml_course_id';

	foreach(explode(',', $new_mapping->saml_course_id) as $val )
	{
        	$params = ['lms_course_id' => $new_mapping->course_moodle, 'saml_course_id' => $val];

        	if ($DB->record_exists_select('course_mapping', $select, $params)) {
            		$errors['course_mapping'] = get_string('coursemappingexists');
		}
	}
        return $errors;
    }

}

