<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once($CFG->libdir.'/formslib.php');

class mapping_add_filter_form extends moodleform {
    
    /**
     * Form definition.
     */
    public function definition() {
        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];
        $extraparams = $this->_customdata['extraparams'];

        $mform->addElement('header', 'newfilter', get_string('newfilter', 'filters'));

        foreach ($fields as $ft) {
            $ft->setupForm($mform);
        }

        // Add button.
        $mform->addElement('submit', 'addfilter', get_string('addfilter', 'filters'));
    }

}
