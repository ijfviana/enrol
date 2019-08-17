<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require('../../config.php');
require_once('course_mapping_form.php');


/*
$courseid = optional_param('courseid', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/saml:config', $context);

$PAGE->set_url('/enrol/saml/edit_course_mapping.php', ['courseid' => $course->id]);
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/settings.php', ['id' => $course->id]);
if (!enrol_is_enabled('saml')) {
    //Redirects the user to another page, after printing a notice.
    redirect($return);
}

$plugin = enrol_get_plugin('saml');

if ($instances = $DB->get_records('enrol', ['courseid' => $course->id, 'enrol' => 'saml'], 'id ASC')) {
    $instance = array_shift($instances);
    if ($instances) {
        // Oh - we allow only one instance per course!!
        foreach ($instances as $del) {
            $plugin->delete_instance($del);
        }
    }
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(
        new moodle_url('/enrol/instances.php', ['id' => $course->id])
    );
    $instance = new stdClass();
    $instance->id = null;
    $instance->courseid = $course->id;
}*/

$return = new moodle_url('/');
global $DB;

$mform = new course_mapping_editadvanced_form(null, [$instance, $plugin, $context]);

//$mform->set_data($toform);


if ($mform->is_cancelled()) {
    redirect($return);
} else if ($fromform  = $mform->get_data()) {
    
    $timecreated = time();
    /*$courses = get_courses();
    $id = null;
    
    foreach($courses as $course){
        if($course->shortname == $fromform->course_moodle){
            $id=$course->id;
        }
    }*/
    

    
        $fields = [
            'saml_id' => $fromform->saml_id,
            'course_id' => $fromform->course_moodle + 1,
            'active' => $fromform->active,
            'blocked' => $fromform->blocked,
            'source' => 0,
            'creation' => $timecreated
            
        ];
        //new entry in course_mapping table
        $DB->insert_record('course_mapping', $fields);

    
    redirect($return);
}
/*
$PAGE->set_title(get_string('pluginname', 'enrol_saml'));
$PAGE->set_heading($course->fullname);
*/
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_saml'));
$mform->display();
echo $OUTPUT->footer();