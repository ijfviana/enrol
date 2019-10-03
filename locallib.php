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
 * Returns all courses available in Moodle
 * 
 * @return all course records
 */
function get_all_courses_available() {
    /* return get_courses(); */

    global $DB;
    $query = "SELECT id, idnumber, shortname from {course} WHERE id !=" . SITEID;
    $courses = $DB->get_records_sql($query);
    return $courses;
}
/**
 * Returns all courses that are not already mapped for selection.
 * 
 * @param int $mapid if its not null we are editing a course mapping
 * @return array all courses that are not already mapped.
 */
function get_courses_not_mapped($mapid = null) {

    global $DB;
    if (!empty($mapid)) { // Can edit course mapping with this id
        $query = "SELECT id, idnumber, shortname from {course} as c WHERE NOT EXISTS( 
    SELECT * FROM {course_mapping} as m WHERE c.shortname = m.course_id and m.id != '" . $mapid . "') AND c.id !=" . SITEID;
    } else { // New course mapping
        $query = "SELECT id, idnumber, shortname from {course} as c WHERE NOT EXISTS( 
    SELECT * FROM {course_mapping} as m WHERE c.shortname = m.course_id) AND c.id !=" . SITEID;
    }

    $courses = $DB->get_records_sql($query);
    return $courses;
}

/**
 * Return all course mappings
 * @return array all records from course_mapping
 */
function get_all_course_mapping() {

    global $DB;
    $courses = $DB->get_records('course_mapping');
    return $courses;
}

/**
 * Return all course mappings
 * @return array all records from course_mapping
 */
function get_all_course_mapping_custom() {

    global $DB;
    $query = "SELECT  id, course_id, saml_id, source, creation, modified from {course_mapping}";
    $courses = $DB->get_records_sql($query);
    return $courses;
}

/**
 * Deletes a course mapping
 * @param object $course
 * @return boolean false if any problem ocurs
 */
function delete_course_mapping($course) {

    global $DB;
    return $DB->delete_records('course_mapping', ['id' => $course->id]);
}

/**
 * Updates a course mapping
 * @param object $course
 * @return boolean false if any problem ocurs
 */
function update_course_mapping($course) {

    global $DB;
    return $DB->update_record('course_mapping', $course);
}

/**
 * Returns the course saml status
 * @param object $course_map
 * @return boolean true if the course saml status is active
 */
function get_saml_enrol_status($course_map) {

    global $DB;

    $getcourse = $DB->get_record('course', ['shortname' => $course_map->course_id]);

    $select = 'courseid = :course_id AND enrol = :enrol AND status = :status';
    // status = 0, means enrol instance is active. table {enrol}
    $saml = 'saml';
    $params = ['course_id' => $getcourse->id, 'enrol' => $saml, 'status' => 0];

    return $DB->record_exists_select('enrol', $select, $params);
}

/**
 * Returns a certain amount of course mappings
 * @param int $limitfrom
 * @param int $limitnum
 * @return array all records within the limits set
 */
function get_some_course_mapping($limitfrom, $limitnum) {

    global $DB;
    $courses = $DB->get_records('course_mapping', null, '', '*', $limitfrom, $limitnum);
    return $courses;
}

/**
 * Counts the number of course mappings
 * @param string $extraselect
 * @param array $params
 * @return array all records within the limits set
 */
function course_mapping_count($extraselect='', array $params=null) {

    global $DB;
    if(!$extraselect){
        $count = $DB->count_records('course_mapping');
    }else{

        $count = $DB->count_records_select('course_mapping',$extraselect,$params);
    }
    
    return $count;
}

/**
 * Return a field name in enrol_saml
 * @param string $field
 * @return string from enrol_saml
 */
function get_map_field_name($field) {
    return get_string($field, 'enrol_saml');
}

/**
 * Return filtered (if provided) list of users in site, except guest and deleted users.
 *
 * @global moodle_database $DB
 * @param string $sort An SQL field to sort by
 * @param string $dir The sort direction ASC|DESC
 * @param int $page The page or records to return
 * @param int $recordsperpage The number of records to return per page
 * @param string $search A simple string to search for
 * @param string $firstinitial Users whose first name starts with $firstinitial
 * @param string $lastinitial Users whose last name starts with $lastinitial
 * @param string $extraselect An additional SQL select statement to append to the query
 *   as appropriate for current user and given context
 * @return array Array of course_mapping records
 */
function get_course_map_listing($sort = 'saml_id', $dir = 'ASC', $page = 0, $recordsperpage = 0, $search = '', $extraselect = '', array $extraparams = null) {

    global $DB, $CFG;

    $select = '';
    $params = [];

    if (!empty($search)) {
        $search = trim($search);
        if (!$select) {
            $select .= "(" . $DB->sql_like('saml_id', ':search1', false, false) .
                    " OR " . $DB->sql_like('course_id', ':search2', false, false);
            
        } else {
            $select .= " AND (" . $DB->sql_like('saml_id', ':search1', false, false) .
                    " OR " . $DB->sql_like('course_id', ':search2', false, false);
            
        }
        $params['search1'] = "%$search%";
        $params['search2'] = "%$search%";
    }

    if ($extraselect) {
        if (!$select) {
            $select .= "$extraselect";
        } else {
            $select .= " AND $extraselect";
        }

        $params = $params + (array) $extraparams;
    }

    if ($sort) {
        $sort = " ORDER BY $sort $dir";
    }


    if(!$select){
        return $DB->get_records_sql("SELECT id, saml_id, course_id, blocked, source, creation, modified
                                   FROM {course_mapping}
                                  $sort", $params, $page, $recordsperpage);
    }else{
        return $DB->get_records_sql("SELECT id, saml_id, course_id, blocked, source, creation, modified
                                   FROM {course_mapping}
                                   WHERE $select
                                  $sort", $params, $page, $recordsperpage);
    }
    
}
