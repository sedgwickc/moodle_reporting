<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__."/../../../config.php");

class home_form extends moodleform {
	
	//Add elements to form
	public function definition() {
		global $CFG;

		$mform = &$this->_form; // Don't forget the underscore!
		$report_types = report::$report_types;

		$mform->addElement('select', 'type', get_string('report_type', 'block_dial_rewards'),
			$report_types);
		$mform->setDefault('select', $report_types['pivot']);
		$this->add_action_buttons(true, get_string('create_report', 'block_dial_rewards'));

		                                 
		//Default value...
	}

	//Custom
	//validation
	//should
	//be
	//added
	//here
	function validation($data,	$files)	{
		return	array();
	}
}
