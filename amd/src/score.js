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
 * @package
 * @author  2022 3iPunt <https://www.tresipunt.com/>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-disable no-unused-vars */
/* eslint-disable no-console */

define([
        'jquery',
        'core/str',
        'core/ajax',
        'core/templates'
    ], function ($, Str, Ajax, Templates) {
        "use strict";

        /**
         *
         */
        let ACTION = {
            SCORE_SELECT: '[data-action="score"]',
        };

        let TEXT = {
            COMMENT_TEXT: '[data-text="comment"]',
        };

        /**
         *
         */
        let SERVICES = {
            JOKE_SCORE: 'mod_jokeofday_score'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} jokeid
         * @param {Number} cmid
         */
        function Score(region, jokeid, cmid) {
            this.node = $(region);
            this.node.find(ACTION.SCORE_SELECT).on('change', this.onScoreSelectChange.bind(this));
            this.cmid = cmid;
            this.jokeid = jokeid;
        }

        Score.prototype.onScoreSelectChange = function (e) {
            //this.node.find(ACTION.GROUP_SELECT).val(0);
            // TODO valores de las variables con jquery.

            // GET  value.
            let val = document.getElementById("selectvalue");
            val = val.options[val.selectedIndex].value;
            // if val != none else borrar registro
            var request = {
                methodname: SERVICES.JOKE_SCORE,
                args: {
                    jokeid: this.jokeid,
                    cmid: this.cmid,
                    value: val,
                }
            };
            console.log(request);
            Ajax.call([request])[0].done(function(response) {
                // TODO ahora que? recargar pagina.
                console.log(response);
                //location.reload();
            }).fail((error) => { console.log(error);});
        };
    // Notification.exception
        /** @type {jQuery} The jQuery node for the region. */
        Score.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} jokeid
             * @param {Number} cmid
             * @return {Score}
             */
            initScore: function (region, jokeid, cmid) {
                return new Score(region, jokeid, cmid);
            }
        };
    }
);
