<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// No se puede acceder si no hay courses
require('../../config.php');
require_once('course_mapping_form.php');
require_once('locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');


$mappingid = optional_param('mappingid', 0, PARAM_INT);

global $DB;
//require_login($course);
//require_capability('enrol/saml:config', $context);


$PAGE->set_url('/enrol/saml/edit_course_mapping.php', ['mappingid' => $mappingid]);
$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance()); // SYSTEM context.

$return = new moodle_url('/enrol/saml/course_mapping.php');

if (!enrol_is_enabled('saml')) {
    //Redirects the user to another page, after printing a notice.
    redirect($return);
}


if($mappingid){
    $mappingcourse = $DB->get_record('course_mapping', ['id' => $mappingid], '*', MUST_EXIST);
    $courses = get_courses_not_mapped($mappingcourse->id);
}else{
    $courses = get_courses_not_mapped();
}




$mform = new course_mapping_editadvanced_form($PAGE->url, ['courses' => $courses]);



if ($mform->is_cancelled()) {
    redirect($return);
} else if ($fromform = $mform->get_data()) {

    $time = time();

    $keys = array_keys($courses);
    
    $course = $courses[$keys[$fromform->course_moodle]];

    if (!$mappingid) { //New Course Mapping
        
        

        $fields = [
            'saml_id' => $fromform->saml_id,
            //select devuelve un numero del 0 al ...
            //ponemos el id del curso al que le corresponda esa posición
            'course_id' => $course->shortname,
            'blocked' => $fromform->blocked,
            'source' => 0,
            'creation' => $time
        ];
        //new entry in course_mapping table
        $DB->insert_record('course_mapping', $fields);
    } else {  //Edit Course Mapping
        
        global $DB;
        $mapping = $DB->get_record('course_mapping', ['id' => $mappingid], '*', MUST_EXIST);


        $mapping->saml_id = $fromform->saml_id;
        $mapping->course_id = $course->shortname;
        $mapping->blocked = $fromform->blocked;
        $mapping->modified = $time;
        update_course_mapping($mapping);
    }

    redirect($return);
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_saml'));
$mform->display();
echo $OUTPUT->footer();
