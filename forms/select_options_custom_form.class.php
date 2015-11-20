<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once(__DIR__."/../report.class.php");
require_once(__DIR__."/../dial_reports_lib.php");

class select_options_custom_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $DB;

		$mform = &$this->_form; 

		switch( $_SESSION['report_type'] ){
			case 'custom_user':
				$table_columns = get_columns(report::$valid_tables['user']);
				$table_columns['user']['user.position'] = 'Position';
				$calc_type = array('total' =>'Total number of users', 
					'dept' => 'Total number of users per department', 
					'city' => 'Total number of users per city',
					'min_dept' => 'Department with fewest users');
				break;
			case 'custom_course':
				$table_columns = get_columns(report::$valid_tables['course']);
				$table_columns = array_merge( $table_columns, get_columns(
					'course_categories'));
				$calc_type = array('total' =>'Total number of courses', 
					'min_num' => 'Category with fewest courses',
					'max_num' => 'Category with the most courses');
				break;
			case 'custom_completions':
				$table_columns = get_columns(report::$valid_tables['course']);
				$table_columns = array_merge($table_columns,
					get_columns(report::$valid_tables['user']));
				$table_columns = array_merge( $table_columns,
					get_columns(report::$valid_tables['course_completions']));
				$table_columns['course_completions']['course_completions.total_minutes']
					= 'training time (Minutes)';
				$table_columns['user']['user.position'] = 'Position';
				$calc_type = array('total' =>'Total Sum of training minutes', 
					'category' => 'Sum of training minutes per category', 
					'user' => 'Sum of training minutes per user', 
					'min_time' => 'shortest completion time',
					'max_time' => 'Longest completion time');
				break;
			default:
				break;
		}
		$calc_type = array_prepend( $calc_type, '-', '-');
		// functionality: user selects columns to include -> generator joins
		// tables as necessary. Conditions are implied based on the tables
		// involved (i.e. User.first name, course_completions.* -> join on
		// userid)
		$all_cols = array();
		foreach( $table_columns as $table => $columns ){
			$count = count($columns);
			if( $count > 10 ){
				$count = $count / 2;
			}
			asort($columns);

			$select_col = $mform->addElement('select', $table.'_columns',
				'Select '.$table.' columns:', $columns, array('size'=>$count));
			$select_col->setMultiple(true);
			$all_cols = array_merge( $all_cols, $columns );
		}
		$tab_cols = array_prepend( $all_cols, '-', '-' );
		$ops = report::$operators;
		$ops = array_prepend( $ops, '-', '-' );
		$calcs = report::$calculations;
		$calcs = array_prepend( $calcs, '-', '-' );
		
		$calc_items = array();
		$calc_items[] = $mform->createElement('select', 'calc_type', 'Select
			Calculation: ', $calc_type);
		$calcnum = 1;

		$calcoptions = array();
		$calcoptions[] = array();
		$calcoptions[] = array();
		$calcoptions[] = array();

		$this->repeat_elements($calc_items, $calcnum, $calcoptions,
			'calc_num', 'calc_add', $calcnum, 'Add Calculation');

		$where_items = array();
		$where_items[] = $mform->createElement('select', 'where_col', 'Select
			column to filter on: ', $tab_cols);
		$where_items[] = $mform->createElement('select', 'where_op', 'Select
			filter operation: ', $ops);
		$where_items[] = $mform->createElement('text', 'where_filter', 'Enter
			a value by: ');
		$mform->setType('where_filter', PARAM_NOTAGS);
		$where_items[] =& $mform->createElement( 'static', 'where_example',
			'', '<b>e.g. DD-MM-YYYY, trainee3, Business Writing: Email<b><br>');

		$wherenum = 1;

		$whereoptions = array();
		$whereoptions[] = array();
		$whereoptions[] = array();
		$whereoptions[] = array();

		$this->repeat_elements($where_items, $wherenum, $whereoptions,
			'where_num', 'where_add', $wherenum, 'Add Filter');

		$select_order = $mform->addElement('select', 'order_by', get_string('select_order_by',
			'block_dial_reports'), $tab_cols);

		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_reports'));
	}
}

function array_prepend($array, $new_key = 0, $new_value){

	$old_keys = array_keys($array);
	$old_values = array_values( $array );
	array_unshift( $old_keys, $new_key );
	array_unshift( $old_values, $new_value);
	return array_combine($old_keys, $old_values);
}
