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
 * Enrolment watcher settings file.
 * @package   local_enrolmentwatcher
 * @author    Mark Sharp <m.sharp@chi.ac.uk
 * @copyright 2020 University of Chichester {@link https://www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!$hassiteconfig) {
    return;
}

$settings = new admin_settingpage('local_enrolmentwatcher', new lang_string('pluginname', 'local_enrolmentwatcher'));
$ADMIN->add('localplugins', $settings);

// User field to check.
$options = [
    'department' => new lang_string('department'),
    'email' => new lang_string('email'),
    'idnumber' => new lang_string('idnumber'),
    'institution' => new lang_string('institution')
];
$uifs = $DB->get_records('user_info_field', [], 'name ASC', 'id, shortname, name');
foreach ($uifs as $uif) {
    $options['uif_' . $uif->id] = $uif->name;
}

$name = new lang_string('filterfield', 'local_enrolmentwatcher');
$description = new lang_string('filterfield_desc', 'local_enrolmentwatcher');
$settings->add(new admin_setting_configselect('local_enrolmentwatcher/filterfield',
    $name, $description, 'email', $options
));

// String to test on that field.
$name = new lang_string('filtervalue', 'local_enrolmentwatcher');
$description = new lang_string('filtervalue_desc', 'local_enrolmentwatcher');
$settings->add(new admin_setting_configtext('local_enrolmentwatcher/filtervalue',
    $name, $description, ''
));

// Role assignment heading and description.
$name = 'local_enrolmentwatcher/roleassignment';
$title = new lang_string('roleassignment', 'local_enrolmentwatcher');
$description = new lang_string('roleassignment_desc', 'local_enrolmentwatcher');
$setting = new admin_setting_heading($name, $title, $description);
$settings->add($setting);

// Send message to assigner.
$name = 'local_enrolmentwatcher/roleassignment_sendtoassigner';
$title = new lang_string('roleassignment_sendtoassigner', 'local_enrolmentwatcher');
$description = new lang_string('roleassignment_sendtoassigner_desc', 'local_enrolmentwatcher');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$settings->add($setting);

// Besides the role assigner, who else should receive a message?
$name = 'local_enrolmentwatcher/roleassignment_extrarecipients';
$title = new lang_string('roleassignment_extrarecipients', 'local_enrolmentwatcher');
$description = new lang_string('roleassignment_extrarecipients_desc', 'local_enrolmentwatcher');
$setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_TEXT);
$settings->add($setting);

// Should course modules cause a message to be sent?
$name = 'local_enrolmentwatcher/roleassignment_coursemodules';
$title = new lang_string('roleassignment_coursemodules', 'local_enrolmentwatcher');
$description = new lang_string('roleassignment_coursemodules_desc', 'local_enrolmentwatcher');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$settings->add($setting);

// Should the assigner message be appended to extrarecipients?
$name = 'local_enrolmentwatcher/roleassignment_appendassignerbody';
$title = new lang_string('roleassignment_appendassignerbody', 'local_enrolmentwatcher');
$description = new lang_string('roleassignment_appendassignerbody_desc', 'local_enrolmentwatcher');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$settings->add($setting);

$name = 'local_enrolmentwatcher/roleassignment_message_body';
$title = new lang_string('roleassignment_message', 'local_enrolmentwatcher');
$desc = new lang_string('roleassignment_message_desc', 'local_enrolmentwatcher');
$default = new lang_string('roleassignment_message_body', 'local_enrolmentwatcher');
$settings->add(new admin_setting_confightmleditor($name, $title, $desc, $default, PARAM_RAW));
