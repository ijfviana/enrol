<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ($oldversion < 2019090501) {

    // Define field id to be added to course_mapping.
    $table = new xmldb_table('course_mapping');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
    $table->add_field('saml_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '0', 'id');
    $table->add_field('course_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '0', 'saml_id');
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
    upgrade_plugin_savepoint(true, 2019090501, 'enrol', 'saml');
}
