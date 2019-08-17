<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('../../config.php');
require_once('course.php');
require_once('locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
//require_once($CFG->dirroot . '/user/filters/lib.php');
//require_once($CFG->dirroot . '/user/lib.php');


$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$confirmuser = optional_param('confirmuser', 0, PARAM_INT);
$sort = optional_param('sort', 'name', PARAM_ALPHANUM);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT);        // how many per page
$ru = optional_param('ru', '2', PARAM_INT);            // show remote users
$lu = optional_param('lu', '2', PARAM_INT);            // show local users
$acl = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);
$unlock = optional_param('unlock', 0, PARAM_INT);


$sitecontext = context_system::instance();
$site = get_site();

$returnurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page' => $page));



$stredit = get_string('edit');
$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');
$strshowallcourses = get_string('showallcourses');
$strsuspend = get_string('suspenduser', 'admin');
$strunsuspend = get_string('unsuspenduser', 'admin');
$strunlock = get_string('unlockaccount', 'admin');
$strconfirm = get_string('confirm');




echo $OUTPUT->header();

// Carry on with the user listing
$context = context_system::instance();

// These columns are always shown in the users list.
$requiredcolumns = ['city', 'country', 'lastaccess'];
// Extra columns containing the extra user fields, excluding the required columns (city and country, to be specific).
//$extracolumns = get_extra_user_fields($context, $requiredcolumns);
// Get all user name fields as an array.
//$allusernamefields = get_all_user_name_fields(false, null, null, null, true);
// All of the names are in one column. Put them into a string and separate them with a /.
//$fullnamedisplay = implode(' / ', $fullnamedisplay);


$courses = get_all_course_mapping();

$baseurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
echo $OUTPUT->paging_bar(count($courses), $page, $perpage, $baseurl);

flush();

if (!$courses) {
    $match = array();
    echo $OUTPUT->heading(get_string('nocoursesfound'));

    $table = NULL;
} else {


    $table = new html_table();
    $table->head = array();
    $table->colclasses = array();
    //$table->head[] = $fullnamedisplay;
    
    //$table->attributes['class'] = 'admintable generaltable';
    $table->head[] = get_string('saml_id');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('course_id');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('active');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('blocked');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('source');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('created');
    $table->colclasses[] = 'centeralign';
    $table->head[] = get_string('modified');
    $table->colclasses[] = 'centeralign';

    $table->id = "courses";



    foreach ($courses as $course) {
        // Use the link from $$column for sorting on the user's name.

        $row = array();
        $row[] = $course->saml_id;
        $row[] = $course->course_id;
        $row[] = $course->active;
        $row[] = $course->blocked;
        $row[] = $course->source;
        $row[] = $course->creation;
        $row[] = $course->modified;
        
        $table->data[] = $row;
    }
}



echo html_writer::start_tag('div', array('class' => 'no-overflow'));
echo html_writer::table($table);
echo html_writer::end_tag('div');
echo $OUTPUT->paging_bar(count($courses), $page, $perpage, $baseurl);

echo $OUTPUT->footer();

