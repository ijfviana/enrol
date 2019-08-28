<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    public function execute() {

        global $DB;

        $result[] = ["total" => 0,
            "updated" => 0,
            "ignored" => 0,
            "created" => 0,
            "errors" => 0
        ];


        // Loop over the CSV lines.
        while ($line = $this->cir->next()) {
            $this->linenb++;
            $result["total"] += 1;


            $data = $this->parse_line($line);
            if (!empty($data['saml_id']) && !empty($data['course_id'])) {

                if ($this->exists($data) && $mapping = $DB->get_record('course_mapping', ['course_id' => $data['course_id']])) {

                    if ($this->mode) {

                        $data['modified'] = time();


                        $data['id'] = $mapping->id;
                        update_course_mapping($data);
                        $result["updated"] += 1;
                    } else {

                        $result["ignored"] += 1;
                    }
                } else {

                    if ($DB->record_exists('course', ['id' => $data['course_id']])) {



                        $data['creation'] = time();
                        $result["created"] += 1;
                        $data['source'] = (int) 0;
                        //new entry in course_mapping table
                        $DB->insert_record('course_mapping', $data);
                    } else {
                        $result["errors"] += 1;
                    }
                }
            } else {
                $result["errors"] += 1;
            }
        }


        return $result;
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


        $select = 'saml_id = :saml_id OR course_id = :course_id';
        $params = ['saml_id' => $data['saml_id'], 'course_id' => $data['course_id']];

        return $DB->record_exists_select('course_mapping', $select, $params);
    }

    /**
     * Return the data that will be used upon saving.
     *
     * @return null|array
     */
    public function get_data() {
        return $this->data;
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
        $res[] = null;
        foreach ($result as $key => $value) {
            $res[] = $key . ' (' . $value . ')';
        }
        return $res;
    }

}
