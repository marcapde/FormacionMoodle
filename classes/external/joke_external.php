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
 * @package     mod_jokeofday
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

namespace mod_jokeofday\external;


use context_module;
use Exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use mod_jokeofday\jokeofday_joke;
use moodle_exception;


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/group/lib.php');

class joke_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function score_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'jokeid' => new external_value(PARAM_INT, 'joke ID'),
                'cmid' => new external_value(PARAM_INT, 'course module ID'),
                'value' => new external_value(PARAM_INT, 'JOKE score value'),
            )
        );
    }

    /**
     * @param int $jokeid
     * @param int $cmid
     * @param int $value
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function score(int $jokeid, int $cmid, int $value): array {
        global $CFG, $USER, $DB, $PAGE, $COURSE;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::score_parameters(), [
                'jokeid' => $jokeid,
                'cmid' => $cmid,
                'value' => $value
            ]
        );
        // TODO : crear logica de puntuacion y suucees true y error "" si todo va bien.
        // TODO: manejar errores (usuario matriculado en curso, joke tiene que existrir, 1<=value<=5, error en DB).
        $succes = true;
        $error = '';
        //$cmid = $PAGE->cm->id;

        $context = context_module::instance($cmid);

        // CHECK user.
        $userid = $USER->id;
        $enrolled = is_enrolled($context, $userid, '', true);
        if (!$enrolled) {
            $succes = false;
            $error = 'User not enrolled in course';
        }
        // CHECK joke.
        if ($succes) {
            if (!jokeofday_joke::joke_exists($jokeid)) {
                $succes = false;
                $error = 'Joke does not exist: ' . $jokeid;
            }
        }
        // CHECK value.
        if ($succes) {
            if (0 <= $value && $value <= 5) {
                $record = new \stdClass();
                $record->joke_id = $jokeid;
                $record->userid = $userid;
                $record->cmid = $cmid;
                $record->course = $context->id;
                $record->value = $value;
                $record->timecreated = time();

                try {
                    $dbrecord = $DB->get_record('jokeofday_points',
                        array("userid" => $record->userid, 'joke_id' => $record->joke_id));
                    if (!$dbrecord) {
                        $DB->insert_record('jokeofday_points', $record);
                    } else {
                        $record->id = $dbrecord->id;
                        $DB->update_record('jokeofday_points', $record);
                    }
                } catch (Exception $e) {
                    $succes = false;
                    $error = 'Unaible to uptdate DB: ' . $e;
                }

            } else if ( $value == -1) {
                // Delete.
                $dbrecord = $DB->get_record('jokeofday_points',
                    array("userid" => $userid, 'joke_id' => $jokeid));
                if ($dbrecord) {
                    $DB->delete_records('jokeofday_points',
                        array('id' => $dbrecord->id));
                }
            }
        }

        return [
            'success' => $succes,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function score_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }
}