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
 * @package   local_enrolmentwatcher
 * @author    Mark Sharp <m.sharp@chi.ac.uk
 * @copyright 2020 University of Chichester {@link www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_enrolmentwatcher;

use context;
use core_user;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles role assignment events.
 */
class observers {
    /**
     * Role has been assigned
     * @param \core\event\role_assigned
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        global $DB;
        if ($event->other['component'] != '') {
            // Only interested in Manual enrolments.
            return;
        }
        $config = get_config('local_enrolmentwatcher');
        
        $assignee = $DB->get_record('user', ['id' => $event->relateduserid]);
        $field = null;
        if (strpos($config->filterfield, 'uip') === 0) {
            $fieldparts = explode('_', $config->filterfield);
            $field = $DB->get_field('user_info_data', 'data', ['fieldid' => $fieldparts[1], 'userid' => $assignee->id]);
        } else {
            $field = $assignee->{$config->filterfield};
        }
        
        // What to do if there's nothing there? Nothing. Ignore it.
        if (!$field) {
            return;
        }

        // Does this field contain the information we need to check the role assignment?
        if (strpos($field, $config->filtervalue) === false) {
            // No match.
            return;
        }

        $studentroles = $DB->get_records('role', ['archetype' => 'student']);
        if (in_array($event->objectid, array_keys($studentroles))) {
            // A student role has been assigned, this is ok.
            // error_log('A student role has been assigned, this is ok.');
            return;
        }

        // A role can be assigned in different context levels. Usually this is course (50), but it could be something different.
        $context = context::instance_by_id($event->contextid);

        $roleassigned = $DB->get_record('role', ['id' => $event->objectid]);
        // Gets localised role name.
        $roleassignedname = role_get_name($roleassigned, $context);

        $ra = $DB->get_record('role_assignments', ['id' => $event->other['id']]);

        $assigneename = fullname($assignee, true);
        $assigner = $DB->get_record('user', ['id' => $ra->modifierid]);
        $assignername = fullname($assigner, true);

        $courseid = null;
        $coursemodule = null;
        $coursecat = null;
        // If the context isn't course, then I need to get the course info.
        switch ($context->contextlevel) {
            case CONTEXT_BLOCK:
                // Blocks can be added at all levels, but roles aren't assigned here.
                // Though permissions can be assigned to a role using role-override.
                // Lecturers have access to role-override for students - we should stop that perhaps.
                // error_log("{$roleassignedname} role assigned at block level for {$assigneename} by {$assignername}");
                return;
                break;
            case CONTEXT_MODULE:
                // A student can be assigned a role here, but only if they are already enrolled on the course.
                // This could be useful to keep. Make message sending optional.
                if (!$config->roleassignment_coursemodules) {
                    return;
                }
                $coursemodule = $DB->get_record('course_modules', ['id' => $context->instanceid]);
                $cmtype = $DB->get_field('modules', 'name', ['id' => $coursemodule->module]);
                $cminstance = $DB->get_record($cmtype, ['id' => $coursemodule->instance]);
                $courseid = $coursemodule->course;
                // error_log("{$roleassignedname} role assigned at course module level for {$assigneename} by {$assignername}");
                break;
            case CONTEXT_COURSE:
                $courseid = $context->instanceid;
                // error_log("{$roleassignedname} role assigned at course level for {$assigneename} by {$assignername}");
                break;
            case CONTEXT_COURSECAT:
                // Coursecat level can be really dangerous as they would have control over all sub-courses.
                // However, only dept admins have this capability.
                $coursecat = $DB->get_record('course_categories', ['id' => $context->instanceid]);
                // error_log("{$roleassignedname} role assigned at coursecat level for {$assigneename} by {$assignername}");
                break;
            case CONTEXT_USER:
                // User level, shouldn't be a problem as students can add blocks to /my pages.
                // error_log("{$roleassignedname} role assigned at user level for {$assigneename} by {$assignername}");
                return;
                break;
            default:
                // System level, complete no no, but only admins can do that.
                // error_log("{$roleassignedname} role assigned at system level for {$assigneename} by {$assignername}");
                break;
        }
               
        $subjectdata = new stdClass();
        $bodydata = new stdClass();
        $bodydata->roleassigned = $roleassignedname;
        $params = ['id' => $assigner->id];
        
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
            $params['course'] = $course->id;
            $subjectdata->item = $course->shortname;
            $bodydata->item = '<a href="' . 
                new moodle_url('/course/view.php', ['id' => $courseid]) .
                '">' . $course->shortname . '</a>';
        }
        
        $bodydata->assigner = '<a href="' . 
            new moodle_url('/user/view.php', $params) . '" title="' . $assignername . '">' . $assignername . '</a>';

        $params['id'] = $assignee->id;
        $bodydata->assignee = '<a href="' . 
            new moodle_url('/user/view.php', $params) . '" title="' . $assigneename . '">' . $assigneename . '</a>';       

        if ($coursemodule) {
            $coursesection = $DB->get_field('course_sections', 'section', ['id' => $coursemodule->section]);
            $subjectdata->item = $course->shortname . ' - ' . $cminstance->name . ' (' . $cmtype . ')';
            $bodydata->item = '<a href="' . 
                new moodle_url('/course/view.php', ['id' => $courseid], 'section-' . $coursesection) .
                '">' . $course->shortname . ' - "' . $cminstance->name . '" (' . $cmtype . ')</a>';
        }

        if ($coursecat) {
            $subjectdata->item = $coursecat->name;
            $bodydata->item = '<a href="' . new moodle_url('/course/index.php', ['categoryid' => $coursecat->id]) . '">' . $coursecat->name . '</a>';
        }

        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $bodydata->item = get_string('roleassignment_thesystem', 'local_enrolmentwatcher');
            $subjectdata->item = $bodydata->item;
        }
        
        $subject = html_to_text(get_string('roleassignment_roleassignedsubject', 'local_enrolmentwatcher', $subjectdata));
        $assignerbody = get_string('roleassignment_assignerbody', 'local_enrolmentwatcher', $bodydata);
        $body = get_string('roleassignment_roleassignedbody', 'local_enrolmentwatcher', $bodydata);

        if ($config->roleassignment_appendassignerbody) {
            $body = $body . "\n<p>++++++++++++++++++++++++++++++++++</p>\n" . $assignerbody;
        }

        // error_log(print_r($bodydata, true));
        // error_log($subject);
        // error_log($assignerbody);
        // error_log($body);

        $recipients = [];
        // Always include the admin user.
        $recipients[] = core_user::get_user_by_username('admin');
        if (trim($config->roleassignment_extrarecipients) != '') {
            $extrarecipients = explode("\n", $config->roleassignment_extrarecipients);
            foreach($extrarecipients as $er) {
                $recipient = $DB->get_record('user', ['username' => trim($er)]);
                if (!$recipient) {
                    continue;
                }
                $recipients[] = $recipient;
            }
        }

        foreach ($recipients as $recipient) {
            email_to_user($recipient, core_user::get_noreply_user(), $subject, html_to_text($body), $body);
        }
        if ($config->roleassignment_sendtoassigner) {
            $recipient = $DB->get_record('user', ['id' => $ra->modifierid]);
            email_to_user($recipient, core_user::get_noreply_user(), $subject, html_to_text($assignerbody), $assignerbody);
        }
        
    }
}