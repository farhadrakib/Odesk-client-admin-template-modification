<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 2014/06/30
 * Time: 2:51 PM
 */

require_once('../../config.php');
require_once('../db/db.class.php');

$wpdb = new DB(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD);

$type = '';
switch ($_POST['map']) {
	case 'click':
		$type     = 'Click Heatmap';
		$table2   = $_POST['prefix'] . 'clicks_' . $_POST['session'];
		$clicks   = $wpdb->get_results("SELECT `click_data` FROM $table2 WHERE `page_url` = '$_POST[url]' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->click_data));
		}

		break;
	case 'mmove':
		$type     = 'Eyescroll Heatmap';
		$table2   = $_POST['prefix'] . 'mmove_' . $_POST['session'];
		$clicks   = $wpdb->get_results("SELECT `mmove_data` FROM $table2 WHERE `page_url` = '$_POST[url]' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->mmove_data));
		}

		break;

	case 'scroll':
		$type     = 'Scroll Heatmap';
		$table2   = $_POST['prefix'] . 'scroll_' . $_POST['session'];
		$clicks   = $wpdb->get_results("SELECT `scroll_data` FROM $table2 WHERE `page_url` = '$_POST[url]' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->scroll_data));
		}
		break;

}