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
 * Adds new instance of enrol_saml to specified course
 * or edits current instance.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// formslib.php es una api para crear formularios de Moodle. Ahi novedades!!!!!!
// https://docs.moodle.org/dev/lib/formslib.php_Usage
require_once($CFG->libdir.'/formslib.php');

class enrol_saml_edit_form extends moodleform {

  // La definición de los elementos a incluir en el formulario.
  //https://docs.moodle.org/dev/lib/formslib.php_Form_Definition
    function definition() {
        $mform = $this->_form;

        //Asigna los elementos del array a las variables, $instance, $plugin, $context
        //_customdata https://moodle.org/mod/forum/discuss.php?d=331414&parent=1334183
        // No se inicializa en este plugin aqui (en edit.php).
        list($instance, $plugin, $context) = $this->_customdata;

// Add elements to your form
        // get_string - String api
        // Devuelve un string para el usuario actual. https://docs.moodle.org/dev/String_API#get_string.28.29
        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_saml'));

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_saml'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_saml');
        $mform->setDefault('status', $plugin->get_config('status'));

        $mform->addElement('duration', 'enrolperiod', get_string('defaultperiod', 'enrol_saml'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('defaultrole', 'role'), $roles);
        $mform->setDefault('roleid', $plugin->get_config('roleid'));

        $mform->addElement('hidden', 'courseid');

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }
}
