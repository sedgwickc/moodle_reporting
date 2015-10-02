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
require_once('../../config.php');
require_once('report.class.php');
require_once('forms/home_form.class.php');
include_once('chromephp/ChromePhp.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/blocks/dial_reports/home.php'));
$PAGE->set_title('Generate Reports');
$PAGE->set_heading('Generate Reports');
$mform = new home_form();
if( $mform->is_cancelled() ){
	redirect($CFG->wwwroot);
}elseif( $data = $mform->get_data() ) {
	if( $data->type === 'data'){
		$_SESSION['report_type'] = 'data';
		redirect( new moodle_url('/blocks/dial_reports/select_data.php'));
	}elseif( $data->type === 'pivot' ){
		$_SESSION['report_type'] = 'pivot';
		redirect( new moodle_url('/blocks/dial_reports/select_data.php'));
	}
}else{
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
                <h2>Welcome to Rewards Reporting</h2>
               	<p>
               	<?php
					$mform->display();
               	?>
               	</p>
                </article>
            </section>
        </main>
    </div>
    <footer role="contentinfo">
        <?php echo $OUTPUT->footer(); ?>
    </footer>
</body>
</html>
