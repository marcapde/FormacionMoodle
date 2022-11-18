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
 * Add jokeofday form
 *
 * @package mod_jokeofday
 * @copyright  2022 3ipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_jokeofday_mod_form extends moodleform_mod {

    public function definition() {
        global $PAGE;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Add categories.
        $options = array(
            'Programming' => 'Programming',
            'Miscellaneous' => 'Miscellaneous',
            'Dark' => 'Dark',
            'Pun' => 'Pun',
            'Spooky' => 'Spooky',
            'Christmas' => 'Christmas'
        );
        $mform->addElement('select', 'categories', get_string('categories', 'jokeofday'), $options);
        $mform->getElement('categories')->setMultiple(true);
        // Add blacklist.
        $mform->addElement('text', 'blacklist', get_string('blacklist', 'jokeofday'));
        $mform->setType('blacklist', PARAM_TEXT);
        // Add numjokes.
        $mform->addElement('text', 'numjokes', get_string('numjokes', 'jokeofday'));
        $mform->setType('numjokes', PARAM_TEXT);
        $mform->addRule('numjokes', null, 'required', null, 'client');
        $this->standard_coursemodule_elements();

        // Buttons.
        $this->add_action_buttons(true, false, null);

    }

}
