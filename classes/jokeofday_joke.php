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
class jokeofday_joke
{
    const TABLE = "jokeofday_jokes";
    public static function get(cm_info $cm)
    {
        global $DB;
        // Get request settings.
        $where = array(
            'id' => $cm->instance
        );
        return $DB->get_record(self::TABLE, $where, '*', MUST_EXIST);
    }
    public static function joke_exists($jokeid){
        global $DB;
        // Get request settings.
        $where = array(
            'joke_id' => $jokeid
        );
        return $DB->get_record(self::TABLE, $where, '*', IGNORE_MISSING);
    }
    //update or insert
    public static function update_or_insert($joke){
        global $DB;
        // Check if it's alredy on the table
        $record = self::joke_exists($joke["id"]);
        $record ? $update=true : $update=false;

        $record->category = $joke["category"];
        $record->joke_id =  $joke["id"];
        $record->lang =     $joke["lang"];
        $record->type =     $joke["type"] ;
        $record->safe =     $joke["safe"];
        // $joke["joke"] ? $record->joke =$joke["joke"] : $record->joke =array('setup' => $joke["setup"],'delivery'=>$joke["delivery"]);
        (isset($joke["joke"])) ? $record->joke =$joke["joke"] : ($record->joke= $joke["setup"] . '//' . $joke["delivery"]);
        $record->flags = ""; // implode(',',$joke["flags"]);
        $allflags = ["nsfw","religious","political","racist","sexist","explicit"];
        for($i=0;$i<count($joke["flags"]);$i++){
            if ($joke["flags"][$allflags[$i]] != ""){
                $record->flags = $record->flags . $allflags[$i] . ",";
            }
        }
        if($record->flags != ""){
            $record->flags = substr($record->flags, 0, -1);
        }
//        echo"<pre>";
//        var_dump($record);
//        die();

        if($update){
            return $DB->update_record(self::TABLE, $record);
        }else{
            return $DB->insert_record(self::TABLE, $record);
        }
//        echo"<pre>";
//        var_dump($record);
//        die();
//        return false;
    }
}
