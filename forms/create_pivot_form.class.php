<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once("../../../config.php");
require_once("report.class.php");

class report_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $CFG;

		$tables = array('users'=>'users', 'courses'=>'courses', 'completions'=>'completions');

		$mform = &$this->_form; 
		$table_select = $mform->addElement('select', 'tables', get_string('table_select',
			'block_dial_rewards'), $tables);
		$table_select->setMultiple(true);
		$table_select->setSelected('users');

		$mform->addElement( 'submit', 'add_tables', get_string('add_tables',
			'block_dial_rewards'));
		$mform->registerNoSubmitButton('add_tables');
		
		//$mform->setDefault('table_select', $tables['users']);


		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_rewards'));
		//Default value...
	}

}
