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
defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_saml_upgrade($oldversion) {
    
    global $CFG, $DB;

    require_once($CFG->libdir . '/db/upgradelib.php');
    $dbman = $DB->get_manager();

    if ($oldversion < 2019092701) {

        // Define field id to be added to course_mapping.
        $table = new xmldb_table('course_mapping');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('saml_id', XMLDB_TYPE_CHAR, '55', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('course_id', XMLDB_TYPE_CHAR, '55', null, XMLDB_NOTNULL, null, '0', 'saml_id');
        $table->add_field('blocked', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'course_id');
        $table->add_field('source', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'blocked');
        $table->add_field('creation', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, '0', 'source');
        $table->add_field('modified', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'creation');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('unique_course', XMLDB_KEY_UNIQUE, ['course_id']);


        // Conditionally launch create table for message_popup.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Saml savepoint reached.
        upgrade_plugin_savepoint(true, 2019092701, 'enrol', 'saml');
    }
    return true;
}
