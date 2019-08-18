<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('MOODLE_INTERNAL') || die();

require_once('locallib.php');
require_once('course.php');
require_once($CFG->libdir . '/formslib.php');

class course_mapping_editadvanced_form extends moodleform {

    protected function definition() {

        $mform = $this->_form;

        //$strgeneral = get_string('general');
        // Print the required moodle fields first.
        //$mform->addElement('header', 'moodle', $strgeneral);

        $courses = get_all_courses_available();




        $c_shortname = array_column($courses, 'shortname');


        //$mform->addElement('select', 'course_moodle', get_string('course_moodle', 'enrol_saml'), $c_shortname);
        //$mform->addHelpButton('status', 'status', 'enrol_saml');

        $mform->addElement('autocomplete', 'course_moodle', get_string('course_moodle', 'enrol_saml'), $c_shortname);
        $mform->addHelpButton('status', 'status', 'enrol_saml');



        $mform->addElement('text', 'saml_id', get_string('saml_id'), 'size="20"');
        $mform->addHelpButton('username', 'username', 'auth');
        https://docs.moodle.org/dev/lib/formslib.php_Form_Definition#Most_Commonly_Used_PARAM_.2A_Types
        $mform->setType('saml_id', PARAM_INT);



        $mform->addElement('advcheckbox', 'active', get_string('active', 'enrol_saml'), 'Mapeo activo');
        $mform->addHelpButton('active', 'active', 'enrol_saml');




        $mform->addElement('advcheckbox', 'blocked', get_string('blocked', 'enrol_saml'), 'Mapeo bloqueado');
        $mform->addHelpButton('blocked', 'blocked', 'enrol_saml');
        //$mform->setDefault('status', $plugin->get_config('status'));


        $this->add_action_buttons(get_string('savechanges'));

        //$this->set_data();
    }

    function validation($data, $files) {
        global $CFG, $DB;

        $errors = parent::validation($data, $files);

        $new_mapping = (object) $data;
        //$course_mapping    = $DB->get_record('course_mapping', array('saml_id' => $new_mapping->saml_id));

        if ($new_mapping->saml_id == 0) {
            $errors['course_mapping'] = get_string('nosamlid');
        }



        $new_mapping->course_moodle = + 2;

        $params = array(
            'saml_id' => $new_mapping->saml_id
        );
        // If there are other coursemapping(s) that already have the same samlid, show an error.
        if ($DB->record_exists_sql('SELECT * FROM {course_mapping} WHERE saml_id = :saml_id', $params)) {
            $errors['course_mapping'] = get_string('coursemappingexists');
        }

        return $errors;
    }

}
