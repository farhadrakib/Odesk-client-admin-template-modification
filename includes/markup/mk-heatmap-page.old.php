<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly'); ?>
<?php
$user = current_user();
$cur_status_id = detect_user_status($user);
$ui_enabled = validate_user_status($cur_status_id);
if (!$ui_enabled) die("Subscription status issue");
?>
<?php
global $loggedin_user;
//registered user
if (!is_user_logged_in( $loggedin_user )) die("Only admin can access this section");
//secure get vars
foreach ($_GET as $key => $value) {
	$_GET[$key] = HMTrackerFN::hmtracker_secure($value);
}

$_GET['url'] = str_replace("~", ".", $_GET['url']);

global $wpdb, $loggedin_user;
$option = $this->OPTIONS;

switch ($_GET['map']) {
	case 'click':
		$table2   = T_PREFIX . 'clicks_' . $loggedin_user[2];
		$clicks   = $wpdb->get_results("SELECT `click_data` FROM $table2 WHERE `page_url` = '$_GET[url]' AND  date >= '$_GET[from]' AND date <= '$_GET[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->click_data));
		}

		break;
	case 'mmove':
		$table2   = T_PREFIX . 'mmove_' . $loggedin_user[2];
		$clicks   = $wpdb->get_results("SELECT `mmove_data` FROM $table2 WHERE `page_url` = '$_GET[url]' AND  date >= '$_GET[from]' AND date <= '$_GET[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->mmove_data));
		}

		break;

	case 'scroll':
		$table2   = T_PREFIX . 'scroll_' . $loggedin_user[2];
		$clicks   = $wpdb->get_results("SELECT `scroll_data` FROM $table2 WHERE `page_url` = '$_GET[url]' AND  date >= '$_GET[from]' AND date <= '$_GET[to]'");
		$clickArr = array();
		foreach ($clicks as $key => $value) {
			$clickArr = array_merge($clickArr, explode("|", $value->scroll_data));
		}
		break;

}

$spots = array();
$counts = array();
$height = 0;
$width = 0;
$radius = 30;
$count = count($clickArr);
$exArr = array();

foreach ($clickArr as $key => $value) {
	$valueArr = explode(" ", $value);
	if (count($valueArr) < 3) continue;
	$exArr[] = $valueArr;
	$width   = ($width < $valueArr[2]) ? $valueArr[2] : $width;
	$height  = ($height < $valueArr[1]) ? $valueArr[1] : $height;
}

foreach ($exArr as $key => $value) {
	switch ($_GET['layout']) {
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
?>
<!doctype html>
<html lang="en">
<head>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<?php
	$this->includeCSS();
	$this->includeJS();
	?>





	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('#spy-iframe').load(function () {
				<?php
				if($_GET['map'] == "scroll"){
				asort($clickArr);
				?>
				function rebuild_map() {
					jQuery('#spy-iframe').contents().find("#scroll_grid_container").remove();
					var scroll_data = new Array(<?php echo implode(",", $clickArr); ?>);
					var max_h = <?php echo max($clickArr); ?>;
					jQuery('#spy-iframe').css("width", "100%").css("height", max_h + 20 + "px").css("opacity", "1");


					jQuery('#spy-iframe').contents().find("body").append('<div id="scroll_grid_container" style="position: absolute !important; z-index:9999 !important; top: 0 !important; left: 0 !important; width:100%; height:' + max_h + 'px;" ></div>');

					if (jQuery('#spy-iframe').contents().find("#wpadminbar").length > 0) {
						jQuery('#heatmapArea').css("top", "23px");
					}
					;

					//build grid
					var color_map = [
						/*0%*/"#166ba3",
						/*10%*/"#166ba3",
						/*20%*/"#53907a",
						/*30%*/"#8db353",
						/*40%*/"#c6da29",
						/*50%*/"#e9f50a",
						/*60%*/"#eaff00",
						/*70%*/"#c8ff00",
						/*80%*/"#9aff00",
						/*90%*/"#6fff00",
						/*100%*/"#37ff00"
					];
					var grid_step = jQuery("#grd_step").val();
					var grid_levels_count = parseInt(max_h / grid_step);


					for (var i = 0; i < grid_levels_count; i++) {
						jQuery('#spy-iframe').contents().find("#scroll_grid_container").append('<div id="scroll_grid_container" style="position: absolute !important; z-index:9999 !important; top: ' + (i * grid_step) + 'px !important; opacity:0.2; left: 0 !important; width:100%; height:' + grid_step + 'px;" ></div>');
					}
					;

					var points = {};
					jQuery('#spy-iframe').contents().find("#scroll_grid_container div").each(function () {
						var hPt = parseInt(jQuery(this).position().top) + parseInt(grid_step);
						points[jQuery(this).index()] = 0;
						for (var i = 0; i < scroll_data.length; i++) {
							points[jQuery(this).index()] += (hPt < scroll_data[i]) ? 1 : 0;
						}
						;
					});

					var max = points[0];
					var percents = {}
					var colors = {}
					jQuery.each(points, function (key, value) {
						var _prc = parseInt(value * 100 / max);
						percents[key] = _prc;
						colors[key] = color_map[(_prc - _prc % 10) / 10];
					});

					jQuery('#spy-iframe').contents().find("#scroll_grid_container div").each(function () {
						jQuery(this).css("background", colors[jQuery(this).index()]);
						jQuery(this).append('<span style="position: absolute !important; display: block !important; bottom: 3px !important; left: 3px !important; font: 12px sans-serif; color: #000 !important">' + percents[jQuery(this).index()] + '%</span>');
					});
					jQuery('#spy-iframe').contents().find("#scroll_grid_container div").hover(
						function () {
							jQuery(this).css("opacity", "0.6");
							jQuery(this).css("border-bottom", "2px #000 dashed");
							jQuery(this).find("span").css("background-color", "#fff");
						},
						function () {
							jQuery(this).css("opacity", "0.2");
							jQuery(this).css("border-bottom", "none");
							jQuery(this).find("span").css("background", "none");
						}
					);
				}

				rebuild_map();
				jQuery("#grd_step").change(function () {
					rebuild_map();
				})
				jQuery('.heat-holder').css('width', '100%');
				<?php
				} else {?>

				try {
					if (jQuery.browser.msie) {
						if (parseInt(jQuery.browser.version, 10) < 9) {
							jQuery('#ie_message').html("Use IE9+ to see this heat map");
						}
					}
				} catch (e) {

				}


				jQuery('#spy-iframe').contents().find("body").append('<div id="hmtracker_heatmap" style="width: 100px; height:100px; position: absolute !important; z-index:9999 !important; top: 0 !important; left: 0 !important"></div>');

				if (jQuery('#spy-iframe').contents().find("#wpadminbar").length > 0) {
					jQuery('#heatmapArea').css("top", "23px");
				}
				;


				// heatmap configuration
				var config = {
					element: jQuery('#heatmapArea').get(0),
					radius: 20,
					opacity: 50
				};

				//creates and initializes the heatmap
				var heatmap = h337.create(config);

				// let's get some data
				var data = {
					max: <?php echo max($counts) ?>,
					data: [
						<?php
							foreach ($spots as $key => $value) {
								?>
						{ x: <?php echo $value[0] ?>, y: <?php echo $value[1] ?>, count: <?php echo $counts[$value[0]."_".$value[1]] ?> },
						<?php
					}
				?>
					]
				};

				heatmap.store.setDataSet(data);

				<?php

					switch ($_GET['layout']) {
						case 'left': ?>
				setTimeout(function () {
					jQuery("html, body").scrollLeft(0);
					jQuery('#spy-iframe').animate({ opacity: 1 }, 200);
				}, 500);
				<?php break;
			case 'center':  ?>
				setTimeout(function () {
					jQuery("html, body").scrollLeft((jQuery("body")[0].scrollWidth - jQuery("body")[0].clientWidth) / 2);
					jQuery('#spy-iframe').animate({ opacity: 1 }, 200);
				}, 500);
				<?php break;
			case 'right':  ?>
				setTimeout(function () {
					jQuery("html, body").scrollLeft(jQuery("body")[0].scrollWidth);
					jQuery('#spy-iframe').animate({ opacity: 1 }, 200);
				}, 500);
				<?php break;
		}

	}?>


			});
		});
	</script>
	<style type="text/css">
		body {
			font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
			background: #f5f5f5;
		}

		.spy-frame {
			display: block;
			width: <?php echo $width+$radius ?>px;
			height: <?php echo (($height+$radius)> 1000)?($height+$radius):1000 ?>px;
			z-index: 1;
		}

		iframe {
			-moz-box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
			-webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
		}
	</style>
</head>
<body>
<div class="navbar navbar-fixed-top" style="z-index: 20">
	<div class="navbar-inner">
		<div class="container" style="margin-left: 10px">
			<a class="brand" href="javascript: window.close()" style="padding-top: 0; padding-bottom: 0"><img
					src="<?php echo $this->getBrandLogo(); ?>" alt="logo" style="max-height: 40px;"
					class="center"/> </a>
			<ul class="nav">
				<?php if ($count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;" id="ie_message"><?php echo count($clickArr) ?> points analyzed</li>

					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">From: <?php echo $_GET['from'] ?> To: <?php echo $_GET['to'] ?></li>
					<?php if ($_GET['map'] == 'scroll') { ?>
						<li class="divider-vertical"></li>
						<li style="margin: 10px 0 0;">Scroll grid step: &nbsp;&nbsp;</li>
						<li style="margin: 10px 0 0;"><input id="grd_step" style=" height: 23px; margin: 0; padding: 0;"
						                                     class="span1 opt_record_interval" min="50" max="500"
						                                     step="10" value="50" type="number"></li>
					<?php } ?>
				<?php elseif ($count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;"><?php echo count($clickArr) ?> points analysys</li>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">Please, wait..</li>
				<?php
				elseif (!$count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;"><strong style="color:#f00">No tracking data for the selected time
							period</strong></li>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">For the period From: <?php echo $_GET['from'] ?>
						To: <?php echo $_GET['to'] ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<div class="heat-holder spy-frame" style="margin: 41px auto 0; position: relative; z-index: 10">
	<?php if ($count > 0): ?>
		<div id="heatmapArea" class="spy-frame"
		     style="position: absolute !important; z-index:9999 !important; top: 0px; left: 15px !important"></div>
		<iframe id="spy-iframe" class="spy-frame"
		        src="<?php echo home_url() . '?heatmap_frame=&url=' . str_replace(".", "~", $_GET['url']) ?>"
		        name="spy-frame" frameborder="0" noresize="noresize" scrolling="no"></iframe>
	<?php endif; ?>
</div>
</body>
</html>