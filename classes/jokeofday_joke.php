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
use stdClass;

/**
 * jokeofday class
 *
 * @package    mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jokeofday_joke {
    const TABLE = "jokeofday_jokes";

    /**
     * @param int $timecreated time when a certain joke was saved in cache
     * @return bool true if expired | false if not
     * @throws \dml_exception
     */
    public static function has_expired($timecreated) {
        $maxtime = get_config('jokeofday', 'maxtime');
        return time() - (($timecreated)) > $maxtime;
    }

    /**
     * request to the JokeAPI.
     *
     * @param $jokeconfig
     * @param $cache
     * @param string $key
     * @return mixed
     * @throws dml_exception
     */
    public static function request($jokeconfig, $cache, $key) {
        $curl = new curl();
        $url = jokeofday::get_url($jokeconfig);
        $resp = $curl->get($url);
        $resp = json_decode($resp, true);
        self::update_or_insert($resp);
        $savecache = $resp;
        $savecache["timecreated"] = time();
        $cache->set($key, $savecache);
        return $resp;
    }

    /**
     * @param object $jokeconfig settings for the call to the API.
     * @return array|bool|float|int|mixed|\stdClass|string
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_joke($jokeconfig) {
        global $USER;

        $cache = cache::make('mod_jokeofday', 'jokesdata');

        $key = 'joke_' . $USER->id;
        $data = $cache->get($key);

        if (!$data) {
            $resp = self::request($jokeconfig, $cache, $key);
        } else {
            if (self::has_expired($data["timecreated"])) {
                $cache->delete($key);
                $resp = self::request($jokeconfig, $cache, $key);
            } else {
                $resp = $data;
            }
        }
        return $resp;
    }

    /**
     * @param int $jokeid
     * @return false|mixed|stdClass
     * @throws dml_exception
     */
    public static function joke_exists($jokeid) {
        global $DB;
        // Get request settings.
        $where = array(
            'joke_id' => $jokeid
        );
        return $DB->get_record(self::TABLE, $where, '*', IGNORE_MISSING);
    }
    // Update or insert.

    /**
     * @param object $joke
     * @return bool|int
     * @throws \dml_exception
     */
    public static function update_or_insert($joke) {
        global $DB;
        // Check if it's alredy on the table.
        $record = self::joke_exists($joke["id"]);
        $record ? $update = true : $update = false;

        $record->category = $joke["category"];
        $record->joke_id = $joke["id"];
        $record->lang = $joke["lang"];
        $record->type = $joke["type"];
        $record->safe = $joke["safe"];
        (isset($joke["joke"])) ? $record->joke = $joke["joke"] : ($record->joke = $joke["setup"] . '//' . $joke["delivery"]);
        $record->flags = "";
        $allflags = ["nsfw", "religious", "political", "racist", "sexist", "explicit"];
        for ($i = 0; $i < count($joke["flags"]); $i++) {
            if ($joke["flags"][$allflags[$i]] != "") {
                $record->flags = $record->flags . $allflags[$i] . ",";
            }
        }
        if ($record->flags != "") {
            $record->flags = substr($record->flags, 0, -1);
        }
        if ($update) {
            return $DB->update_record(self::TABLE, $record);
        } else {
            return $DB->insert_record(self::TABLE, $record);
        }
    }
}
