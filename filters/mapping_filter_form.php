<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class mapping_add_filter_form extends moodleform {
    
    
    /**
     * Form definition.
     */
    public function definition() {
        
        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];
        
        $mform->addElement('header', 'newfilter', get_string('newfilter', 'filters'));

        foreach ($fields as $ft) {
            $ft->setupForm($mform);
        }



        // Add button.
        $mform->addElement('submit', 'addfilter', get_string('addfilter', 'filters'));
    }

}


/**
 * Class user_active_filter_form
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mapping_active_filter_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $SESSION; // This is very hacky :-(.

        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];

        if (!empty($SESSION->map_filtering)) {
            // Add controls for each active filter in the active filters group.
            $mform->addElement('header', 'actfilterhdr', get_string('actfilterhdr', 'filters'));

            foreach ($SESSION->map_filtering as $fname => $datas) {
                if (!array_key_exists($fname, $fields)) {
                    continue; // Filter not used.
                }
                $field = $fields[$fname];
                foreach ($datas as $i => $data) {
                    $description = $field->get_label($data);
                    $mform->addElement('checkbox', 'filter['.$fname.']['.$i.']', null, $description);
                }
            }

            $objs = array();
            $objs[] = &$mform->createElement('submit', 'removeselected', get_string('removeselected', 'filters'));
            $objs[] = &$mform->createElement('submit', 'removeall', get_string('removeall', 'filters'));
            $mform->addElement('group', 'actfiltergrp', '', $objs, ' ', false);
        }
    }
}