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

use cm_info;

/**
 * jokeofday class
 *
 * @package    mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jokeofday {
    const TABLE="jokeofday";
    public static function get(cm_info $cm){
        global $DB;
        // Get request settings.
        $where=array(
            'id' => $cm->instance
        );
        return $DB->get_record(self::TABLE,$where, '*', MUST_EXIST);
    }
    public static function get_url($joke_config){

        $url = 'https://v2.jokeapi.dev/joke/';
        if($joke_config->categories==""){
            $url = $url . "Any";
        }else{
            $url = $url . $joke_config->categories;
        }
        if($joke_config->blacklist!=""){
            $url = $url . "?blacklistFlags=";
            $url = $url . $joke_config->blacklist;
        }
//        echo"<pre>";
//        var_dump($url);
//        die();
        return $url;

    }

}
