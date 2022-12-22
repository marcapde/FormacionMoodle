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
 * icon_component
 *
 * @package     mod_jokeofday
 * @copyright   2021 Tresipunt <moodle@tresipunt.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_jokeofday\output;

use cm_info;
use dml_exception;
use mod_jokeofday\jokeofday;
use mod_jokeofday\jokeofday_joke;
use mod_jokeofday\jokeofday_score;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * icon_component
 *
 * @package     mod_jokeofday
 * @copyright   2021 Tresipunt <moodle@tresipunt.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class joke_component implements renderable, templatable {
    /** @var array|bool|float|int|mixed|stdClass|string response */
    protected $resp;
    /** @var false|mixed|stdClass joke config */
    protected $jokeconfig;
    /** @var cm_info  course module*/
    protected $cm;
    /**
     *  constructor.
     * @param $cm
     */
    public function __construct(cm_info $cm) {
        $this->jokeconfig = jokeofday::get($cm);
        $this->resp = jokeofday_joke::get_joke($this->jokeconfig);
        $this->cm = $cm;
    }

    /**
     * Export for Template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        if (isset($this->resp["delivery"])) {
            $data->joke_delivery = $this->resp["delivery"];
            $data->joke_setup = $this->resp["setup"];
        } else {
            $data->joke = $this->resp["joke"];
        }
        $data->jokeid = $this->resp["id"];
        $data->cmid = $this->cm->id;
        $selected = jokeofday_score::get_selected($data->jokeid);

        $arr = array("", "", "", "", "", "", "");
        $selected ? $arr[$selected->value + 1] = "selected" : $arr[0] = "selected";
        $data->options = '';

        for ($i = 0; $i < count($arr); $i++) {
            $data->options .= '<option ' . $arr[$i] . ' value="' . ($i - 1) . '">';
            $i == 0 ? $data->options .= 'Score...' : $data->options .= ($i - 1);
            $data->options .= '</option>';
        }
        // Display all scores.
        $allrecords = jokeofday_score::get_all_jokeids_from_cm($this->cm->id);
        $data->scores = array();
        foreach ($allrecords as $r) {
            $rdata = jokeofday_joke::get_joke_from_id($r);

            $newarray = array ('joke2' => $rdata->joke, 'score' => jokeofday_score::get_mean($r));
            array_push($data->scores, $newarray);

        }
        return $data;
    }

}

