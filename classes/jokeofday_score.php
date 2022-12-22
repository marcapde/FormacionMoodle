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
 * jokeofday class
 *
 * @package    mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_jokeofday;

use cache;
use cm_info;
use coding_exception;
use curl;
use dml_exception;
use gradereport_singleview\local\ui\unique_value;
use stdClass;

/**
 * jokeofday class
 *
 * @package    mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jokeofday_score {
    const TABLE = "jokeofday_points";

    /**
     * @param integer $jokeid
     * @return false|mixed|stdClass
     * @throws dml_exception
     */
    public static function get_selected ($jokeid) {
        global $USER, $DB;
        return $DB->get_record(self::TABLE,
            array('userid' => $USER->id, 'joke_id' => $jokeid));
    }
    public static function get_all_jokeids_from_cm ($cmid) {
        global $DB;
        return self::remove_duplicates($DB->get_records(self::TABLE, array("cmid" => $cmid )));
    }
    private static function remove_duplicates ($records) {
        $res = array();
        foreach ($records as $r) {
            if (!in_array($r->joke_id, $records)) {
                array_push($res, $r->joke_id);
            }
        }

        return $res;
    }

    public static function get_mean ($jokeid) {
        global $DB;
        $records = $DB->get_records(self::TABLE, array('joke_id' => $jokeid));
        $sum = 0;
        $count = 0;
        foreach ($records as $r) {
            $sum += $r->value;
            $count += 1;
        }
        return ($sum / $count);
    }

}
