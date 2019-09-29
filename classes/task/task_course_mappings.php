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
 * Sync users task
 * @package   auth_db
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_saml\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Sync users task class
 * @package   auth_db
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_course_mappings extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_updatecoursemappings', 'enrol_saml');
    }

    /**
     * Run task for synchronising course_mappings.
     *
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @global core_renderer $OUTPUT
     */
    public function execute() {



        if (!enrol_is_enabled('saml')) {
            mtrace('enrol_saml plugin is disabled, synchronisation stopped', 2);
            return;
        }



        $enrol = enrol_get_plugin('saml');
        $config = get_config('enrol_saml');
        $trace = new \text_progress_trace();
        $update = $config->updatemappings;
        $active = $config->externalmappings;

        if ($config->supportcourses == 'external') {
            
            if ($update == "noupdate") {
                $update = 0;
            } else {
                $update = 1;
            }

            if ($active == "ignore") {
                $active = 0;
            } else {
                $active = 1;
            }

            $enrol->update_course_mappings($trace, $update, $active);
        }else{
            mtrace('enrol_saml plugin supportcourses is not external', 2);
            return;
        }
    }

}
