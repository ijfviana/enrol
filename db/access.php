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
 * Capabilities for saml enrolment plugin.
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 //Todos los plugin de Moodle tiene este archivo
 //Access API

 //The Access API gives you functions so you can determine what the current user is allowed to do.
 //It also allows modules to extend Moodle with new capabilities.
 //https://docs.moodle.org/dev/Access_API

// Cualquier archivo php en su complemento será accesible al navegador o ser áe un archivo interno.
//Para archivos internos
defined('MOODLE_INTERNAL') || die();



$capabilities = array(

    // El nombre de la capacidad.
    'enrol/saml:config' => array(
      //tipo de capacidad: escritura
        'captype' => 'write',
      //Declara el nivel de contexto donde se verifica esta capacidad.
        'contextlevel' => CONTEXT_COURSE,
      //especifica valores predeterminados para roles con arquetipos estándar. https://docs.moodle.org/dev/Role_archetypes
      //manager: Los usuarios con capacidad de moodle/course:view  pueden acceder al curso en el que no están inscritos. No pueden participar.
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        )
    ),

    'enrol/saml:enrol' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    'enrol/saml:manage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    'enrol/saml:unenrol' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    'enrol/saml:unenrolself' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
        )
    ),

);
