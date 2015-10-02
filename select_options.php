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
require_once(__DIR__.'/../../config.php');
require_once('report.class.php');
require_once('forms/select_options_data_form.class.php');
require_once('forms/select_options_pivot_form.class.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new
	moodle_url('/blocks/dial_reports/select_options.php'));

if( isset( $_SESSION['report_type'] ) ){
	$type = $_SESSION['report_type'];
} else {
	echo 'No Type Selected!';
	redirect( new moodle_url('/my') );
}

if( empty($type) ){
	echo 'No Type Set!';
	redirect( new moodle_url('/my') );
}

switch($type){
	case 'data':
		$title = 'Select Options for Data Table';
		$mform = new select_options_data_form();
		break;
	case 'pivot':
		$title = 'Select Options for Pivot Table';
		$mform = new select_options_pivot_form();
		break;
	default:
		echo 'Type is Invalid!';
		redirect( new moodle_url('my') );
		break;
	}

$PAGE->set_heading($title);
$PAGE->set_title($title);

if ($mform->is_cancelled()) {
	// Redircet user to dashbosrd page
	redirect(new moodle_url('/blocks/dial_reports/reports/home.php'));
} elseif ($data = $mform->get_data()) {
	$report = new report();
	$report->set_columns($data->columns);
	$report->set_tables($_SESSION['report_tbls']);
	$report->set_order_by($data->order_by);
} else {
	// this branch is executed if the form is submitted but
	// the data doesn't validate and the form should be
	// redisplayed or on the first display of the form.
}

echo $OUTPUT->header();
?>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="reports_info" content="Info on how to acheive dial reports.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/normalize.css" rel="stylesheet" media="all">
    <link href="css/styles.css" rel="stylesheet" media="all">
</head>
<body>
    <header id="header" role="banner">
    </header>
    <div id="content" class="wrap">
        <main role="main">
            <section>
                <article id="main_article">
                <?php
                if( isset($report) ){
                	$report->render();
                }else{
                	echo "<p>Hold control to select multiple items</p>";
                	$mform->display();
                }
                 ?>
                </article>
            </section>
        </main>
    </div>
    <footer role="contentinfo">
        <?php echo $OUTPUT->footer(); ?>
    </footer>
</body>
</html>
