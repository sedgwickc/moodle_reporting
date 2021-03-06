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
require_once(__DIR__.'/../../../../config.php');
require_once(__DIR__.'/../../dial_rewards_lib.php');
require_once(__DIR__.'/../report_lib.php');
require_once(__DIR__.'/dept_sum.class.php');
require_once(__DIR__.'/../Pivot.php');
require_once('chromephp/ChromePhp.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('popup');
$PAGE->set_url(new moodle_url('/blocks/dial_rewards/reports/premade/dept_summary.php'));
$PAGE->set_title('Rewards Summary');
$PAGE->set_heading('Rewards Summary by Department');
echo $OUTPUT->header();

$libobj = new report_lib();
try {
$dept_summaries = $libobj->dial_dept_summary();
} catch( Exception $e ) {
	echo "Exception: ",$e->getMessage(),"/n";
}

$recordset = $libobj->retrieve_completed_courses_all();

?>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="rewards_summary" content="Summary of rewards by dept.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/normalize.css" rel="stylesheet" media="all">
    <link href="../../css/styles.css" rel="stylesheet" media="all">
</head>
<body>
    <header id="header" role="banner">
    </header>
    <div id="content" class="wrap">
        <main role="main">
            <section>
                <article id="main_article">
                <?php
                $headers = array('id',
                	'userid',
                	'department',
                	'courseid',
                	'fullname',
                	'category',
                	'category name',
                	'timeenrolled',
                	'timestarted',
                	'timecompleted'
                	);

				echo"<h2>original data</h2>";
				simpleHtmlTable($recordset, $headers);

				echo "<h2>pivot on userid</h2>";
				$data = Pivot::factory($recordset)
					->pivotOn(array('userid'))
					->addColumn(array('category name'), array('timecompleted'))
					->fetch();
				if( empty($data) ){
					echo"Error: Pivot table not returned!";
				} else {
					simpleHtmlTable($data,array('userid','fullname', 'department', 'timecompleted'));
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
