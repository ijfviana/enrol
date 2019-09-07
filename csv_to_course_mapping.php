<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Returns something fancy
 *
 * @global moodle_database $DB
 * @global moodle_page $PAGE
 * @global core_renderer $OUTPUT
 */
// We load all moodle config and libs.
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . '/enrol/saml/locallib.php');
require_once($CFG->dirroot . '/enrol/saml/parse_csv_mapping.php');
require_once($CFG->dirroot . '/enrol/saml/csv_to_course_mapping_form.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');


// Validate that the user has admin rights.
if (!is_siteadmin()) {
    die('Only admins can execute this action.');
}



$PAGE->set_url('/enrol/saml/csv_to_course_mapping.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance()); // SYSTEM context.

$returnurl = new moodle_url('/enrol/saml/course_mapping.php');


$mform = new csv_to_mapping_form($PAGE->url);



if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {

    $importid = csv_import_reader::get_new_iid('uploadmapping');
    $cir = new csv_import_reader($importid, 'uploadmapping');
    $content = $mform->get_file_content('userfile');
    $readcount = $cir->load_csv_content($content, $fromform->encoding, $fromform->delimiter_name);
    unset($content);

    if ($readcount === false) {
        print_error('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
    } else if ($readcount == 0) {
        print_error('csvemptyfile', 'error', $returnurl, $cir->get_error());
    }
    
    

    $mode = $fromform->mode;
    $parser = new mapping_parser($cir, $mode);

    $result = $parser->execute($PAGE->context);

    echo $OUTPUT->notification($parser->result_to_string($result));





    //echo $OUTPUT->confirm($returnurl, get_string('confirm'));

    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importmapping', 'enrol_saml'));
$mform->display();
echo $OUTPUT->footer();
