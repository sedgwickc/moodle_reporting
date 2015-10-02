<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
* Settings for the RSS client block.
*
* @package   block_dial_reports
* @copyright 2015 David Monllao
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
  $settings->add(new admin_setting_configtext(
  	'block_dial_reports/apikey',
  	get_string('new_skill_category', 'block_dial_reportss'),
  	get_string('new_skill_category_info', 'block_dial_reports'),
  	'', 
  	PARAM_RAW_TRIMMED, 
  	40));
}
                               	
