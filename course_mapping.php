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
 * Shows a table with all course mappings. Mappings are shown by pages of 10.
 * Gives the option to edit a course mapping if it source was internal (admin manualy created it).
 * Columns can be asorted and filtered by the admin.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once('locallib.php');
require_once('mapping_filter.php');
require_once($CFG->libdir . '/adminlib.php');


$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$confirmuser = optional_param('confirmuser', 0, PARAM_INT);
$sort = optional_param('sort', 'saml_course_id', PARAM_ALPHAEXT);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);        // how many per page
$acl = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);



admin_externalpage_setup('course_mappings');

$sitecontext = context_system::instance();
$site = get_site();


if (!is_siteadmin()) {
    die('Only admins can execute this action.');
}


if (!has_capability('enrol/saml:config', $sitecontext)) {
    print_error('nopermissions', 'error', '', 'edit/edit course mappings');
}

$stredit = get_string('edit', 'enrol_saml');
$strdelete = get_string('deletemapping', 'enrol_saml');
$strsuspend = get_string('suspendmap', 'enrol_saml');
$strunsuspend = get_string('unsuspendmap', 'enrol_saml');

$returnurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page' => $page));

$course = null;

if ($confirmuser) {
    
} else if ($delete) {              // Delete a selected course mapping, after confirmation
    $course_mapping = $DB->get_record('course_mapping', ['id' => $delete], '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        
        echo $OUTPUT->header();
        $id = $course_mapping->saml_course_id;
        $name = $course_mapping->lms_course_id;
        echo $OUTPUT->heading(get_string('deletemapping', 'enrol_saml'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete));
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckfullmapping1', 'enrol_saml', "'$name'")."".get_string('deletecheckfullmapping2', 'enrol_saml', "'$id'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        if (delete_course_mapping($course_mapping)) {
            redirect($returnurl);
        } else {

            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $course_mapping->lms_course_id));
        }
    }
} else if ($suspend) {
    if ($course = $DB->get_record('course_mapping', ['id' => $suspend])) {
        if ($course->blocked != 1) {
            $course->blocked = 1;
            update_course_mapping($course);
        }
    }
    redirect($returnurl);
} else if ($unsuspend) {

    $course_mapping = $DB->get_record('course_mapping', ['id' => $unsuspend]);

    if ($confirm != md5($unsuspend)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('blockmapping', 'enrol_saml'));
        $id = $course_mapping->saml_course_id;
        $name = $course_mapping->lms_course_id;

        $optionsyes = array('unsuspend' => $unsuspend, 'confirm' => md5($unsuspend));
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('edit'), 'post');

        echo $OUTPUT->confirm(get_string('unlockcheckfullmapping1', 'enrol_saml', "'$name'")."".get_string('unlockcheckfullmapping2', 'enrol_saml', "'$id'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        if ($course_mapping->blocked != 0) {
            $course_mapping->blocked = 0;

            if (update_course_mapping($course_mapping)) {
                redirect($returnurl);
            } else {

                echo $OUTPUT->header();
                echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $course_mapping->lms_course_id));
            }
        }
    }
}

echo $OUTPUT->header();

// create the map filter form
$filter = new mapping_filtering();
$context = context_system::instance();

$columns = ['saml_course_id', 'lms_course_id', 'source', 'creation', 'modified'];

foreach ($columns as $column) {
    $string[$column] = get_map_field_name($column);
    if ($sort != $column) {
        $columnicon = "";

        $columndir = "ASC";
    } else {
        $columndir = $dir == "ASC" ? "DESC" : "ASC";

        $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
        $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, get_string(strtolower($columndir)), 'core', ['class' => 'iconsort']);
    }
    $$column = "<a href=\"course_mapping.php?sort=$column&amp;dir=$columndir\">" . $string[$column] . "</a>$columnicon";
}


list($extrasql, $params) = $filter->get_sql_filter();
$courses = get_course_map_listing($sort, $dir, $page * $perpage, $perpage, '', $extrasql, $params);

$coursescount = course_mapping_count();
$coursesearchcount = course_mapping_count($extrasql, $params);

if ($extrasql !== '') {
    echo $OUTPUT->heading("$coursesearchcount / $coursescount " . get_string('course_map', 'enrol_saml'));
    $coursescount = $coursesearchcount;
} else {
    echo $OUTPUT->heading("$coursescount " . get_string('course_map', 'enrol_saml'));
}

$baseurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
//echo $OUTPUT->paging_bar($coursescount, $page, $perpage, $baseurl);


flush();

if (!$courses) {
    $match = array();
    echo $OUTPUT->heading(get_string('nocoursesfound', 'enrol_saml'));

    $table = NULL;
} else {


    $table = new html_table();
    $table->head = array();
    $table->colclasses = array();

    $table->head[] = $saml_course_id;
    $table->head[] = $lms_course_id;
    $table->head[] = get_string('active', 'enrol_saml');
    $table->head[] = $source;
    $table->head[] = $creation;
    $table->head[] = $modified;
    $table->head[] = get_string('edit');
    $table->colclasses[] = 'centeralign';

    $table->id = "courses";

    foreach ($courses as $course) {

        $buttons = [];

        // suspend button
        if (has_capability('enrol/saml:config', $sitecontext)) {
            if ($course->blocked && !$course->source) {
                $url = new moodle_url($returnurl, array('unsuspend' => $course->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/unlock', $strunsuspend));
            } else {

                $url = new moodle_url($returnurl, array('suspend' => $course->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/lock', $strsuspend));

                // edit button

                if (!$course->source)
                {
                    $url = new moodle_url('/enrol/saml/edit_course_mapping.php', array('mappingid' => $course->id));
                    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
                }
                // delete button

                $url = new moodle_url($returnurl, array('delete' => $course->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
            }
        }

        if (!$course->source) {
            $fuente = get_string('source_internal', 'enrol_saml');
        } else {
            $fuente = get_string('source_external', 'enrol_saml');
        }

        $row = [];
        if ($course_link = $DB->get_record('course', ['shortname' => $course->lms_course_id])) {
            if (get_saml_enrol_status($course)) {
                $status = get_string('active');
            } else {
                $status = get_string('inactive');
            }

            $row[] = $course->saml_course_id;
            $row[] = "<a href=\"$CFG->wwwroot/course/view.php?id=$course_link->id\">$course->lms_course_id</a>";
            $row[] = "<a href=\"$CFG->wwwroot/enrol/saml/edit.php?courseid=$course_link->id\">$status</a>";
            $row[] = $fuente;
            $row[] = date("Y-m-d", $course->creation);

            if (!empty($course->modified)) {
                $row[] = date("Y-m-d", $course->modified);
            } else {
                $row[] = $course->modified;
            }
        } else {

            $row[] = $course->saml_course_id;
            $row[] = $course->lms_course_id;

            $status = get_string('inactive');
            $row[] = $status;

            $row[] = $fuente;

            $row[] = date("Y-m-d", $course->creation);

            if (!empty($course->modified)) {
                $row[] = date("Y-m-d", $course->modified);
            } else {
                $row[] = $course->modified;
            }

            foreach ($row as $k => $v) {
                $row[$k] = html_writer::tag('span', $v, array('class' => 'usersuspended'));
            }
        }

        $row[] = implode(' ', $buttons);

        $table->data[] = $row;
    }
}

echo '</p>';
echo html_writer::link(new moodle_url("/admin/settings.php?section=enrolsettingssaml"), get_string('returntosettings', 'enrol_saml'));
echo '</p>';

// add filters
$filter->display_add();
$filter->display_active();

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
    echo $OUTPUT->paging_bar($coursescount, $page, $perpage, $baseurl);
    echo html_writer::start_tag('div', array('class' => 'usersuspended'));
    echo html_writer::span(get_string('muted_map', 'enrol_saml'));
    echo html_writer::end_tag('div');
}


if (course_count()) {

    $url = new moodle_url('/enrol/saml/edit_course_mapping.php');
    echo $OUTPUT->single_button($url, get_string('new_mapping', 'enrol_saml'), 'get');
} else {
    echo html_writer::start_tag('div');
    echo html_writer::span(get_string('nocourses', 'enrol_saml'));
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();

