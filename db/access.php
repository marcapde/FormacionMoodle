<?php
/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     mod_jokeofday
 * @copyright   2022 Marc Marc@Capde.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//  Possible capabilities: changecategories, changeblacklist, changelanguage, or makeSoft.
$capabilities = array(
    'mod/jokeofday:addinstance' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),


);