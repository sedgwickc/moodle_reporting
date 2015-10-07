<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once(__DIR__."/../report.class.php");

class select_options_data_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $DB;

		$mform = &$this->_form; 

		$tables = report::$valid_tables;
		$ops = report::$operators;
		$base_table = $_SESSION['report_base'];
		$tab_cols = array();
		foreach ( report::$valid_tables  as $table => $label ){
			$tab_cols_records = $DB->get_recordset_sql('describe {'.$table.'}');
			foreach( $tab_cols_records as $record ){
				if( !in_array($record->field, report::$remove_columns[$table] ) ){ 
					$tab_cols[$table.'.'.$record->field] = $table.'.'.$record->field;
				}
			}
		}	

		$select_col = $mform->addElement('select', 'columns', get_string('select_col',
			'block_dial_reports'), $tab_cols, array('size'=>count($tab_cols)/2,
			'required'));
		$select_col->setMultiple(true);
		unset($tables[$base_table]);
		$table_options= array_prepend( $tables, '-', '-' );
		$tab_cols = array_prepend( $tab_cols, '-', '-' );
		$ops = array_prepend( $ops, '-', '-' );

		$repeatno = count($tables);
		if( $repeatno >= 1 ){
			$num = 0;
			$groups = array();
			foreach( $tables as $table) {
				$join_items = array();
				//use hidden element to pass table to join on instead of select?
				$join_items[] =& $mform->createElement( 'static', 'join_sep',
					'<b>Add Join<b><br>', null);
				$join_items[] =& $mform->createElement( 'select', 'join_table', 
					'Select table to join data from: ', $table_options);
				$join_items[] =& $mform->createElement( 'select', 'join_col_1', 
					'Select column from above table to join on: ', $tab_cols);
				$join_items[] =& $mform->createElement( 'select', 'join_op', 
					'Select operator for join condition: ', $ops);
				$join_items[] =& $mform->createElement( 'select', 'join_col_2',
					'Select column from base table to join selected table on: ', $tab_cols);

				$groups[] =& $mform->createElement('group', 'join_'.$num, get_string('Add
					Join'), $join_items );
				$num++;
			}
			//$groups_group[] =& 
			$mform->addElement('group', 'joins', get_string('Add Join'), $groups);
		}
		
		$select_order = $mform->addElement('select', 'order_by', get_string('select_order_by',
			'block_dial_reports'), $tab_cols);

		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_reports'));
		//Default value...
	}
}

function array_prepend($array, $new_key = 0, $new_value){

	$old_keys = array_keys($array);
	$old_values = array_values( $array );
	array_unshift( $old_keys, $new_key );
	array_unshift( $old_values, $new_value);
	return array_combine($old_keys, $old_values);
}
