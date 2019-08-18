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

function delete_course_mapping($course) {

    global $DB;
    return $DB->delete_records('course_mapping', ['id' => $course->id]);
}

function update_course_mapping($course) {

    global $DB;
    return $DB->update_record('course_mapping', $course);
}

function internal_course_mapping() {
    if ($config->supportcourses == 'internal') {

        $table_course_mapping = $this->get_course_mapping_xmldb();
        $table_role_mapping = $this->get_role_mapping_xmldb();

        if (!$dbman->table_exists($table_course_mapping)) {
            $this->create_course_mapping_db($DB, $err);
        }
        if (!$dbman->table_exists($table_role_mapping)) {
            $this->create_role_mapping_db($DB, $err);
        }

        //COURSE MAPPINGS
        //Delete mappings
        if (isset($config->deletecourses)) {
            if (isset($config->course_mapping_id)) {
                foreach ($config->course_mapping_id as $course => $value) {
                    $sql = "DELETE FROM " . $DB->get_prefix() . "course_mapping WHERE course_mapping_id='" . $value . "'";
                    try {
                        $DB->execute($sql);
                    } catch (Exception $e) {
                        $err['course_mapping_db'][] = get_string("auth_saml_error_executing", "auth_saml") . $sql;
                    }
                }
            }
        } else {
            //Update mappings
            if (isset($config->update_courses_id) && empty($err['course_mapping'])) {
                foreach ($config->update_courses_id as $course_id) {
                    $course = $config->{'course_' . $course_id};
                    $sql = "UPDATE " . $DB->get_prefix() . "course_mapping SET lms_course_id='" . $course[0] . "', saml_course_id='" . $course[1] . "', saml_course_period='" . $course[2] . "' where course_mapping_id='" . $course_id . "'";
                    try {
                        $DB->execute($sql);
                    } catch (Exception $e) {
                        $err['course_mapping_db'][] = get_string("auth_saml_error_executing", "auth_saml") . $sql;
                    }
                }
            }

            //New courses mapping
            if (isset($config->new_courses_total) && empty($err['course_mapping'])) {
                for ($i = 0; $i <= $config->new_courses_total; $i++) {
                    $new_course = $config->{'new_course' . $i};
                    if (!empty($new_course[1]) && !empty($new_course[2])) {
                        $sql = "INSERT INTO " . $DB->get_prefix() . "course_mapping (lms_course_id, saml_course_id, saml_course_period) values('" . $new_course[0] . "', '" . $new_course[1] . "', '" . $new_course[2] . "')";
                        try {
                            $DB->execute($sql);
                        } catch (Exception $e) {
                            $err['course_mapping_db'][] = get_string("auth_saml_error_executing", "auth_saml") . $sql;
                        }
                    }
                }
            }
        }
        //END-COURSE MAPPINGS
    }
    return true;
}
