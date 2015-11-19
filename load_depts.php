<?php
require_once('../../config.php');

global $DB;
echo "Deleting current Records...<br>";
$DB->delete_records("block_dial_reports_depts");

echo "Inserting new dept records...<br>";
$dept_records = array();
$known_depts = array("1" => "Accounting",
        "2" => "CEO's office",
        "3" => "Continuous Improvement",
        "4" => "Customer Survey",
        "5" => "Door Line",
        "6" => "Glass Line",
        "7" => "GM's Office",
        "8" => "HR",
        "9" => "HSE",
        "10" => "Hybrid Line",
        "11" => "Information",
        "12" => "Order Desk",
        "13" => "Installation",
        "14" => "IT",
        "15" => "Machining",
        "16" => "Maintenance",
        "17" => "Marketing",
        "18" => "Materials",
        "19" => "Ops Manager's Office",
        "20" => "President's office",
        "21" => "PVC",
        "22" => "Quality",
        "23" => "R&D",
        "24" => "Sales",
        "25" => "Scheduling",
        "26" => "Security",
        "27" => "Service",
        "28" => "Shipping",
        "29" => "Training and Development",
        "30" => "Vivace" );

foreach( $known_depts as $dept )
{
	$record = new stdClass();
	$record->dept_name = $dept;
	$dept_records[] = $record;
}
$DB->insert_records("block_dial_reports_depts",$dept_records);

echo "printing new position records<br>";
$dept_records = $DB->get_records_sql("select * from
	{block_dial_reports_depts}");

echo "<ul>";
foreach( $dept_records as $dept )
{
	echo "<li>".$dept->id.", ".$dept->dept_name."</li>";
}
echo "</ul>";

?>
