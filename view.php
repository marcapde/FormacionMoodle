<?php
/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     mod_jokeofday
 * @copyright   2022 Marc Marc@Capde.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_once('../../lib/filelib.php');

global $OUTPUT,$PAGE,$DB;
defined('MOODLE_INTERNAL') || die();


$id = required_param('id', PARAM_INT);    // Course Module ID
list($course, $cm) = get_course_and_cm_from_cmid($id);
$title = get_string('pluginname', 'jokeofday');
$pagetitle = $title;

$context = context_module::instance($id);
$PAGE->set_context($context);
$PAGE->set_url('/mod/jokeofday/view.php', ['id' => $id]);
$PAGE->set_cm($cm);
$PAGE->set_title($title);
$PAGE->set_heading($title);

require_login($course->id);

// Get request settings.
//$joke_config = $DB->get_record('mdl_jokeofday',array('id => 3'), $strictness=IGNORE_MISSING);
$joke_config = $DB->get_record_sql('SELECT * FROM mdl_jokeofday WHERE id = ?', [$id]);
echo"<pre>";
var_dump($joke_config);
die();


$curl = new curl();
$url = 'https://v2.jokeapi.dev/joke/Any';
$resp = $curl->get($url);
$resp = json_decode($resp, true);



//echo"<pre>";
//var_dump($resp);
//die();
echo $OUTPUT->header();

echo $OUTPUT->heading($pagetitle);

$templatecontext = (object)[
    'joke_setup' => $resp["setup"],
    'joke_delivery' => $resp["delivery"]
];
echo $OUTPUT->render_from_template('jokeofday/joke_view', $templatecontext);

//echo $resp["setup"];
//if ($resp["delivery"]){
//    echo $resp["delivery"];
//}
echo $OUTPUT->footer();
