<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('MOODLE_INTERNAL') || die();

require_once('locallib.php');
//https://docs.moodle.org/dev/lib/formslib.php_Form_Definition
require_once($CFG->libdir . '/formslib.php');

class csv_to_mapping_form extends moodleform {

    protected function definition() {

        $mform = $this->_form;


        //https://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, 
                array('accepted_types' => '.csv'));
        $mform->addRule('userfile', null, 'required');
        $mform->addHelpButton('userfile', 'coursefile', 'enrol_saml');
        
        
        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploadcourse'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }
        $mform->addHelpButton('delimiter_name', 'csvdelimiter', 'tool_uploadcourse');

        
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploadcourse'), $choices);
        $mform->setDefault('encoding', 'UTF-8');
        $mform->addHelpButton('encoding', 'encoding', 'tool_uploadcourse');
        
        
        $choices = [get_string('ignore_mapping'),
            get_string('update_mapping')];
        $mform->addElement('select', 'mode', get_string('mode', 'tool_uploadcourse'), $choices);
        $mform->addHelpButton('mode', 'mode', 'tool_uploadcourse');
        
        
        $this->add_action_buttons(false, get_string('preview', 'tool_uploadcourse'));
    }

}
