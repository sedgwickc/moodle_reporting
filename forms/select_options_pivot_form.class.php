<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once("report.class.php");

class select_options_pivot_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $DB;
		$mform = &$this->_form;

		// grab columns from selected tables

		$tables = $_SESSION['report_tbls'];
		$tab_cols = array();
		foreach (
			$tables as
			$table ){
			$tab_cols_records =	$DB->get_recordset_sql('describe {'.$table.'}');
			foreach( $tab_cols_records as $record ){
				$tab_cols[] = $record->field;
            }
        }

		$col_select = $mform->addElement('select', 'tables', get_string('col_select',
			'block_dial_reports'), $tab_cols);
		$col_select->setMultiple(true);
		$col_select->setSelected('users');

		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_reports'));
		//Default value...
	}

}
