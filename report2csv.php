<?php
require_once(__DIR__.'/../../config.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=lms_report.csv');

$output = fopen('php://output', 'w');

if( isset($_SESSION['report_columns']) ){
	fputcsv($output, $_SESSION['report_columns']);
	if( isset($_SESSION['report_rows'] ) ){
		foreach( $_SESSION['report_rows'] as $row ) {
			fputcsv($output, $row);
		}
	}else{
		echo "Error: Rows not saved to session. ";
		die();
	}
} else {
	echo "Error: Columns not saved to session. ";
	die();
}
fclose($output);
