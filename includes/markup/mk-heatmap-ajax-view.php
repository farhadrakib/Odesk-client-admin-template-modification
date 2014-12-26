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

function requireToVar($file, $var = array())
{
	ob_start();
	extract($var);
	require($file);

	return ob_get_clean();
}

$spots    = array();
$counts   = array();
$height   = isset($_POST['map']) && $_POST['map'] == "scroll" ? max($clickArr) : 0;
$width    = 0;
$radius   = 30;
$grd_step = 50;
$count    = count($clickArr);
$exArr    = array();

foreach ($clickArr as $key => $value) {
	$valueArr = explode(" ", $value);
	if (count($valueArr) < 3) continue;
	$exArr[] = $valueArr;
	$width   = ($width < $valueArr[2]) ? $valueArr[2] : $width;
	$height  = ($height < $valueArr[1]) ? $valueArr[1] : $height;
}

foreach ($exArr as $key => $value) {
	switch ($_POST['layout']) {
		case 'left':
			$_x = $value[0];
			$_y = $value[1];
			break;
		case 'center':
			$delta = (int)($width / 2 - $value[2] / 2);
			$_x    = $value[0] + $delta;
			$_y    = $value[1];
			break;
		case 'right':
			$delta = $width - $value[2];
			$_x    = $value[0] + $delta;
			$_y    = $value[1];
			break;
	}

	if (isset($counts[$_x . "_" . $_y])) $counts[$_x . "_" . $_y] += 1;
	else {
		$counts[$_x . "_" . $_y] = 1;
		$spots[]                 = array($_x, $_y);
	}
}

$data = array();
foreach ($spots as $key => $value) {
	$data[$key]        = new stdClass();
	$data[$key]->x     = (int)$value[0];
	$data[$key]->y     = (int)$value[1];
	$data[$key]->value = $counts[$value[0] . "_" . $value[1]];
}

// SimpleHeat
//$data = array();
//foreach ($spots as $key => $value) {
//	$data[$key]     = array();
//	$data[$key][0]  = (int)$value[0];
//	$data[$key][1]  = (int)$value[1];
//	$data[$key][2]  = $counts[$value[0] . "_" . $value[1]];
//}
$width += $radius;
$height += $radius;

$args              = array();
$args['count']     = $count;
$args['home_url']  = $_POST['home_url'];
$args['url']       = str_replace(".", "~", $_POST['url']);
$args['map']       = $_POST['map'];
$args['brandLogo'] = isset($_POST['brandLogo']) ? $_POST['brandLogo'] : '';
$args['from']      = $_POST['from'];
$args['to']        = $_POST['to'];
$args['grid_step'] = $_POST['grid_step'];
$args['width']     = $width;
$args['height']    = $height;

echo json_encode(array(
	"width"  => $width,
	"height" => $height > 1000 ? $height : 3000,
	"max"    => !empty($counts) ? max($counts) : 0,
	"data"   => $data,
	"view"   => requireToVar("views/mk-heatmap-view.php", $args)
));

die();