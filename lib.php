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
 * Mandatory public API of url module
 *
 * @package    mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function jokeofday_supports($feature) {
    switch($feature)
    {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GROUPINGS:
        case FEATURE_GROUPS:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_OTHER;
        default:
            return null;
    }
}

/**
 * @param $instance
 * @return bool|int
 * @throws dml_exception
 */
function jokeofday_add_instance($instance) {
     global $DB;
     global $USER;

     $instance->timecreated = time();
     $instance->timemodified = time();
     $instance->userid = $USER->id;

    $categories = implode(',', $instance->categories);
    $blacklist = implode (',', $instance->blacklist);

    $instance->categories = $categories;
    $instance->blacklist = $blacklist;

    $instance->id = $DB->insert_record('jokeofday', $instance);
    return $instance->id;

}

/**
 * @param object $instance
 * @return bool
 * @throws dml_exception
 */
function jokeofday_update_instance($instance): bool {
    global $DB;
    $instance->timemodified = time();
    $instance->id = $instance->instance;

    $categories = implode(',', $instance->categories);
    $blacklist = implode (',', $instance->blacklist);

    $instance->categories = $categories;
    $instance->blacklist = $blacklist;

    return $DB->update_record('jokeofday', $instance);
}

/**
 * @param $id
 * @return bool
 * @throws dml_exception
 */
function jokeofday_delete_instance($id): bool {
    global $DB;

    if (!$instance = $DB->get_record("jokeofday", array("id" => $id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("jokeofday", array("id" => $instance->id))) {
        $result = false;
    }

    return $result;
}
