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
 * Library of enrol_saml.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_saml_plugin extends enrol_plugin {

    /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        // Data manipulation API - https://docs.moodle.org/dev/Data_manipulation_API#DB_object
        //
      //Para hacer que el objeto DB esté disponible en nuestro ámbito local, como dentro de una función:
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', ['id' => $instance->roleid])) {
                $context = context_course::instance($instance->courseid);
                $role = ' (' . role_get_name($role, $context) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_' . $enrol) . $role;
        } else {
            //https://docs.moodle.org/dev/Output_functions
            //Función para imprimir cualquier  html / plain / markdown / moodle texto
            return format_string($instance->name);
        }
    }

    public function roles_protected() {
        // Users may tweak the roles later.
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak period and status.
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'saml') {
            throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/saml:config', $context)) {
            $managelink = new moodle_url('/enrol/saml/edit.php', ['courseid' => $instance->courseid, 'id' => $instance->id]);
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'saml') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = [];

        if (has_capability('enrol/saml:manage', $context)) {
            $managelink = new moodle_url("/enrol/saml/manage.php", ['enrolid' => $instance->id]);
            $icons[] = $OUTPUT->action_icon(
                    $managelink, new pix_icon(
                    'i/users', get_string('enrolusers', 'enrol_saml'), 'core', ['class' => 'iconsmall']
                    )
            );
        }
        if (has_capability('enrol/saml:config', $context)) {
            $editlink = new moodle_url("/enrol/saml/edit.php", ['courseid' => $instance->courseid]);
            $icons[] = $OUTPUT->action_icon(
                    $editlink, new pix_icon(
                    'i/edit', get_string('edit'), 'core', ['class' => 'icon']
                    )
            );
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or ! has_capability('enrol/saml:config', $context)) {
            return null;
        }

        if ($DB->record_exists('enrol', ['courseid' => $courseid, 'enrol' => 'saml'])) {
            return null;
        }

        return new moodle_url('/enrol/saml/edit.php', ['courseid' => $courseid]);
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance, null if can not be created
     */
    public function add_default_instance($course) {
        $fields = [
            'status' => $this->get_config('status'),
            'enrolperiod' => $this->get_config('enrolperiod', 0),
            'roleid' => $this->get_config('roleid', 0)
        ];
        return $this->add_instance($course, $fields);
    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = null) {
        global $DB;

        if ($DB->record_exists('enrol', ['courseid' => $course->id, 'enrol' => 'saml'])) {
            // Only one instance allowed.
            return null;
        }

        return parent::add_instance($course, $fields);
    }

    public function get_instance($course) {
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = null;
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "saml") {
                $instance = $courseenrolinstance;
                break;
            }
        }
        return $instance;
    }

    public function get_or_create_instance($course) {
        $instance = $this->get_instance($course);
        if (empty($instance)) {
            $instance = $this->add_instance($course);
        }
        return $instance;
    }

    public function sync_user_enrolments($user) {

        $samlpluginconfig = get_config('auth_saml');
        $enrolpluginconfig = get_config('enrol_saml');

        $prefixes = $enrolpluginconfig->group_prefix;
        if (!empty($prefixes)) {
            $prefixes = explode(",", $prefixes);
        }

        global $DB, $SAML_COURSE_INFO, $err;

        if ($enrolpluginconfig->supportcourses != 'nosupport') {
            if (!isset($samlpluginconfig->moodlecoursefieldid)) {
                $samlpluginconfig->moodlecoursefieldid = 'shortname';
            }
            try {
                $plugin = enrol_get_plugin('saml');
                if (isset($SAML_COURSE_INFO) && !empty($SAML_COURSE_INFO->mapped_roles)) {
                    foreach ($SAML_COURSE_INFO->mapped_roles as $role) {
                        $moodlerole = $DB->get_record("role", ["shortname" => $role]);
                        if ($moodlerole) {
                            $newcourseids = [];
                            $delcourseids = [];
                            if (isset($SAML_COURSE_INFO->mapped_courses[$role])) {
                                if (isset($SAML_COURSE_INFO->mapped_courses[$role]['active'])) {
                                    $newcourseids = array_keys($SAML_COURSE_INFO->mapped_courses[$role]['active']);
                                }
                                if (isset($SAML_COURSE_INFO->mapped_courses[$role]['inactive'])) {
                                    $delcourseids = array_keys($SAML_COURSE_INFO->mapped_courses[$role]['inactive']);
                                }
                            }
                            if (!$enrolpluginconfig->ignoreinactivecourses) {
                                foreach ($delcourseids as $courseid) {
                                    // Check that is not listed on $newcourseids.
                                    if (in_array($courseid, $newcourseids)) {
                                        continue;
                                    }

                                    if ($course = $DB->get_record("course", [$samlpluginconfig->moodlecoursefieldid => $courseid])) {
                                        if ($course->id == SITEID) {
                                            continue;
                                        }
                                        $context = context_course::instance($course->id);
                                        if (user_has_role_assignment($user->id, $moodlerole->id, $context->id)) {
                                            $instance = $plugin->get_or_create_instance($course);
                                            if (!empty($instance)) {
                                                $plugin->unenrol_user($instance, $user->id);
                                                $this->enrol_saml_log_info($user->username . ' unenrolled in course ' . $course->shortname, $enrolpluginconfig->logfile);
                                            }
                                        }
                                    }
                                }
                            }
                            foreach ($newcourseids as $courseid) {
                                if ($course = $DB->get_record("course", [$samlpluginconfig->moodlecoursefieldid => $courseid])) {
                                    if ($course->id == SITEID) {
                                        continue;
                                    }

                                    $instance = $plugin->get_or_create_instance($course);
                                    if (empty($instance)) {
                                        $err['enrollment'][] = get_string(
                                                "error_instance_creation", "role_saml", $role, $course->id
                                        );
                                        $this->enrol_saml_log_error("error enrolling " . $user->username . ' with role ' . $role . ' on course ' . $course->shortname, $enrolpluginconfig->logfile);
                                    } else {
                                        $context = context_course::instance($course->id);
                                        if (!user_has_role_assignment($user->id, $moodlerole->id, $context->id)) {
                                            $plugin->enrol_user($instance, $user->id, $moodlerole->id, 0, 0, 0);
                                            $this->enrol_saml_log_info($user->username . ' enrolled in course ' . $course->shortname . ' with role ' . $role, $enrolpluginconfig->logfile);
                                            // Last parameter (status) 0->active  1->suspended.
                                        }
                                        $this->assign_group($SAML_COURSE_INFO->mapped_courses[$role]['active'][$courseid], $course, $user, $enrolpluginconfig, $prefixes);
                                    }
                                }
                            }
                        } else {
                            $err['enrollment'][] = get_string("auth_saml_error_role_not_found", "auth_saml", $role);
                        }
                    }
                }
            } catch (Exception $e) {
                $err['enrollment'][] = $e->getMessage();
                $this->enrol_saml_log_error("Enrol process for user " . $user->username . ' stopped.' . $e->getMessage(), $enrolpluginconfig->logfile);
            }

            unset($SAML_COURSE_INFO->mapped_courses);
            unset($SAML_COURSE_INFO->mapped_roles);
        }
    }

    public function group_matches_prefixes($groupname, $prefixes) {
        $matches = false;
        if (isset($groupname) && !empty($prefixes)) {
            foreach ($prefixes as $prefix) {
                if (stripos($groupname, $prefix) === 0) {
                    $matches = true;
                    break;
                }
            }
        } else {
            $matches = true;
        }
        return $matches;
    }

    public function assign_group($samlcourseinfo, $course, $user, $enrolpluginconfig, $prefixes) {
        if ($course->groupmode) {
            if (isset($samlcourseinfo['group'])) {
                $groupname = $samlcourseinfo['group'];
                $matchesprefix = $this->group_matches_prefixes($groupname, $prefixes);
                if (isset($groupname) && $matchesprefix) {
                    global $CFG;
                    require_once("$CFG->dirroot/group/lib.php");
                    $groupid = groups_get_group_by_name($course->id, $groupname);
                    if ($groupid == false) {
                        $newgroupdata = new stdClass();
                        $newgroupdata->name = $groupname;
                        $newgroupdata->courseid = $course->id;
                        $newgroupdata->description = isset($enrolpluginconfig->created_group_info) ? $enrolpluginconfig->created_group_info : '';
                        $groupid = groups_create_group($newgroupdata);
                        $this->enrol_saml_log_info('Group ' . $groupname . ' created on course ' . $course->shortname, $enrolpluginconfig->logfile);
                    }
                    $groups = groups_get_all_groups($course->id, $user->id);
                    $found = false;
                    foreach ($groups as $group) {
                        if ($group->id == $groupid) {
                            $found = true;
                        } else {
                            // Unassign from previous groups
                            $matchesprefix = $this->group_matches_prefixes($group->name, $prefixes);
                            if ($matchesprefix) {
                                groups_remove_member($group->id, $user->id);
                                $this->enrol_saml_log_info($user->username . ' unassigned from group ' . $group->name . ' from course ' . $course->shortname, $enrolpluginconfig->logfile);
                            }
                        }
                    }
                    if (!$found) {
                        groups_add_member($groupid, $user->id, 'enrol_saml');
                        $this->enrol_saml_log_info($user->username . ' assigned to group ' . $groupname . ' from course ' . $course->shortname, $enrolpluginconfig->logfile);
                    }
                }
            }
        }
    }

    private function enrol_saml_log_error($msg, $logfile) {
        $this->enrol_saml_write_in_log($msg, $logfile, 'error');
    }

    private function enrol_saml_log_info($msg, $logfile) {
        $this->enrol_saml_write_in_log($msg, $logfile, 'info');
    }

    private function enrol_saml_write_in_log($msg, $logfile, $level = 'error') {
        global $CFG;
        if (isset($logfile) && !empty($logfile)) {
            if (substr($logfile, 0) == '/') {
                $destination = $logfile;
            } else {
                $destination = $CFG->dataroot . '/' . $logfile;
            }
            $msg = auth_saml_decorate_log($msg, $level);
            file_put_contents($destination, $msg, FILE_APPEND);
        }
    }

    private function auth_saml_decorate_log($msg, $level = "error") {
        return $msg = date('D M d H:i:s  Y') . ' [client ' . $_SERVER['REMOTE_ADDR'] . '] [' . $level . '] ' . $msg . "\r\n";
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/apply:config', $context);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/apply:config', $context);
    }

    /**
     * Performs a full sync with external database.
     *
     *
     * @param progress_trace $trace
     * @param int $update 0 means exiting entries wont be updated, 1 entries get updated
     * @param int $active 1 means that previously inserted entried that no longer exit in the ext DB will be inactive, 0 does nothing
     * @return int 0 means success, 1 db connect failure, 4 db read failure
     */
    public function update_course_mappings(progress_trace $trace, $update, $active) {
        global $CFG, $DB;

        // Make sure we sync either enrolments or courses.
        if (!$this->get_config('dbtype')) {
            $trace->output('Course map synchronisation skipped.');
            $trace->finished();
            return 0;
        }

        $trace->output('Starting course mapping update...');

        // We may need a lot of memory here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        if (!$extdb = $this->db_init($trace)) {
            $trace->output('Error while communicating with external enrolment database');
            $trace->finished();
            return 1;
        }

        $external = $this->get_external_source_mappings();



        $sql = "SELECT  lms_course_id, saml_course_id, blocked from course_mapping";

        if ($rs = $extdb->Execute($sql)) {
            if (!$rs->EOF) {
                while ($fields = $rs->FetchRow()) {
                    $fields = array_change_key_case($fields, CASE_LOWER);
                    $data = $this->db_decode($fields);
                    if ($this->course_mapping_conditions($trace, $data, $update) && $active) {

                        $this->delete_when_same_ext_mapping($trace, $data, $external);
                    }
                }



                foreach ($external as $ex_mapping) {


                    $courseid = $DB->get_record('course', ['shortname' => $ex_mapping->lms_course_id]);
                    if ($instance = $DB->get_record('enrol', ['enrol' => 'saml', 'courseid' => $courseid->id])) {
                        if (!$instance->status) {



                            $instance->status = 1;
                            $DB->update_record('enrol', $instance);
                            $trace->output("Course mapping, is now inactive: 'Course id': " . $ex_mapping->lms_course_id . ", SAML id': " . $ex_mapping->saml_course_id . ".");
                        } else {
                            $trace->output("Course mapping, was already inactive: 'Course id': " . $ex_mapping->lms_course_id . ", 'SAML id': " . $ex_mapping->saml_course_id . ".");
                        }
                    }
                }
            }
            $rs->Close();
        } else {
            $extdb->Close();
            $trace->output('Error reading data from the external course table');
            $trace->finished();
            return 4;
        }
        // Close db connection.
        $extdb->Close();
        $trace->output('...course mapping synchronisation finished.');
        $trace->finished();

        return 0;
    }

    
    /**
     * Deletes from $external array all duplicate course mappings, to 
     * know which ones are no longer present on the external database
     *
     *
     * @param progress_trace $trace
     * @param array $data
     * @param array $external
     */
    protected function delete_when_same_ext_mapping(progress_trace &$trace, $data, &$external) {

        foreach ($external as $key => $value) {

            //$trace->output($data['saml_course_id'] . $value->saml_course_id . $data['lms_course_id'] . $value->lms_course_id);

            if ($data['saml_course_id'] == $value->saml_course_id && $data['lms_course_id'] == $value->lms_course_id) {
                unset($external[$key]);
                break;
            }
        }
    }

    /**
     * Here we validate which external course mappings should be
     * ignored, updated or inserted
     *
     *
     * @param progress_trace $trace
     * @param array $data
     * @param int $update
     */
    protected function course_mapping_conditions(progress_trace $trace, $data, $update) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/enrol/saml/locallib.php');

        $entry = false;


        if ($this->prepare($data)) {
            $entry = true;

            if ($this->course_mapping_exists($data) && $mapping = $DB->get_record('course_mapping', ['lms_course_id' => $data['lms_course_id'], 'saml_course_id' => $data['saml_course_id']])) {
                if ($update) {


                    $data['modified'] = time();
                    $data['id'] = $mapping->id;
                    update_course_mapping($data);
                    $trace->output("Update course mapping detected: course id: " . $data['lms_course_id'] . ", saml id: " . $data['saml_course_id'] . ".");
                } else {
                    $trace->output("Can not insert new course mapping, duplicate detected: 'Course id': " . $data['lms_course_id'] . ", 'SAML id': " . $data['saml_course_id'] . ".");
                }
            } else {
                if ($DB->record_exists('course', ['shortname' => $data['lms_course_id']])) {

                    $data['creation'] = time();
                    $data['source'] = (int) 1;
                    //new entry in course_mapping table
                    $DB->insert_record('course_mapping', $data);
                    $trace->output("New course mapping inserted, 'Course id': " . $data['lms_course_id'] . ", 'SAML' id: " . $data['saml_course_id'] . ".");
                } else {
                    $trace->output("Can not insert new course mapping, can not find lms_course_id on table {course}: course id: " . $data['lms_course_id'] . ".");
                }
            }
        } else {
            $trace->output("Can not insert new course mapping, course id and saml id can not be empty. 'Course id': " . $data['lms_course_id'] . ", 'SAML' id: ". $data['saml_course_id'] . ".");
        }
        return $entry;
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
        if (!empty($data['saml_course_id']) && !empty($data['lms_course_id']) && $data['lms_course_id'] != $site->shortname) {
            if ($data['lms_course_id'] !== clean_param($data['lms_course_id'], PARAM_TEXT) && $data['saml_course_id'] !== clean_param($data['saml_course_id'], PARAM_ALPHAEXT)) {

                $res = false;
            }
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     * Returns all external mappings previously inserted 
     *
     * @return StdObject() false if not found
     */
    protected function get_external_source_mappings() {
        global $DB;

        return $DB->get_records('course_mapping', ['source' => 1]);
    }

    /**
     * Tries to make connection to the external database.
     *
     * @return null|ADONewConnection
     */
    protected function db_init(progress_trace $trace) {
        global $CFG;

        require_once($CFG->libdir . '/adodb/adodb.inc.php');


// Connect to the external database (forcing new connection).
        $extdb = ADONewConnection($this->get_config('dbtype'));


// The dbtype my contain the new connection URL, so make sure we are not connected yet.
        if (!$extdb->IsConnected()) {
            $result = $extdb->Connect($this->get_config('dbhost'), $this->get_config('dbuser'), $this->get_config('dbpass'), $this->get_config('dbname'), true);
            if (!$result) {
                return null;
            }
        }

        $extdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($this->get_config('dbsetupsql')) {
            $extdb->Execute($this->get_config('dbsetupsql'));
        }
        return $extdb;
    }

    protected function db_decode($text) {
        $dbenc = $this->get_config('dbencoding');
        if (empty($dbenc) or $dbenc == 'utf-8') {
            return $text;
        }
        if (is_array($text)) {
            foreach ($text as $k => $value) {
                $text[$k] = $this->db_decode($value);
            }
            return $text;
        } else {
            return core_text::convert($text, $dbenc, 'utf-8');
        }
    }

    /**
     * Validates if a course mappings already exists.
     * 
     * @param StdObject() $data
     * @return true if the lms_course_id field exists.
     */
    protected function course_mapping_exists($data) {
        global $DB;


        $select = 'lms_course_id = :lms_course_id AND saml_course_id = :saml_course_id';
        $params = ['lms_course_id' => $data['lms_course_id'], 'saml_course_id' => $data['saml_course_id']];

        return $DB->record_exists_select('course_mapping', $select, $params);
    }

}

/**
 * Indicates API features that the enrol plugin supports.
 *
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function enrol_saml_supports($feature) {
    switch ($feature) {
        case ENROL_RESTORE_TYPE:
            return ENROL_RESTORE_EXACT;
        default:
            return null;
    }
}
