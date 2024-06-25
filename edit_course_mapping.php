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
// No se puede acceder si no hay courses


/**
 * Calls a form for adding a new course mapping or
 * edit an existing one.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once('course_mapping_form.php');
require_once('locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');


$mappingid = optional_param('mappingid', 0, PARAM_INT);

global $DB;

if (!is_siteadmin()) {
    die('Only admins can execute this action.');
}

navigation_node::override_active_url(
        new moodle_url('/enrol/saml/course_mapping.php')
);

$PAGE->set_url('/enrol/saml/edit_course_mapping.php', ['mappingid' => $mappingid]);
$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance()); // SYSTEM context.

$return = new moodle_url('/enrol/saml/course_mapping.php');

if (!enrol_is_enabled('saml')) {
    //Redirects the user to another page, after printing a notice.
    redirect($return);
}

/*
  $mappingcourse = null;
  if ($mappingid) {
  $mappingcourse = $DB->get_record('course_mapping', ['id' => $mappingid], '*', MUST_EXIST);
  $courses = get_courses_not_mapped($mappingcourse->id);
  } else {
  $courses = get_courses_not_mapped();
  }
 * 
 */
$mappingcourse = null;
if ($mappingid) {
    $mappingcourse = $DB->get_record('course_mapping', ['id' => $mappingid]);
}
$courses = get_all_courses_available();

if (!empty($courses)) {


    $mform = new course_mapping_editadvanced_form($PAGE->url, ['courses' => $courses, 'mappingcourse' => $mappingcourse]);



    if ($mform->is_cancelled()) {
        redirect($return);
    } else if ($fromform = $mform->get_data()) {

        $time = time();

        $keys = array_keys($courses);

        $course = $courses[$keys[$fromform->course_moodle]];

	if (!$mappingid) { //New Course Mapping
		foreach (explode(",", $fromform->saml_course_id) as $val)
		{
            		$fields = [
                'saml_course_id' => trim($val),
                //select devuelve un numero del 0 al ...
                //ponemos el id del curso al que le corresponda esa posición
                'lms_course_id' => $course->shortname,
		'saml_course_period' => $fromform->saml_course_period,
                'blocked' => $fromform->blocked,
                'source' => 0,
                'creation' => $time
            		];
            		//new entry in course_mapping table
	    		$DB->insert_record('course_mapping', $fields);
		}
        } else {  //Edit Course Mapping
            global $DB;
            $mapping = $DB->get_record('course_mapping', ['id' => $mappingid], '*', MUST_EXIST);


            $mapping->saml_course_id = $fromform->saml_course_id;
            $mapping->lms_course_id = $course->shortname;
            $mapping->blocked = $fromform->blocked;
            $mapping->modified = $time;
	    $mapping->saml_course_period = $fromform->saml_course_period;
            update_course_mapping($mapping);
        }

        redirect($return);
    }


    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'enrol_saml'));
    $mform->display();
    echo $OUTPUT->footer();
} else {
    redirect($return);
}
