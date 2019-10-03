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

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . '/enrol/saml/old_version_importer.php');
require_once($CFG->dirroot . '/enrol/saml/locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');

if (!is_siteadmin()) {
    die('Only admins can execute this action.');
}

navigation_node::override_active_url(
        new moodle_url('/enrol/saml/course_mapping.php')
);

$PAGE->set_url('/enrol/saml/import_old_course_mappings.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance()); // SYSTEM context.


$importer = new mapping_import();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importmappingreview', 'enrol_saml'));

$importer->execute($PAGE->context);


$returnurl = new moodle_url('/enrol/saml/course_mapping.php');

echo $OUTPUT->continue_button($returnurl);
echo html_writer::link(new moodle_url("/report/log/index.php?id=0"), get_string('infolog', 'enrol_saml'));

echo $OUTPUT->footer();
