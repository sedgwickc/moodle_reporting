<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once("report.class.php");

class select_options_data_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $DB;
		$mform = &$this->_form; 

		$tables = $_SESSION['report_tbls'];
		$tab_cols = array();
		foreach ( $tables as $table ){
			$tab_cols_records = $DB->get_recordset_sql('describe {'.$table.'}');
			foreach( $tab_cols_records as $record ){
				if( !in_array($record->field, report::$remove_columns[$table] ) ){ 
					$tab_cols[$table.'.'.$record->field] = $table.'.'.$record->field;
				}
			}
		}	

		$select_col = $mform->addElement('select', 'columns', get_string('select_col',
			'block_dial_reports'), $tab_cols, array('size'=>count($tab_cols)/2));
		$select_col->setMultiple(true);

		
		$select_order = $mform->addElement('select', 'order_by', get_string('select_order_by',
			'block_dial_reports'), $tab_cols);

		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_reports'));
		//Default value...
	}

}
