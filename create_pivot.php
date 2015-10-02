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
require_once('../../../config.php');
require_once('report.class.php');
require_once('report_create_pivot_form.class.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new
	moodle_url('/blocks/dial_rewards/info_pages/report_create_pivot.php'));
$title = 'Generate New Pivot Table';
$PAGE->set_heading($title);
$PAGE->set_title($title);
$mform = new report_form();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
	// Redircet user to dashbosrd page
	redirect(new moodle_url('/blocks/dial_rewards/reports/report_home.php'));
} elseif ( $mform->no_submit_button_pressed()){
	echo 'Fetching data ';
	if( $data = $mform->get_data() ){
		echo 'No Data! ';
	} else {
		echo 'Here is the data: ';
		print_r($data);
	}
	
} elseif ($data = $mform->get_data()) {
	//In this case you process validated data.
	//$mform->get_data() returns data posted in form.
	
		
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
    <meta name="rewards_info" content="Info on how to acheive dial rewards.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/normalize.css" rel="stylesheet" media="all">
    <link href="../css/styles.css" rel="stylesheet" media="all">
</head>
<body>
    <header id="header" role="banner">
    </header>
    <div id="content" class="wrap">
        <main role="main">
            <section>
                <article id="main_article">
                <?php
                $mform->display();

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
