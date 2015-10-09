<?php
require_once('dial_reports_lib.php');
require_once("$CFG->dirroot/user/profile/lib.php");

class block_dial_reports extends block_base {
    public function init() {
        $this->title = get_string('dial_reports', 'block_dial_reports');
    }
    
    public function get_content() {
    	global $DB,$USER,$CFG;
        if ($this->content !== null) {
          return $this->content;
        }
		
		$systemcontext = context_system::instance();
		$is_admin = has_capability('moodle/site:config', $systemcontext, $USER);
		// change links based on user authorizations
		$this->content = new stdClass();
        $this->content->text = <<<HTML
        <div id='reports_links'>
        	<a href='{$CFG->wwwroot}/blocks/dial_reports/home.php'> Create New
        	Report
        	</a>
        </div>
HTML;
        return $this->content;
      }
} 
