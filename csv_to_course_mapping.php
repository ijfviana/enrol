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
 * Calls a form used for importing course mappings
 * from a csv file
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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


/*
$PAGE->set_url('/enrol/saml/csv_to_course_mapping.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance()); // SYSTEM context.
 * 
 */

admin_externalpage_setup('csv_to_course_mapping');

$sitecontext = context_system::instance();
$site = get_site();


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
    
    

    flush();
    
    $mode = $fromform->mode;
    $parser = new mapping_parser($cir, $mode);
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('importmappingreview', 'enrol_saml'));
    $parser->execute($PAGE->context);



    //echo $OUTPUT->confirm($returnurl, get_string('confirm'));
    echo $OUTPUT->continue_button($returnurl);

    //redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importmapping', 'enrol_saml'));
echo '</p>';
echo html_writer::link(new moodle_url("/admin/settings.php?section=enrolsettingssaml"), get_string('returntosettings', 'enrol_saml'));
echo '</p>';
$mform->display();
echo $OUTPUT->footer();
