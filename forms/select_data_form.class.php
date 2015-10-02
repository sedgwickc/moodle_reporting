<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");
require_once("report.class.php");

class select_data_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $CFG;

		$tables = array('user'=>'Users', 
			'course'=>'Courses',
			'course_completions'=>'Course Completions'
			);

		$mform = &$this->_form; 
		$table_select = $mform->addElement('select', 'tables', get_string('table_select',
			'block_dial_rewards'), $tables, array('size'=>count($tables)));
		$table_select->setMultiple(true);
		$table_select->setSelected('users');

		$this->add_action_buttons(true, get_string('generate_report', 'block_dial_rewards'));
		//Default value...
	}

}
