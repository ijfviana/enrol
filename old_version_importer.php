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


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/enrol/saml/classes/tracker.php');

class mapping_import {

    /** @var array of errors where the key is the line number. */
    protected $errors = array();

    /** @var int line number. */
    protected $linenb = 0;

    /**
     * Constructor
     *
     * @param csv_import_reader $cir import reader object

     */
    public function __construct() {

        $this->reset();
    }

    /**
     * Returns something fancy
     *
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @global core_renderer $OUTPUT
     */
    protected function get_old_course_mapping() {
        global $DB;


        return $DB->get_records_sql('SELECT * FROM {config_plugins} WHERE ' . $DB->sql_like('name', ':name'), ['name' => 'course_mapping_%', 'plugin' => 'auth_saml']);
    }

    public function execute($context) {

        global $DB;

        /* $result[] = ["total" => 0,
          "updated" => 0,
          "ignored" => 0,
          "created" => 0,
          "errors" => 0
          ]; */

        $total = 0;
        $updated = 0;
        $ignored = 0;
        $created = 0;
        $n_errors = 0;

        $tracker = new old_mapping_tracker();

        // We may need a lot of memory here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        if ($course_map = $this->get_old_course_mapping()) {



            // Loop over the CSV lines.
            foreach ($course_map as $map) {


                

                $mappings = $this->explode_mapping($map);

                //we only delete the course mapping from config_plugins if both are created with no errors
                $n_elemets = count($mappings);
                $cont = 0;

                foreach ($mappings as $new_map) {
                    
                    $tracker->start();
                    
                    $this->linenb++;
                    $total++;


                    if ($this->prepare($new_map)) {


                        if ($this->exists($new_map)) {

                            $entry = "Ignored Course id " . $new_map->course_id . ", SAML id " . $new_map->saml_id . " course mapping";
                            $this->events($entry, $context);

                            $ignored++;
                            $tracker->output($this->linenb, true, $new_map);
                        } else {


                            $new_map->creation = time();
                            $new_map->source = (int) 0;
                            //new entry in course_mapping table
                            if ($DB->insert_record('course_mapping', $new_map)) {
                                
                                $created++;
                                $entry = "Created 'Course id' " . $new_map->course_id . ", 'SAML id' " . $new_map->saml_id . " course mapping";
                                $this->events($entry, $context);
                                $tracker->output($this->linenb, true, $new_map);
                            } else {
                                $n_errors++;
                                $errors = "Course id " . $new_map->course_id . ", 'SAML id' " . $new_map->saml_id . " can not be inserted";
                                $this->events($errors, $context);
                                $tracker->output($this->linenb, false, $new_map);
                            }
                        }
                        $cont++;
                    } else {

                        $errors = "Entry missing parameters 'Course id' " . $new_map->course_id . ", 'SAML id' " . $new_map->saml_id;
                        $this->events($errors, $context);
                        $n_errors++;
                        $tracker->output($this->linenb, false, $new_map);
                    }
                }

                if ($n_elemets == $cont) {

                    if (!$DB->delete_records('config_plugins', ['id' => $map->id])) {
                        $n_errors++;
                        $errors = "Course id " . $new_map->course_id . ", 'SAML id' " . $new_map->saml_id . " can not be deleted";
                        $this->events($errors, $context);
                        $tracker->output($this->linenb, false, $new_map);
                    }
                }
            }

            $tracker->finish();
            $tracker->results($total, $created, $ignored, $n_errors);
        } else {
            echo html_writer::start_tag('div');
            echo html_writer::span(get_string('nocoursesfound', 'enrol_saml'));
            echo html_writer::end_tag('div');
        }

        //return $result;
    }

    protected function explode_mapping($map) {

        $mappings = [];
        $explode_map = explode(",", $map->value);
        foreach ($explode_map as $saml_id) {

            $new_map = new stdClass();
            $new_map->course_id = str_replace('course_mapping_', '', $map->name);
            $new_map->saml_id = $saml_id;
            $mappings[] = $new_map;
        }
        return $mappings;
    }

    /**
     * Reset the current process.
     *
     * @return void.
     */
    public function reset() {
        $this->linenb = 0;
        $this->errors = array();
    }

    /**
     * Return whether the course exists or not.
     *
     * @global moodle_database $DB
     * @return bool
     */
    protected function exists($new_map) {
        global $DB;


        $select = 'course_id = :course_id AND saml_id = :saml_id';
        $params = ['course_id' => $new_map->course_id, 'saml_id' => $new_map->saml_id];

        return $DB->record_exists_select('course_mapping', $select, $params);
    }

    /**
     * Validates and prepares the data.
     *
     * @return $res false if any error occured.
     */
    protected function prepare($new_map) {
        global $DB;

        $res = true;
        $site = $DB->get_record('course', ['id' => SITEID]);

        // Validate the shortname.
        if (!empty($new_map->saml_id) && !empty($new_map->course_id) && $new_map->course_id != $site->shortname) {
            if ($new_map->course_id !== clean_param($new_map->course_id, PARAM_TEXT) && $new_map->saml_id !== clean_param($new_map->saml_id, PARAM_ALPHAEXT)) {

                $res = false;
            }
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     * Return the errors found during preparation.
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Return whether there were errors with this course.
     *
     * @return boolean
     */
    public function has_errors() {
        return !empty($this->errors);
    }

    function events($entry, $context) {


        $event = \enrol_saml\event\import_event::create(array(
                    'other' => $entry,
                    'context' => $context
        ));
        $event->trigger();
    }

}
