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
 * Strings for component 'enrol_saml', language 'es_es'
 *
 * @package    enrol
 * @subpackage saml
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignrole'] = 'Asignar rol';
$string['defaultperiod'] = 'Periodo por defecto';
$string['defaultperiod_desc'] = 'Longitud del periodo por defecto (en segundos).';
$string['enrolledincourserole'] = 'Matriculado en "{$a->course}" como "{$a->role}"';
$string['enrolusers'] = 'Matricular usuarios';
$string['saml:config'] = 'Congigurar las instancias de matriculación saml';
$string['saml:enrol'] = 'Matricular usuarios';
$string['saml:manage'] = 'Gestionar la matriculación de usuarios';
$string['saml:unenrol'] = 'Desmatricular usuario del curso';
$string['saml:unenrolself'] = 'Auto-desmatricularse del curso';
$string['pluginname'] = 'Matriculación SAML';
$string['pluginname_desc'] = 'El plugin de matriculación SAML permite a los usuarios automatricularse en cursos cuando se loguean usando el plugin auth/saml basandose en la información propodercionada por el Proveedor de Identidad. (asegurate de definir el mapeo de los cursos y de los roles en la configuración del plugin auth/saml';
$string['status'] = 'Habilitar matriculación SAML';
$string['status_desc'] = 'Permitir acceder al curso a los usuarios internamente matriculados.Debería estar habilitado casi siempre.';
$string['status_help'] = 'Esta configuración determina la forma en que los usuarios serán matriculados vía SAML.';
$string['statusenabled'] = 'Habilitado';
$string['statusdisabled'] = 'Deshabilitado';
$string['logfile'] = 'Fichero de log';
$string['logfile_description'] = 'Si se define, información acerca de la matriculación en cursos y grupos será almacenada en el log. (Establece una ruta absoluta o Moodle guardará este fichero dentro de la carpeta moodledata).';
$string['unenrolselfconfirm'] = '¿Realmente desea desmatricularse del curso "{$a}"?';
$string['unenroluser'] = '¿Realmente desea desmatricular al usuario "{$a->user}" del curso "{$a->course}"?';
$string['unenrolusers'] = 'Desmatricular usuarios';
$string['wscannotenrol'] = 'La instancia del plugin de matriculación SAML no puede matricular al usuario en el curso con identificador: (id = {$a->courseid})';
$string['wsnoinstance'] = 'La instancia del plugin de matriculación SAML no existe o está deshabilitado para el curso (id = {$a->courseid})';
$string['wsusercannotassign'] = 'No tienes permisos para asignar este rol ({$a->roleid}) al usuario ({$a->userid}) en este curso ({$a->courseid}).';
$string['error_instance_creation'] = 'Existe una instancia inactiva del plugin de SAML para el curso "{$a}", actívala en lugar de crear una instancia nueva';
$string['group_prefix'] = 'Prefijo para grupos manipulados';
$string['group_prefix_description'] = 'Define un prefijo si quieres que la extensión únicamente manipule aquellos grupos que tengan dicho prefijo. Deja el campo en blanco para manapular cualquier grupo. Campo multi-valor separados por comas';
$string['created_group_info'] = 'Descripción para nuevos grupos';
$string['created_group_info_description'] = 'Establece en este campo el texto que será asociado a la descripción de los grupos creados por la extensión';
