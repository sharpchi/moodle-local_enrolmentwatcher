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
 * Helper utility functions
 *
 * @package   local_enrolmentwatcher
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_enrolmentwatcher;

class helper {
    /**
     * Replaces variables in the message template with values
     *
     * @param string $setting The setting/language string key
     * @param array $options array of fields to be replaced with values
     * @return string Message
     */
    public static function prepare_message(string $setting, array $options) {
        $message = get_config('local_enrolstaff', $setting);
        if (empty($message)) {
            $message = get_string($setting, 'local_enrolmentwatcher');
        }

        foreach ($options as $key => $option) {
            $message = str_replace('[[' . $key . ']]', $option, $message);
        }
        return $message;
    }
}