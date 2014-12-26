<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 2014/06/30
 * Time: 9:46 AM
 */

set_time_limit(0);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
require_once('mk-heatmap-db.php');

if ($_POST['map'] == "scroll") {

	//build grid
	$color_map = array(
		/*0%*/
		"#16A099",
		/*10%*/
		"#166ba3",
		/*20%*/
		"#53907a",
		/*30%*/
		"#8db353",
		/*40%*/
		"#c6da29",
		/*50%*/
		"#e9f50a",
		/*60%*/
		"#eaff00",
		/*70%*/
		"#c8ff00",
		/*80%*/
		"#9aff00",
		/*90%*/
		"#6fff00",
		/*100%*/
		"#37ff00"
	);

	$max_h             = max($clickArr);
	$grd_step          = $_POST['grid_step'];
	$grid_levels_count = (int)ceil($max_h / $grd_step);

	$points   = array();
	$percents = array();
	$colors   = array();
	$map      = array();
	// Default the arrays
	for ($i = 0; $i < $grid_levels_count; $i++) {
		$key          = $i * $grd_step + $grd_step;
		$points[$i]   = 0;
		$percents[$i] = 0;
		$colors[$i]   = $color_map[0];
		$map[$i]      = $key;
	}

	sort($clickArr);
	foreach ($clickArr as $value) {
		for ($i = 0; $i < $grid_levels_count; $i++) {
			$hPt = $map[$i];
			if ($hPt > $value) {
				break;
			}
			$points[$i] += 1;
			$percents[$i] = (int)floor($points[$i] * 100 / $points[0]);
			$colors[$i]   = $color_map[(int)floor($percents[$i] / 10)];
		}
	}

	echo json_encode(array(
		"max_h"      => $max_h,
		"percents"   => $percents,
		"colors"     => $colors,
		"grid_count" => $grid_levels_count,
		"grid_step"  => $_POST['grid_step']
	));
}

die();