<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
     * @param array $extraparams extra page parameters
     */
    public function __construct($fieldnames = null, $baseurl = null, $extraparams = null) {
        

        
    }
}