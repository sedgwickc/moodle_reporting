<?php
require_once(__DIR__.'/../../config.php');
require_once('common_reports/dept_sum.class.php');
require_once(__DIR__.'/report.class.php');
include_once 'chromephp/ChromePhp.php';

class dial_reports_lib {

    private $categories;
	public static $departments = array("1" => "Accounting",
		"2" => "CEO's office",
		"3" => "Continuous Improvement",
		"4" => "Customer Survey",
		"5" => "Door Line",
		"6" => "Glass Line",
		"7" => "GM's Office",
		"8" => "HR",
		"9" => "HSE",
		"10" =>	"Hybrid	Line",
		"11" =>	"Information",
		"12" =>	"Order Desk",
		"13" =>	"Installation",
		"14" =>	"IT",
		"15" =>	"Machining",
		"16" =>	"Maintenance",
		"17" =>	"Marketing",
		"18" =>	"Materials",
		"19" =>	"Ops Manager's Office",
		"20" => "President's office",
		"21" =>	"PVC",
		"22" => "Quality",
		"23" => "R&D",
		"24" => "Sales",
		"25" => "Scheduling",
		"26" => "Security",
		"27" => "Service",
		"28" => "Shiping",
		"29" => "Training and Development",
		"30" => "Vivace"
		);
	public static $date_format = "Y-m-d H:i:s";
	
	function __CONSTRUCT(){
		global $DB;

		$category_records = $DB->get_recordset_sql("select id,name from {course_categories}");
		$this->categories = array();

		foreach ( $category_records as $category ) {
			$this->categories[$category->id] = strtolower($category->name);
		}

	}

    public function get_categories(){
   		return $this->categories;
   	}

	public function get_category_count(){
		return count($this->categories);
	}

	public function close_records_categories(){
		if($this->categories->valid() ){
			$this->categories->close();
		}
	}

	public function get_dial_lib_obj(){
		return $this->libobj;
	}
}

$averageCbk = function($reg)
{
    return round($reg['clicks']/$reg['users'],2);
};

/* @param: 
 * $start: time a course was started in seconds from unix epoch
 * $finish: time  course was scompleted in seconds from unix epoch
 * @return: minutes it took to complete the course
 */
function time_spent($start, $finished){
	if ( $finished < $start ){
		return null;
	}

	return ($finished-$start)/60;
}

function simpleHtmlTable( $data, $headers )
{
	if( !$data->valid() || empty($headers) ){
		return null;
	}
	$rows = array();
	$keys = array();
	echo "<table border='1'>";
	echo "<thead>";
	foreach ($headers as $item) {
		echo "<td><b>{$item}<b></td>";
	}
	echo "</thead>";
	foreach	($data as $key_r => $record) {
		echo "<tr>";
		$row = array();
		foreach ($record as $key => $field) {
			$keys[]=$key;	//breaks timezone
			if( strpos($key, 'time') !== false 
				|| strpos($key, 'date') !==	false){
				$datum = date(dial_reports_lib::$date_format, $field);
				$row[] = $datum;
				echo "<td>".$datum."</td>";
			} elseif( is_string($field) ){
				$row[] = $field;
				echo "<td>".$field."</td>";
			}elseif ( is_float($field) ){
				$datum = round( $field, 2 );
				$row[] = $datum;
				echo "<td>".$datum."</td>";
			}else{
				$row[] = $field;
				echo "<td>".$field."</td>";
			}
		}
		$rows[] = $row;
		echo "</tr>";
	}
	echo "</table>";
	return $rows;
}

function get_columns( $table ){
	global $DB;

	ChromePhp::log('get_columns(): Table->'.$table);

	if( empty($table) ){
		echo "get_columns(): Table not valid. ";
		return null;
	}
	$table_columns = array($table => array());
	$columns = array();
	$tab_cols_records = $DB->get_recordset_sql('describe {'.$table.'}');
	foreach( $tab_cols_records as $record ){
		if( !in_array($record->field, report::$remove_columns[$table] ) ){ 
			$table_columns[$table][$table.'.'.$record->field] = $record->field;
		}
	}
	return $table_columns;
}
