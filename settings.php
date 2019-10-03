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
 * Self enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//este formulario aparece al instalar el modulo
//https://docs.moodle.org/dev/Admin_settings

if ($hassiteconfig) {
    
    $pluginname = get_string('pluginname', 'enrol_saml');
    $ADMIN->add('root', new admin_category('saml', get_string('pluginname', 'enrol_saml')));
    
    $ADMIN->add('saml', new admin_externalpage('course_mappings', new lang_string('course_map','enrol_saml'), "$CFG->wwwroot/enrol/saml/course_mapping.php", array('enrol/saml:config')));
    $ADMIN->add('saml', new admin_externalpage('csv_to_course_mapping', new lang_string('csv_to_course_mapping','enrol_saml'), "$CFG->wwwroot/enrol/saml/csv_to_course_mapping.php", array('enrol/saml:config')));

}
if ($ADMIN->fulltree) {
    // General settings.
    $settings->add(
        new admin_setting_heading(
            'enrol_saml_settings',
            '',
            get_string('pluginname_desc', 'enrol_saml')
        )
    );

    // Enrol instance defaults.
    $settings->add(
        new admin_setting_heading(
            'enrol_saml_defaults',
            get_string('enrolinstancedefaults', 'admin'),
            get_string('enrolinstancedefaults_desc', 'admin')
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_saml/defaultenrol',
            get_string('defaultenrol', 'enrol'),
            get_string('defaultenrol_desc', 'enrol'),
            1
        )
    );

    $options = [
        ENROL_INSTANCE_ENABLED  => get_string('yes'),
        ENROL_INSTANCE_DISABLED => get_string('no')
    ];
    $settings->add(
        new admin_setting_configselect(
            'enrol_saml/status',
            get_string('status', 'enrol_saml'),
            get_string('status_desc', 'enrol_saml'),
            ENROL_INSTANCE_ENABLED,
            $options
        )
    );

    $title = get_string('logfile', 'enrol_saml');
    $description = get_string('logfile_description', 'enrol_saml');
    $default = '';
    $setting = new admin_setting_configtext('enrol_saml/logfile', $title, $description, $default, PARAM_RAW);
    $settings->add($setting);

    $settings->add(
        new admin_setting_configtext(
            'enrol_saml/enrolperiod',
            get_string('defaultperiod', 'enrol_saml'),
            get_string('defaultperiod_desc', 'enrol_saml'),
            0,
            PARAM_INT
        )
    );

    require_once($CFG->dirroot.'/enrol/saml/classes/admin_setting_configtext_enrol_trim.php');
    $title = get_string('group_prefix', 'enrol_saml');
    $description = get_string('group_prefix_description', 'enrol_saml');
    $default = '';
    $setting = new admin_setting_configtext_enrol_trim('enrol_saml/group_prefix', $title, $description, $default, PARAM_RAW);
    $settings->add($setting);

    $title = get_string('created_group_info', 'enrol_saml');
    $description = get_string('created_group_info_description', 'enrol_saml');
    $default = '';
    $setting = new admin_setting_configtextarea('enrol_saml/created_group_info', $title, $description, $default, PARAM_RAW);
    $settings->add($setting);

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(
            new admin_setting_configselect(
                'enrol_saml/roleid',
                get_string('defaultrole', 'role'),
                '',
                $student->id,
                $options
            )
        );
    }
    
    $name = 'enrol_saml/supportcourses';
    $title = get_string('enrol_saml_supportcourses', 'enrol_saml');
    $description = get_string('enrol_saml_supportcourses_description', 'enrol_saml');
    $default = "nosupport";
    $choices = [
        "nosupport" => "nosupport",
        "internal" => "internal",
        "external" => "external"
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);
    
    $name = 'enrol_saml/courses';
    $title = get_string('enrol_saml_courses', 'enrol_saml');
    $description = get_string('enrol_saml_courses_description', 'enrol_saml');
    $default = 'schacUserStatus';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
    $settings->add($setting);
    
    $name = 'enrol_saml/ignoreinactivecourses';
    $title = get_string('enrol_saml_ignoreinactivecourses', 'enrol_saml');
    $description = get_string('enrol_saml_ignoreinactivecourses_description', 'enrol_saml');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $settings->add($setting);
    
    
    $options = array('', "access", "ado_access", "ado", "ado_mssql", "borland_ibase", "csv", "db2", "fbsql", "firebird", "ibase", "informix72", "informix", "mssql", "mssql_n", "mssqlnative", "mysqli", "mysqlt", "oci805", "oci8", "oci8po", "odbc", "odbc_mssql", "odbc_oracle", "oracle", "pdo", "postgres64", "postgres7", "postgres", "proxy", "sqlanywhere", "sybase", "vfp");
    $options = array_combine($options, $options);
    $settings->add(new admin_setting_configselect('enrol_saml/dbtype', get_string('dbtype', 'enrol_database'), get_string('dbtype_desc', 'enrol_database'), '', $options));

    $settings->add(new admin_setting_configtext('enrol_saml/dbhost', get_string('dbhost', 'enrol_database'), get_string('dbhost_desc', 'enrol_database'), 'localhost'));

    $settings->add(new admin_setting_configtext('enrol_saml/dbuser', get_string('dbuser', 'enrol_database'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('enrol_saml/dbpass', get_string('dbpass', 'enrol_database'), '', ''));

    $settings->add(new admin_setting_configtext('enrol_saml/dbname', get_string('dbname', 'enrol_database'), get_string('dbname_desc', 'enrol_database'), ''));

    $settings->add(new admin_setting_configtext('enrol_saml/dbencoding', get_string('dbencoding', 'enrol_database'), '', 'utf-8'));
    
    $options = [
        "noupdate" => "noupdate",
        "update" => "update"
    ];
    $settings->add(new admin_setting_configselect('enrol_saml/updatemappings', get_string('update_mapping', 'enrol_saml'), get_string('updatemappings_desc', 'enrol_saml'), 0, $options));
    
    $options = [
        "ignore" => "ignore",
        "notactive" => "notactive"
    ];
    $settings->add(new admin_setting_configselect('enrol_saml/externalmappings', get_string('externalmappings', 'enrol_saml'), get_string('externalmappings_desc', 'enrol_saml'), 0, $options));
    
    
    

    
    
    $settings->add(
    new admin_setting_heading(
        'enrol_saml/coursemapping',
        new lang_string('enrol_saml_coursemapping', 'enrol_saml'),
        new lang_string('enrol_saml_coursemapping_head', 'enrol_saml')
    ));
    
    
    
    $courses = get_courses();
    


    if (!empty($courses)) {
    
        
        $settings->add(
            new enrol_saml_admin_setting_special_link(
                'enrol_saml/check_mapping',
                new lang_string('check_mapping', 'enrol_saml'),
                $CFG->wwwroot.'/enrol/saml/course_mapping.php'
            )
        );
        
        $settings->add(
            new enrol_saml_admin_setting_special_link(
                'enrol_saml/import_old_course_mappings',
                new lang_string('import_old_course_mappings', 'enrol_saml'),
                $CFG->wwwroot.'/enrol/saml/import_old_course_mappings.php'
            )
        );
        
        $settings->add(
            new enrol_saml_admin_setting_special_link(
                'enrol_saml/mapping_export',
                new lang_string('mapping_export', 'enrol_saml'),
                $CFG->wwwroot.'/enrol/saml/course_mappings_to_csv.php'
            )
        );
        
        $settings->add(
            new enrol_saml_admin_setting_special_link(
                'enrol_saml/mapping_import',
                new lang_string('mapping_import', 'enrol_saml'),
                $CFG->wwwroot.'/enrol/saml/csv_to_course_mapping.php'
            )
        );
    }
    
    require_once($CFG->dirroot.'/enrol/saml/classes/admin_setting_special_javascript.php');
    $setting = new enrol_saml_admin_setting_javascript();
    $settings->add($setting);
    

}
