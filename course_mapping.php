<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('../../config.php');

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
$perpage = optional_param('perpage', 5, PARAM_INT);        // how many per page
$ru = optional_param('ru', '2', PARAM_INT);            // show remote users
$lu = optional_param('lu', '2', PARAM_INT);            // show local users
$acl = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);
$lock = optional_param('unlock', 0, PARAM_INT);
$unlock = optional_param('unlock', 0, PARAM_INT);

$PAGE->set_url('/enrol/saml/course_mapping.php');
$PAGE->set_pagelayout('admin');


$sitecontext = context_system::instance();
$site = get_site();

if (!is_siteadmin()) {
    die('Only admins can execute this action.');
}


if (!has_capability('enrol/saml:config', $sitecontext)) {
    print_error('nopermissions', 'error', '', 'edit/edit course mappings');
}



$stredit = get_string('edit');
$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');
$strshowallcourses = get_string('showallcourses');
$strsuspend = get_string('suspenduser', 'admin');
$strunsuspend = get_string('unsuspenduser', 'admin');
$strunlock = get_string('unlockaccount', 'admin');
$strconfirm = get_string('confirm');


$returnurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page' => $page));


$course = null;

if ($confirmuser) {
    
} else if ($delete) {              // Delete a selected course mapping, after confirmation
    $course_mapping = $DB->get_record('course_mapping', ['id' => $delete], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $course_mapping->course_id], '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = $course->shortname;
        echo $OUTPUT->heading(get_string('deleteuser', 'admin'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete));
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');



        echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        if (delete_course_mapping($course_mapping)) {

            redirect($returnurl);
        } else {

            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $course->shortname));
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
    if ($course = $DB->get_record('course_mapping', ['id' => $unsuspend])) {
        if ($course->blocked != 0) {
            $course->blocked = 0;

            update_course_mapping($course);
        }
    }
    redirect($returnurl);
}

echo $OUTPUT->header();

// Carry on with the user listing
$context = context_system::instance();


$courses = get_some_course_mapping($page*$perpage, $perpage);
$coursescount = course_mapping_count();

$baseurl = new moodle_url('/enrol/saml/course_mapping.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
echo $OUTPUT->paging_bar($coursescount, $page, $perpage, $baseurl);

flush();

if (!$courses) {
    $match = array();
    echo $OUTPUT->heading(get_string('nocoursesfound'));

    $table = NULL;
} else {


    $table = new html_table();
    $table->head = array();
    $table->colclasses = array();


    $table->head[] = get_string('saml_id');

    $table->head[] = get_string('course_id');

    $table->head[] = get_string('active');

    $table->head[] = get_string('blocked');

    $table->head[] = get_string('source');

    $table->head[] = get_string('created');

    $table->head[] = get_string('modified');


    $table->id = "courses";





    foreach ($courses as $course) {

        $buttons = [];

        

        // suspend button
        if (has_capability('enrol/saml:config', $sitecontext) && !$course->source) {
            if ($course->blocked) {
                $url = new moodle_url($returnurl, array('unsuspend' => $course->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/show', $strunsuspend));
            } else {

                $url = new moodle_url($returnurl, array('suspend' => $course->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/hide', $strsuspend));

                // edit button

                    $url = new moodle_url('/enrol/saml/edit_course_mapping.php', array('mappingid' => $course->id));
                    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
                
                // delete button
  

                    $url = new moodle_url($returnurl, array('delete' => $course->id));
                    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
                
            }
        }


        
        if (get_saml_enrol_status($course)) {
            $status = get_string('active');
        }else{
            $status = get_string('inactive');
        }


        $row = [];
        $row[] = $course->saml_id;
        $row[] = $course->course_id;

        $row[] = $status;
        $row[] = $course->blocked;
        $row[] = $course->source;
        $row[] = $course->creation;
        $row[] = $course->modified;

        if ($course->blocked) {
            foreach ($row as $k => $v) {
                $row[$k] = html_writer::tag('span', $v, array('class' => 'usersuspended'));
            }
        }

        $row[] = implode(' ', $buttons);

        $table->data[] = $row;
    }
}


if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
    echo $OUTPUT->paging_bar($coursescount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();

