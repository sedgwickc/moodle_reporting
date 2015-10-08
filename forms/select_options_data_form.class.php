<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once(__DIR__."/../report.class.php");
require_once(__DIR__."/../dial_reports_lib.php");

class select_options_data_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $DB;

		$mform = &$this->_form; 

		$tables = report::$valid_tables;
		$all_cols = array();
		foreach( $tables as $table => $name){
			$all_cols = array_merge( $all_cols, get_columns( $table ) );
		}
		$ops = report::$operators;
		$base_table = $_SESSION['report_base'];
		$base_cols = get_columns( $base_table );

		$select_col = $mform->addElement('select', 'columns', get_string('select_col',
			'block_dial_reports'), $base_cols, array('size'=>count($base_cols)/2,
			'required'));
		$select_col->setMultiple(true);
		unset($tables[$base_table]);
		$table_options= array_prepend( $tables, '-', '-' );
		$tab_cols = array_prepend( $all_cols, '-', '-' );
		$ops = array_prepend( $ops, '-', '-' );

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

		$where_items = array();
		$where_items[] = $mform->createElement('select', 'where_col', 'Select
			column to filter on: ', $tab_cols);
		$where_items[] = $mform->createElement('select', 'where_op', 'Select
			filter operation: ', $ops);
		$where_items[] = $mform->createElement('text', 'where_filter', 'Enter
			a value or column to filter by: ');

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
