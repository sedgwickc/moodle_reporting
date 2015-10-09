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
				break;
			case 'custom_course':
				$table_columns = get_columns(report::$valid_tables['course']);
				$table_columns = array_merge( $table_columns, get_columns(
					'course_categories'));
				break;
			case 'custom_completions':
				$tables = report::$valid_tables;
				$table_columns = array();
				foreach( $tables as $table => $name){
					$table_columns = array_merge( $table_columns, get_columns( $table ) );
				}
				break;
			default:
				break;
		}

		$ops = report::$operators;
		/* refactor below code to output columns based on new $table_columns
		 * structure. 
		 */

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
		$ops = array_prepend( $ops, '-', '-' );
/*
		$repeatno = count($tables);
		if( $repeatno >= 1 ){
			$num = 0;
			$groups = array();
			
			foreach( $tables as $table) {
				$join_items = array();
				//use hidden element to pass table to join on instead of select?
				$join_items[] =& $mform->createElement( 'select', 'join_table', 
					'Select new table to join data from: ', $table_options);
				$join_items[] =& $mform->createElement( 'static', 'join_break',
					'', '<br>');
				$join_items[] =& $mform->createElement( 'select', 'join_col_1', 
					'Select column from new table to join on: ', $tab_cols);
				$join_items[] =& $mform->createElement( 'static', 'join_break',
					'', '<br>');
				$join_items[] =& $mform->createElement( 'select', 'join_op', 
					'Select condition operator: ', $ops);
				$join_items[] =& $mform->createElement( 'static', 'join_break',
					'', '<br>');
				$join_items[] =& $mform->createElement( 'select', 'join_col_2',
					'Select base table column to join new table on: ', $tab_cols);

				$groups[] =& $mform->createElement('group', 'join_'.$num,
					get_string('add_join', 'block_dial_reports'), $join_items );
				$num++;
			}
			$mform->addElement('group', 'joins', get_string('Add Join'), $groups);
		}
*/
		$where_items = array();
		$where_items[] = $mform->createElement('select', 'where_col', 'Select
			column to filter on: ', $tab_cols);
		$where_items[] = $mform->createElement('select', 'where_op', 'Select
			filter operation: ', $ops);
		$where_items[] = $mform->createElement('text', 'where_filter', 'Enter
			a value by: ');
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
