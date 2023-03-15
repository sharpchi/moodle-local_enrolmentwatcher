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
 * Language pack for enrolment watcher
 * @package   local_enrolmentwatcher
 * @author    Mark Sharp <m.sharp@chi.ac.uk
 * @copyright 2020 University of Chichester {@link https://www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['filterfield'] = 'Filter field';
$string['filterfield_desc'] = 'Which field should enrolment watcher check for student status?';
$string['filtervalue'] = 'Filter value';
$string['filtervalue_desc'] = 'How are students identified? e.g. @student, Student. This is case sensitive.';

$string['noroleerror'] = 'You are not correctly enrolled on this page. Please contact <a href="mailto:modular@chi.ac.uk">modular@chi.ac.uk</a> to check your module enrolments.';

$string['privacy:metadata'] = 'The local_enrolmentwatcher plugin does not store any personal data.';

$string['roleassignment'] = 'Role assignments';

$string['roleassignment_appendassignerbody'] = 'Append assigner message';
$string['roleassignment_appendassignerbody_desc'] = 'Should a copy of the message sent to the role assigner also be sent to the extra recipients?';

$string['roleassignment_coursemodules'] = 'Enable for course modules';
$string['roleassignment_coursemodules_desc'] = '<p>You can assign roles on course modules (activities and resources).</p>
  <p>Since roles can only be assigned to people who have already been enrolled, notifications for role assignment on course modules might be overkill.</p>';

$string['roleassignment_desc'] = 'When a student has been assigned a role other than student, a message is sent to a set of recipients.';

$string['roleassignment_extrarecipients'] = 'Extra recipients';
$string['roleassignment_extrarecipients_desc'] = 'List of extra recipients of the message besides the assigner. One username per line.';

$string['roleassignment_message'] = 'Message body';
$string['roleassignment_message_desc'] = 'Message body for the email that will be sent to the person enrolling a student and other recipients.
[[assignee]], [[assigner]], [[item]], [[roleassigned]] will all be filled in automatically. Please replace [[editme]] with your own values.';
$string['roleassignment_message_body'] = '<p>Are you sure you want <strong>[[assignee]]</strong> to have the
    <strong>[[roleassigned]]</strong> role on [[item]]?</p>
    <p>You are seeing this message because [[assignee]] is a student, and they should be added to modules via 
        <a href="mailto:[[editme]]">[[editme]]</a>.</p>
    <h2>Warning!</h2>
    <p>If you have given them the [[roleassigned]] role, they will be able to:</p>
    <ul>
        <li>see all student data in this module</li>
        <li>change or delete your module content</li>
        <li>submit and view Turnitin Assignments for other people</li>
    </ul>
    <h2>What should I do?</h2>
    <ul>
        <li>Unenrol them if they should be enrolled as a student</li>
        <li>If you wish for a PhD student to have a lecturer role, please contact <a href="mailto:[[editme]]">[[editme]]</a></li>
        <li>Speak to [[assignee]] to ensure they have correctly enrolled with <a href="mailto:[[editme]]">[[editme]]</a></li>
        <li>Speak to Student records yourself</li>
        <li>Check you haven\'t used [[assignee]]\'s student account instead of their staff account (if applicable)</li>
    </ul>
    <h2>Data protection breach</h2>
    <p>This is a possible breach of data protection legislation as it may be giving unauthorised access to private information that [[assignee]]
        is not entitled to. <strong>Please do not ignore this email.</strong>
        A copy has been sent to the TEL team (<a href="mailto:[[editme]]">[[editme]]</a>)
        and the Data Protection Officer (<a href="mailto:[[editme]]">[[editme]]</a>).</p>';


$string['roleassignment_roleassignedbody'] = '<p>[[assignee]] has been assigned the [[roleassigned]] role on [[item]] by [[assigner]].</p>
<p>Please check that this is correct by contacting [[assigner]].</p>';
$string['roleassignment_roleassignedsubject'] = 'Role assignment alert: {$a->item}';

$string['roleassignment_sendtoassigner'] = 'Send message to role assigner';
$string['roleassignment_sendtoassigner_desc'] = 'If enabled, a message is sent to the role assigner';

$string['roleassignment_thesystem'] = 'the system level';

$string['pluginname'] = 'Enrolment watcher';