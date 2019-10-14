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
 * Strings for component 'enrol_saml', language 'en'
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['active'] = 'SAML Status';
$string['assignrole'] = 'Assignrole';
$string['blocked'] = 'Lock edition';
$string['blocked_help'] = 'Check wherever this entry should be modified by the admin or not';
$string['check_mapping'] = 'Check course mappings';
$string['course_id'] = 'Moodle Course id';

$string['course_map'] = 'Course Mappings';
$string['course_moodle'] = 'Moodle course shortname';
$string['course_moodle_help'] = 'Select the Moodle course you want to map';
$string['creation'] = 'Created';
$string['csv_to_course_mapping'] = 'Import CSV to Course Map';
$string['defaultperiod'] = 'Default enrolment period';
$string['defaultperiod_desc'] = 'Default length of the default enrolment period setting (in seconds).';
$string['deletecheckfullmapping1'] = 'Are you absolutely sure you want to completely delete the course mapping with  Moodle course: {$a}';
$string['deletecheckfullmapping2'] = ' and IdP course: {$a}';
$string['deletemapping'] = 'Delete course mapping';


$string['edit'] = "Edit course mapping";


$string['suspendmap'] = "Lock edition of course mapping";

$string['unsuspendmap'] = "Unlock edition of course mapping";


$string['enrol_saml_coursemapping'] = "Course Mapping";
$string['enrol_saml_coursemapping_head'] = "The IdP can use it's own course shortname/idnumber. Set in this section the mapping between IdP and Moodle courses. Accepts multiple valued comma separated.";

$string['enrol_saml_courses'] = 'SAML course mapping';
$string['enrol_saml_courses_description'] = 'SAML attribute that contains courses data';
$string['enrol_saml_courses_not_found'] = "El IdP ha devuelto un conjunto de datos que no contiene el campo donde Moodle espera encontrar los cursos ({\$a}). Este campo es obligatorio para automatricular al usuario.";

$string['enrol_saml_ignoreinactivecourses'] = 'Ignore Inactive';
$string['enrol_saml_ignoreinactivecourses_description'] = "If not checked the plugin will unenroll the 'inactive' users from courses";

$string['enrol_saml_supportcourses'] = 'SAML support courses';
$string['enrol_saml_supportcourses_description'] = 'Select Internal to have Moodle auto-enrol users in courses by the enrol/saml plugin and Select External to also sync course mappings with an external DB. The nosupport option will disable this feature';

$string['externalmappings'] = 'Forgotten course mappings';
$string['externalmappings_desc'] = 'Course mappings that are no longer entries in the DB, will change their Moodle course status to inactive enrollments';

$string['ignore_mapping'] = 'Ignore duplicate mappings';
$string['import'] = 'Import';
$string['importmapping'] = 'Import Course Mappings from .csv';
$string['importmappingreview'] = 'Import Course Mappings results';


$string['import_event'] = 'Import event';
$string['saml:config'] = 'Configure saml enrol instances';
$string['saml:enrol'] = 'Enrol users';
$string['saml:manage'] = 'Manage user enrolments';
$string['saml:unenrol'] = 'Unenrol users from the course';
$string['saml:unenrolself'] = 'Unenrol self from the course';
$string['source_external'] = 'External';
$string['source_internal'] = 'Internal';
$string['pluginname'] = 'SAML enrolments';
$string['pluginname_desc'] = 'The saml enrolments plugin allows users to be auto-enrolled in courses when login using the auth/saml plugin based on the data provided by the Identity Provider. (be sure you provide the course mapping and the role mapping on the settings of the auth/saml plugin';
$string['saml_id'] = 'IDP Course id';
$string['saml_id_help'] = 'Enter the id of the course on the IDP that corresponds with the course in Moodle';
$string['source'] = 'Source';
$string['status'] = 'Enable saml enrolments';
$string['status_desc'] = 'Allow course access of internally enrolled users. This should be kept enabled in most cases.';
$string['status_help'] = 'This setting determines whether users can be auto-enrolled via saml login.';
$string['statusenabled'] = 'Enabled';
$string['statusdisabled'] = 'Disabled';
$string['logfile'] = 'Log file';
$string['logfile_description'] = 'If file defined, enrollment info of courses and groups will be stored. (Use an absolute path or Moodle will save this file in the moodledata folder).';
$string['mappingfile'] = '.csv with course mappings';
$string['mappingfile_help'] = '.csv with colums saml_id for IDP course id and course_id for Moodle course id';
$string['mapping_export'] = 'Export course mappings';
$string['mapping_import'] = "Import course mappings";
$string['new_mapping'] = "New course mappings";
$string['nocourses'] = 'Could not find any Moodle course';
$string['nocoursesfound'] = 'Could not find any course mappings';
$string['nosamlid'] = 'IDP Course id fieldcannot be empty or 0';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['unenrolusers'] = 'Unenrol users';

$string['returntosettings'] = 'Return to settings';

$string['update_mapping'] = 'Update duplicate mappings';
$string['updatemappings_desc'] = 'Update duplicate mappings from the external DB. Updated entries will appear as modified';
$string['wscannotenrol'] = 'SAML plugin instance cannot enrol a user in the course id = {$a->courseid}';
$string['wsnoinstance'] = 'SAML enrolment plugin instance doesn\'t exist or is disabled for the course (id = {$a->courseid})';
$string['wsusercannotassign'] = 'You don\'t have the permission to assign this role ({$a->roleid}) to this user ({$a->userid}) in this course({$a->courseid}).';
$string['error_instance_creation'] = 'Exists an inactive instance of this SAML plugin for this course "{$a}", activate it instead create new one';
$string['group_prefix'] = 'Prefix for managed groups';
$string['group_prefix_description'] = 'Define a prefix if you want that the extension only manages groups that matches the prefix. Leave it blank to manage all. Multi-valued field comma separated';
$string['created_group_info'] = 'Description for new groups';
$string['created_group_info_description'] = 'Set in this field the text that will be used for the description of new groups created by the extension';


$string['modified'] = 'Modified';
$string['task_updatecoursemappings'] = 'Synchronise course mappings task';



$string['coursestotal'] = 'Total mappings: {$a}';
$string['coursescreated'] = 'Mappings created: {$a}';
$string['coursesupdated'] = 'Mappings updated: {$a}';
$string['coursesignored'] = 'Mappings ignored: {$a}';
$string['courseserrors'] = 'Mappings with errors: {$a}';


$string['muted_map'] = 'All Mappings where the Moodle course does not exist appear with text-muted style';

$string['blockmapping'] = 'Unlock course mapping editing';
$string['unlockcheckfullmapping1'] = 'Are you absolutely sure you want to completely unlock the course mapping with Moodle course: {$a}';
$string['unlockcheckfullmapping2'] = ' and IdP course: {$a} editing';

$string['infolog'] = 'More information about this operations on Moodle Logs';
$string['import_old_course_mappings'] = 'Import course mappings from previous version';
