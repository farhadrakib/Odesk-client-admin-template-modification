<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly'); ?>
<?php
global $loggedin_user;

if (!is_user_logged_in( $loggedin_user )) die("Only admin can access this section");
if (isset($_POST['hmtracker_action']) && $_POST['hmtracker_action'] == 'save') {
	$option = $this->PROJECTS[$_POST['opt_record_to']]["settings"];

	$option['opt_record_all'] = $_POST['opt_record_all'];
	if ($option['opt_record_all'] == "false")
		$option['opt_record_special'] = $_POST['opt_record_special'];
	else
		$option['opt_record_special'] = array();

	$option['opt_ignore_query']       = $_POST['opt_ignore_query'];
	$option['opt_record_status']       = ($_POST['opt_record_status']) ? true : false;
	$option['opt_record_mousemove']    = $_POST['opt_record_mousemove'];
	$option['opt_record_pagescroll']   = $_POST['opt_record_pagescroll'];
	$option['opt_record_interval']     = $_POST['opt_record_interval'];
	$option['opt_record_kill_session'] = $_POST['opt_record_kill_session'];
	if (isset($_POST['opt_record_tz']) && $_POST['opt_record_tz'] != "-1")
		$option['opt_record_tz'] = $_POST['opt_record_tz'];
	$this->PROJECTS[$_POST['opt_record_to']]["settings"] = $option;
	update_option($this->PROJECTS_NAME . $loggedin_user[2], $this->PROJECTS);
	echo "Settings saved";
}
?>