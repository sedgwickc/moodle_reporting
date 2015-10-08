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
	
	public function pivot(){
	/*
DELIMITER //
DROP   PROCEDURE IF EXISTS Pivot //
CREATE PROCEDURE Pivot(
	IN tbl_name VARCHAR(99),       -- table name (or db.tbl)
	IN base_cols VARCHAR(99),      -- column(s) on the left, separated by
	commas
	IN pivot_col VARCHAR(64),      -- name of column to put across the
	top
	IN tally_col VARCHAR(64),      -- name of column to SUM up
	IN where_clause VARCHAR(99),   -- empty string or "WHERE
	..."
	IN order_by VARCHAR(99)        -- empty string or "ORDER
	BY ..."; usually the base_cols
)
DETERMINISTIC
SQL SECURITY INVOKER
BEGIN
	-- Find the distinct values
	-- Build the SUM()s
SET @subq = CONCAT('SELECT DISTINCT ', pivot_col, ' AS val',
	' FROM ',tbl_name, ' ', where_clause,' ORDER BY 1');
	--select @subq;

SET @cc1 = "CONCAT('SUM(IF(&p= ',
&v,
',
&t,
0))
AS
',
&v)";
SET
@cc2
=
REPLACE(@cc1,
'&p',
pivot_col);
SET
@cc3
=
REPLACE(@cc2,
'&t',
tally_col);
--
select
@cc2,
@cc3;
SET
@qval
=
CONCAT("'\"',
val,
'\"'");
--
select
@qval;
SET
@cc4
=
REPLACE(@cc3,
'&v',
@qval);
--
select
@cc4;

SET
SESSION
group_concat_max_len
=
10000;
--
just
in
case
SET
@stmt
=
CONCAT(
'SELECT
GROUP_CONCAT(',
@cc4,
'
SEPARATOR
",\n")
INTO
@sums',
'
FROM
(
',
@subq,
'
)
AS
top');
select
@stmt;
PREPARE
_sql
FROM
@stmt;
EXECUTE
_sql;
--
Intermediate
step:
build
SQL
for
columns
DEALLOCATE
PREPARE
_sql;
--
Construct
the
query
and
perform
it
SET
@stmt2
=
CONCAT(
'SELECT
',
base_cols,
',\n',
@sums,
',\n
SUM(',
tally_col,
')
AS
Total'
'\n
FROM
',
tbl_name,
'
',
where_clause,
'
GROUP
BY
',
base_cols,
'\n
WITH
ROLLUP',
'\n',
order_by
);
select
@stmt2;
--
The
statement
that
generates
the
result
PREPARE
_sql
FROM
@stmt2;
EXECUTE
_sql;
--
The
resulting
pivot
table
ouput
 DEALLOCATE
 PREPARE
 _sql;
     --
     For
     debugging
     /
     tweaking,
     SELECT
     the
     various
     @variables
     after
     CALLing.
     END;
     //
     DELIMITER
     ;
*/
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
				$row[] = date(dial_reports_lib::$date_format, $field);
				echo "<td>".date(dial_reports_lib::$date_format, $field)."</td>";
			} else {
				$row[] = round($field, 2);
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

	$tab_cols = array();
	$tab_cols_records = $DB->get_recordset_sql('describe {'.$table.'}');
	foreach( $tab_cols_records as $record ){
		if( !in_array($record->field, report::$remove_columns[$table] ) ){ 
			$tab_cols[$table.'.'.$record->field] = $table.'.'.$record->field;
		}
	}
	return $tab_cols;
}
