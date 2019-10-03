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
 * Strings for component 'enrol_saml', language 'es'
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



$string['active'] = 'Estado SAML';

$string['blocked'] = 'Bloquear edición';
$string['blocked_help'] = 'Seleccionar si esta entrada podrá ser modificado por el administrador';
$string['check_mapping'] = 'Seleccionar mapeos de cursos';
$string['course_id'] = 'ID Curso Moodle';

$string['course_map'] = 'Mapeo de Cursos';
$string['course_moodle'] = 'Nombre corto del Curso Moodle';
$string['course_moodle_help'] = 'Seleccionar el nombre corto del curso Moodle que quiere mapear';
$string['creation'] = 'Creación';
$string['csv_to_course_mapping'] = 'Importar CSV a Mapeo de Curso';

$string['deletecheckfullmapping'] = 'De verdad quiere eliminar el mapeo de cursos {$a}';
$string['deletemapping'] = 'Eliminar mapeo de curso';


$string['edit'] = "Editar mapeo de curso";


$string['suspendmap'] = "Bloquear la edición del mapeo de curso";

$string['unsuspendmap'] = "Habilitar la edición del mapeo de curso";


$string['enrol_saml_coursemapping'] = "Mapeo de Cursos";
$string['enrol_saml_coursemapping_head'] = "El IdP puede usar su propio shortname para este curso. Introduzca en esta sección el mapeo entre el IdP y el curso Moodle. Acepta valores separados por comas.";

$string['enrol_saml_courses'] = 'SAML course mapping';
$string['enrol_saml_courses_description'] = 'Atributo SAML que contiene los datos de los cursos (por defecto es schacUserStatus)';
$string['enrol_saml_courses_not_found'] = "El IdP ha devuelto un conjunto de datos que no contiene el campo donde Moodle espera encontrar los cursos ({\$a}). Este campo es obligatorio para automatricular al usuario.";

$string['enrol_saml_ignoreinactivecourses'] = 'Ignorar Inactivos';
$string['enrol_saml_ignoreinactivecourses_description'] = "Si no está activado el plugin dará de baja de los cursos a los usuarios 'inactivos'";

$string['enrol_saml_supportcourses'] = 'Soportar matriculación SAML';
$string['enrol_saml_supportcourses_description'] = 'Selecciona Interna o Externa para que Moodle a través del plugin enrol/saml automatricule al usuario (Usa Externa si tu asignación de cursos está en una base de datos externa';

$string['externalmappings'] = 'Mapeos de cursos olvidados';
$string['externalmappings_desc'] = 'Los mapeos de cursos que ya no figuren en la BD externa quedaran marcados con cursos SAML inactivos';

$string['ignore_mapping'] = 'Ignorar mapeos duplicados';
$string['import'] = 'Importar';
$string['importmapping'] = 'Importar Mapeos de Cursos de .csv';
$string['importmappingreview'] = 'Resultados de la Importación de Mapeos de Cursos ';


$string['import_event'] = 'Evento importar ';
$string['source_external'] = 'Externo';
$string['source_internal'] = 'Interno';
$string['saml_id'] = 'id Curso en IDP ';
$string['saml_id_help'] = 'Introduzca el id del curso en el IdP que corresponda con el curso Moodle';
$string['source'] = 'Fuente';
$string['mappingfile'] = '.csv con mapeo de cursos';
$string['mappingfile_help'] = '.csv con columnass saml_id como id curso IdP and course_id como shortname curso Moodle';
$string['mapping_export'] = 'Exportar mapeo de cursos';
$string['mapping_import'] = "Importar mapeo de cursos";
$string['new_mapping'] = "Nuevo mapeo de curso";
$string['nocourses'] = 'No se encontró ningún mapeo de curso Moodle';
$string['nocoursesfound'] = 'No se encontró ningún mapeo de cursos';
$string['nosamlid'] = 'id del curson IdP no puede ser vacío ó 0';


$string['returntosettings'] = 'Volver a Configuración';

$string['update_mapping'] = 'Actualizar mapeos duplicados';
$string['updatemappings_desc'] = 'Actualizar los mapeos duplicados de la BD externa. Las entradas actualizadas aparecerán como modificadas';


$string['modified'] = 'Modificado';
$string['task_updatecoursemappings'] = 'Tarea sincronizar mapeo de cursos';

$string['coursestotal'] = 'Total de mapeos: {$a}';
$string['coursescreated'] = 'Mapeos creados: {$a}';
$string['coursesupdated'] = 'Mapeos actualizados: {$a}';
$string['coursesignored'] = 'Mapeos ignorados: {$a}';
$string['courseserrors'] = 'Mapeos con errores: {$a}';

$string['muted_map'] = 'Todo mapeo cuyo curso Moodle no exista, aparece en el estilo texto silenciado';


$string['blockmapping'] = 'Desbloquear edición mapeo de curso';
$string['unlockcheckfullmapping'] = 'De verdad quiere desbloquear la edición para el mapeo de curso {$a}';

$string['infolog'] = 'Mas información sobre estas operaciones en los Registros Moodle';
$string['import_old_course_mappings'] = 'Importar los mapeos de cursos de la anterior version';