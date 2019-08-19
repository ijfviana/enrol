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

function get_all_course_mapping() {

    global $DB;
    $courses = $DB->get_records('course_mapping');
    return $courses;
}

function get_all_course_mapping_custom() {

    global $DB;
    $query = "SELECT saml_id, course_id, active, blocked, creation, modified from {course_mapping}";
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
