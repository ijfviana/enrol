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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/enrol/saml/classes/tracker.php');

class mapping_parser {

    /** @var csv_import_reader */
    protected $cir;

    /** @var int import mode. */
    protected $mode;

    /** @var array CSV columns. */
    protected $columns = array();

    /** @var array of errors where the key is the line number. */
    protected $errors = array();

    /** @var int line number. */
    protected $linenb = 0;

    /**
     * Constructor
     *
     * @param csv_import_reader $cir import reader object

     */
    public function __construct(csv_import_reader $cir, $mode) {

        $this->mode = $mode;
        $this->cir = $cir;
        $this->columns = $cir->get_columns();
        $this->validate();
        $this->reset();
    }

    /**
     * Returns something fancy
     *
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @global core_renderer $OUTPUT
     */
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
        
        $tracker = new uploadmapping_tracker();



        // Loop over the CSV lines.
        while ($line = $this->cir->next()) {
            $this->linenb++;
            //$result["total"] += 1;
            $total++;


            $data = $this->parse_line($line);


            if ($this->prepare($data)) {


                if ($this->exists($data) && $mapping = $DB->get_record('course_mapping', ['course_id' => $data['course_id']])) {

                    //can not modify external source
                    if ($this->mode && !$mapping->source) {

                        $data['modified'] = time();


                        $data['id'] = $mapping->id;
                        if (update_course_mapping($data)) {
                            
                            //$result["updated"] += 1;
                            $updated++;
                            
                            
                            $entry = "Updated Course id " . $data['course_id'] . " SAML id " . $data['saml_id'] . " course mapping";
                            // Trigger an event for creating this field.
                            $this->events($entry, $context);
                            $tracker->output($this->linenb, true, $data);
                            
                        } else {
                            $n_errors++;
                            $errors = "Course id " . $data['course_id'] . " can not be updated";
                            $this->events($errors, $context);
                            $tracker->output($this->linenb, false, $data);
                        }
                    } else {

                        $entry = "Ignored Course id " . $data['course_id'] . " SAML id " . $data['saml_id'] . " course mapping";
                        $this->events($entry, $context);

                        //$result["ignored"] += 1;
                        $ignored++;
                        $tracker->output($this->linenb, true, $data);
                        
                    }
                } else {

                    if ($DB->record_exists('course', ['shortname' => $data['course_id']])) {

                        $data['creation'] = time();
                        $data['source'] = (int) 0;
                        //new entry in course_mapping table
                        if ($DB->insert_record('course_mapping', $data)) {
                            //$result["created"] += 1;
                            $created++;
                            $entry = "Created Course id " . $data['course_id'] . " SAML id " . $data['saml_id'] . " course mapping";
                            $this->events($entry, $context);
                            $tracker->output($this->linenb, true, $data);
                        } else {
                            $n_errors++;
                            $errors = "Course id " . $data['course_id'] . " can not be inserted";
                            $this->events($errors, $context);
                            $tracker->output($this->linenb, false, $data);
                        }
                    } else {

                        $errors = "Course id " . $data['course_id'] . " does not exist";
                        $this->events($errors, $context);
                        //$result["errors"] += 1;
                        $n_errors++;
                        $tracker->output($this->linenb, false, $data);
                    }
                }
            } else {

                $errors = "Entry missing parameters";
                $this->events($errors, $context);
                //$result["errors"] += 1;
                $n_errors++;
                $tracker->output($this->linenb, false, $data);
            }
            
            
        }
        
        $tracker->finish();
        $tracker->results($total, $created, $updated, $n_errors);


        //return $result;
    }

    /**
     * Parse a line to return an array(column => value)
     *
     * @param array $line returned by csv_import_reader
     * @return array
     */
    protected function parse_line($line) {
        $data = [];
        foreach ($line as $keynum => $value) {
            if (!isset($this->columns[$keynum])) {
                // This should not happen.
                continue;
            }

            $key = $this->columns[$keynum];
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Reset the current process.
     *
     * @return void.
     */
    public function reset() {
        $this->processstarted = false;
        $this->linenb = 0;
        $this->cir->init();
        $this->errors = array();
    }

    /**
     * Validation.
     *
     * @return void
     */
    protected function validate() {
        if (empty($this->columns)) {
            throw new moodle_exception('cannotreadtmpfile', 'error');
        } else if (count($this->columns) < 2) {
            throw new moodle_exception('csvfewcolumns', 'error');
        }
    }

    /**
     * Return whether the course exists or not.
     *
     * @global moodle_database $DB
     * @return bool
     */
    protected function exists($data) {
        global $DB;


        $select = 'course_id = :course_id';
        $params = ['course_id' => $data['course_id']];

        return $DB->record_exists_select('course_mapping', $select, $params);
    }

    /**
     * Validates and prepares the data.
     *
     * @return $res false if any error occured.
     */
    protected function prepare($data) {
        global $DB;

        $res = true;
        $site = $DB->get_record('course', ['id' => SITEID]);

        // Validate the shortname.
        if (!empty($data['saml_id']) && !empty($data['course_id']) && $data['course_id'] != $site->shortname) {
            if ($data['course_id'] !== clean_param($data['course_id'], PARAM_TEXT) && $data['saml_id'] !== clean_param($data['saml_id'], PARAM_ALPHAEXT)) {

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

    /**
     * Return whether there were errors with this course.
     *
     * @return boolean
     */
    public function result_to_string($result) {
        $res = $this->mode;
        foreach ($result as $key => $value) {
            $res = $res . '  ' . $key . ' (' . $value . ')';
        }

        return $res;
    }

    function events($entry, $context) {


        $event = \enrol_saml\event\import_event::create(array(
                    'other' => $entry,
                    'context' => $context
        ));
        $event->trigger();
    }

}
