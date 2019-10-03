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
 * Form with necesary fields for importing course mappings from
 * a csv file.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once('locallib.php');
//https://docs.moodle.org/dev/lib/formslib.php_Form_Definition
require_once($CFG->libdir . '/formslib.php');

class csv_to_mapping_form extends moodleform {

    protected function definition() {

        $mform = $this->_form;


        //https://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, array('accepted_types' => '.csv'));
        $mform->addRule('userfile', null, 'required');
        $mform->addHelpButton('userfile', 'mappingfile', 'enrol_saml');


        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploadcourse'), $choices);
        $mform->setDefault('delimiter_name', 'semicolon');

        $mform->addHelpButton('delimiter_name', 'csvdelimiter', 'tool_uploadcourse');


        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploadcourse'), $choices);
        $mform->setDefault('encoding', 'UTF-8');
        $mform->addHelpButton('encoding', 'encoding', 'tool_uploadcourse');


        $choices = [get_string('ignore_mapping', 'enrol_saml'),
            get_string('update_mapping', 'enrol_saml')];
        $mform->addElement('select', 'mode', get_string('mode', 'tool_uploadcourse'), $choices);
        $mform->addHelpButton('mode', 'mode', 'tool_uploadcourse');


        $this->add_action_buttons(false, get_string('import', 'enrol_saml'));
    }

}
