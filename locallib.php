<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_all_courses_available() {
    /* return get_courses(); */

    global $DB;
    $query = "SELECT id, idnumber, shortname from {course} WHERE id !=" . SITEID;
    $courses = $DB->get_records_sql($query);
    return $courses;
}

function get_courses_not_mapped($courseid = null) {

    global $DB;
    if (!empty($courseid)) { // Edit course mapping with this id
        $query = "SELECT id, idnumber, shortname from {course} as c WHERE NOT EXISTS( 
    SELECT * FROM {course_mapping} as m WHERE c.id = m.course_id and c.id != '" . $courseid . "') AND c.id !=" . SITEID;
    } else { // New course mapping
        $query = "SELECT id, idnumber, shortname from {course} as c WHERE NOT EXISTS( 
    SELECT * FROM {course_mapping} as m WHERE c.id = m.course_id) AND c.id !=" . SITEID;
    }

    $courses = $DB->get_records_sql($query);
    return $courses;
}

function get_all_course_mapping() {

    global $DB;
    $courses = $DB->get_records('course_mapping');
    return $courses;
}

function get_all_course_mapping_custom() {

    global $DB;
    $query = "SELECT saml_id, course_id, blocked, creation, modified from {course_mapping}";
    $courses = $DB->get_records_sql($query);
    return $courses;
}

function delete_course_mapping($course) {

    global $DB;
    return $DB->delete_records('course_mapping', ['id' => $course->id]);
}

function update_course_mapping($course) {

    global $DB;
    return $DB->update_record('course_mapping', $course);
}

function get_saml_enrol_status($course) {

    global $DB;

    $select = 'course_id = :course_id AND enrol = :enrol AND status = :status' ;
    // status = 0, means enrol instance is active. table {enrol}
    $params = ['course_id' => $course->id, 'enrol' => "saml", 'status' => 0];

    return $DB->record_exists_select('enrol', $select, $params);
}
