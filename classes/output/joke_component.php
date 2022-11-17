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
defined('MOODLE_INTERNAL') || die();

use dml_exception;
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
    public $resp;
    /**
     *  constructor.
     * @param $resp
     */
    public function __construct($resp) {
        $this->resp = $resp;
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
        return $data;
    }

}