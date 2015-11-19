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
require_once(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../dial_reports_lib.php');
require_once(__DIR__.'/dept_sum.class.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('popup');
$PAGE->set_url(new moodle_url('/blocks/dial_reports/reports/premade/dept_summary.php'));
$PAGE->set_title('Rewards Summary');
$PAGE->set_heading('Rewards Summary by Department');
echo $OUTPUT->header();

$dept_report = new report('dept_hours');
$dept_report->create_dept_report();
?>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="reports_summary" content="Summary of reports by dept.">
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
				if( empty($dept_report) ){
					echo "An error occurred generating the summary.";
				} else {
					$dept_report->render();
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
