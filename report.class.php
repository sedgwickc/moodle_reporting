<?php

/* Charles Sedgwick
 * report.class.php
 */
require_once(__DIR__.'/../../config.php');
require_once('Pivot.php');
require_once('dial_reports_lib.php');
require_once('common_reports/dept_sum.class.php');

class report {

	private $title;
	private $type;
	private $columns;
	private $rows;
	private $values;
	private $calcs;
	private $order_by;
	private $joins;
	private $where_clauses;
	private $record_set;
	private $tables;
	private $fields;
	private $query;
	private $categories;
	private $dept_sums;
	
	public static $report_types = array( 
		'pivot'=>'Pivot', 
		'data'=>'Data' );
	public static $remove_columns = array( 
		'user' => array('idnumber',
			'calendartype',
			'secret',
			'trustbitmask',
			'password', 
			'mnethostid', 
			'theme'),
		'course' => array('idnumber', 
			'sortorder',
			'groupmodeforce',
			'cacherev',
			'defaultgroupingid',
			'theme',
			'maxbytes', 
			'marker', 
			'legacyfiles'),
		'course_completions' => array('id', 
			'reaggregate')
		);

	public static $valid_tables	= array(
		'user' => 'Users', 
		'course' => 'Courses',
		'course_completions' => 'Course Completions'
		);
		
	public static $operators = array('<' => 'less than', 
		 '>' => 'greater than' ,
		'=' => 'equals',
		'!=' => 'not equal'
		);

	function __CONSTRUCT(){
		global $DB;
		$category_records = $DB->get_recordset_sql("select id, name from {course_categories}");
	 	$this->categories = array();

	 	foreach ( $category_records as $category )
	 	{
	    	$this->categories[$category->id] = strtolower($category->name);
	    }

	    $category_records->close();

	}

	public function get_data(){

		global $DB;

		if( empty($this->columns) ){
			echo "->get_data(): Columns no set!";
			return null;
		}

		if( isset( $this->query ) ){
			$this->record_set = $DB->get_recordset_sql($this->query);
		} else {

			$this->query = 'select ';
			end($this->columns);
			$last_key = key($this->columns);
			foreach( $this->columns as $key => $col ){
				$this->query .= $col;
				if( $key == $last_key ){
					$this->query .= ' ';
				} else {
					$this->query .= ', ';
				}
			}

			$this->query .= ' from {'.$this->base_table.'} '.$this->base_table;

			// Find way to detect the value typ ethat matches the values in a
			// column
			// Make a map of known numerical and string fields?
			if( isset($this->where_clauses) ){
				$this->query .= ' where ';
				$last_key = key(end($this->where_clauses));
				foreach( $this->where_clauses as $key => $clause ){
					$this->query .= $clause[0].' '.$clause[1].' "'.$clause[2].'" ';
					if( $key != $last_key ){
						$this->query .= ' and ';
					}
				}
			}
			// add ability to create calc columns based on selected columns

			if( isset( $this->joins ) ){
				foreach( $this->joins as $join ){
					if( $join['join_table'] != '-' ){
						$this->query .= ' inner join {'.$join['join_table'].'} '.
							$join['join_table'].' on '.$join['join_col_1'];
						$this->query .= ' '.$join['join_op'].'
							'.$join['join_col_2'];
					}
				}
			}
			
			if( isset($this->order_by) ){
				$this->query .= ' order by '.$this->order_by.' desc';
			}
			print_r( 'QUERY{ '.$this->query.' }' );
			$this->record_set = $DB->get_recordset_sql($this->query);
		}
	}

	public function create_dept_report(){
		global $DB;
		//old dept_summary logic: use to parse report object data to create dept_sum
		//objects
		$this->columns = array('Department', 'Skill', 'Hours', 'Sum');
		$this->tables = array('course_completions','course','course_categories');
		
		$this->record_set = $DB->get_recordset_sql(
			"select cc.id, cc.userid, u.department, cc.course, c.fullname, c.category, 
			cd.name, cc.timeenrolled, cc.timestarted, cc.timecompleted from 
			{course_completions} cc inner join {user} u on cc.userid = u.id inner join 
			{course} c on cc.course = c.id inner join {course_categories} cd on 
			c.category = cd.id");

		if( !$this->record_set->valid() ){
			echo "Create_dept_report(): recordset not valid";
			return null;
		}

		$depts = dial_reports_lib::$departments;
		$dept_summaries = array();
		foreach ($depts as $id => $dept) {
	    	$dept_summary = new dept_sum();
			$dept_summary->set_dept($dept);
			$dept_summary->init_categories($this->categories);
			if(empty($dept_summaries)){
				$dept_summaries = array($id=>$dept_summary);
			}
			else
			{
				$dept_summaries[$id] = $dept_summary;
	    	}
		}
		foreach($this->record_set as $course) {
			if(array_key_exists($course->department,$dept_summaries) 
				&& $course->timecompleted !== null){
	        	/* in department summaries, create an array that stores
	         	 * cat_id=>total_hours then increment the value for each
	         	 * reocrd. When printing the category, convert category
	         	 * to name.
	         	 */
	         	 $minutes = time_spent($course->timestarted,
	         	 	$course->timecompleted);
	         	 $dept_summaries[$course->department]->increment_category($course->category,
	         	 	$minutes);
	   	   }
   	   }

   	   $this->dept_sums = $dept_summaries;
	}


	public function render(){

		if( isset( $this->dept_sums ) ){
			//dept summary render code
			echo '<table>';
            echo '<tr>';
            echo '<th>Department</th>';
            echo '<th>Skill</th>';
            echo '<th>Hours (Mins)</th>';
            echo '<th>Sum (Mins)</th>';
            echo '</tr>';
            foreach( $this->dept_sums as $summary ) {
        		$categories = $summary->get_categories();
        		$cat_count = count($categories);
        		// set id to key of first
        		// element
        		$id = current(array_keys($categories));
        		echo '<tr>';
        		echo '<td rowspan="'.$cat_count.'">'.$summary->get_dept().'</td>';
        		echo'<td>'.$categories[$id].'</td><td>'.
        			round($summary->get_category_hours($id), 2).'</td>';
        		echo '<td rowspan="'.$cat_count.'">'.
        			round($summary->get_dept_total(), 2).'</td>';
        		echo '</tr>';
        		foreach( $categories as $cat_id=>$cat_name ){
                	if( $cat_id != $id ){
                        echo '<tr><td>'.$cat_name.'</td><td>'.
                        	round($summary->get_category_hours($cat_id), 2).'</td></tr>';
                    }
                }
            }
			echo '</table>';
			return;
		}

		if( !isset($this->record_set) ){
			$this->get_data();
		}

		$this->rows = simpleHtmlTable($this->record_set, $this->columns);

		$this->record_set->close();
	}

	public function csv_link(){
		$_SESSION['report_columns'] = $this->columns;
		$_SESSION['report_rows'] = $this->rows;
		echo "<a href= 'report2csv.php'>Download report as CSV file</a>";
	}

	public function set_title( $new_title ){
		$this->title = $new_title;
	}

	// $new_columns should be an array of the names of the selected fields 
	public function set_columns( $new_columns ){
		$this->columns = $new_columns;
	}

	// new_tables should be an array containing names of table sto be queried
	public function set_base( $new_base ){
		$this->base_table = $new_base;
	}

	// new_fields should be an array containing the fields to be selected 
	public function set_fields ( $new_fields ){

	}

	// $new_calcs should be an array containing key that are columns/rows and values
	// that are the calc to be done on the column
	public function set_calcs ( $new_calcs ){
		$this->calcs = $new_calcs;
	}

	public function set_joins( $new_join ){
		$this->joins = array_values($new_join);
	}

	public function add_where_clause( $new_where ){
		$this->where_clauses[] = $new_where;
	}

	public function set_order_by ( $new_order ){
		if( $new_order != '-' ){
			$this->order_by = $new_order;
		}
	}

	public function set_query( $new_query ){
		$this->query = $new_query;
	}

	/* retrieves records based on the options selected by the user */
	public function populate_recordset(){

	}

	public function get_recordset(){
	
	}

	public function get_where_clauses(){
		return $this->where_clauses;
	}

	public function get_joins(){
		return $this->joins;
	}

	public function get_columns(){
		return $this->columns;
	}

}
