<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://HeatMapTracker.com
 */
if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly');
$user = current_user();
$cur_status_id = detect_user_status($user);
$ui_enabled = validate_user_status($cur_status_id);
if (!$ui_enabled) die("Subscription status issue");
global $loggedin_user;
//registered user
if (!is_user_logged_in( $loggedin_user )) die("Only admin can access this section");
//secure get vars
foreach ($_GET as $key => $value) {
	$_GET[$key] = HMTrackerFN::hmtracker_secure($value);
}

$_GET['url'] = str_replace("~", ".", $_GET['url']);
?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $this->OPTIONS['brandname'] ?></title>
<?php
global $loggedin_user;

$this->includeCSS();
$this->includeJS();
?>
<script type="text/javascript" src="<?php echo $this->PLUGIN_URL; ?>js/heatmap.js"></script>

<script type="text/javascript">
	jQuery(document).ready(function () {
		function process(result) {
			jQuery("#loader").css("display", "none");
			<?php if($_GET['map'] == "scroll") { ?>
			var max_h = parseInt(result.max_h);
			var grid_step = parseInt(result.grid_step);

			jQuery('#spy-iframe').css("width", "100%").css("height", max_h + 20 + "px").css("opacity", "1");
			jQuery('#spy-iframe').contents().find("body").append('<div id="scroll_grid_container_wrapper" style="position: absolute !important; z-index:9999 !important; top: 0 !important; left: 0 !important; width:100%; height:' + max_h + 'px;" ></div>');
			jQuery("#scroll_grid_container_wrapper").data();

			if (jQuery('#spy-iframe').contents().find("#wpadminbar").length > 0) {
				jQuery('#heatmapArea').css("top", "23px");
			}

			var grid_levels_count = result.grid_count;

			for (var i = 0; i < grid_levels_count; i++) {
				jQuery('#spy-iframe').contents().find("#scroll_grid_container_wrapper").append('<div class="scroll_grid_container" style="position: absolute !important; z-index:9999 !important; top: ' + (i * result.grid_step) + 'px !important; opacity:0.2; left: 0 !important; width:100%; height:' + result.grid_step + 'px;" ></div>');
			}

			jQuery('#spy-iframe').contents().find(".scroll_grid_container").each(function () {
				jQuery(this).css("background", result.colors[jQuery(this).index()]);
				jQuery(this).append('<span style="position: absolute !important; display: block !important; bottom: 3px !important; left: 3px !important; font: 12px sans-serif; color: #000 !important">' + result.percents[jQuery(this).index()] + '%</span>');
			});
			jQuery('#spy-iframe').contents().find(".scroll_grid_container").hover(
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
			jQuery('.spy-frame').css('width', '100%');
			jQuery("#loader").css("display", "none");
			<?php } ?>
		}

		function get_map_data() {
			var data = {
				from: '<?php echo $_GET['from']; ?>',
				grid_step: jQuery("#grd_step").val() ? jQuery("#grd_step").val() : 50,
				layout: '<?php echo $_GET['layout']; ?>',
				map: '<?php echo $_GET['map']; ?>',
				prefix: '<?php echo T_PREFIX; ?>',
				session: '<?php echo $loggedin_user[2]; ?>',
				to: '<?php echo $_GET['to']; ?>',
				url: '<?php echo $_GET['url']; ?>'
			}

			jQuery("#loader").css("display", "block");
			jQuery('#spy-iframe').contents().find("#scroll_grid_container_wrapper").remove();
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "includes/markup/mk-heatmap-ajax-data.php",
				data: data
			}).success(function (result) {
				process(result);
			}).error(function (error) {

			});
		}

		function get_view() {

			var data = {
				brandLogo: '<?php echo $this->getBrandLogo(); ?>',
				from: '<?php echo $_GET['from']; ?>',
				grid_step: jQuery("#grd_step").val() ? jQuery("#grd_step").val() : 50,
				home_url: '<?php echo home_url(); ?>',
				layout: '<?php echo $_GET['layout']; ?>',
				map: '<?php echo $_GET['map']; ?>',
				prefix: '<?php echo T_PREFIX; ?>',
				session: '<?php echo $loggedin_user[2]; ?>',
				to: '<?php echo $_GET['to']; ?>',
				url: '<?php echo $_GET['url']; ?>'
			}

			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "includes/markup/mk-heatmap-ajax-view.php",
				data: data
			}).success(function (result) {
				if (result.view) {
					jQuery('body').html(result.view);

					jQuery('#spy-iframe').load(function () {
						jQuery("#loader-text").html("Generating Heatmap:");
						<?php if($_GET['map'] == 'scroll') { ?>
						get_map_data();
						<?php } else { ?>
						<?php switch ($_GET['layout']) {
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
						} ?>
						// heatmap configuration
						var config = {
							container: document.getElementById('heatmapArea'),
//							radius: 20
//							opacity: 0.5
							gradient: { 0.05: "rgb(0,0,255)", 0.35: "rgb(0,255,255)", 0.55: "rgb(0,255,0)", 0.75: "yellow", 1.0: "rgb(255,0,0)"}
						};

						//creates and initializes the heatmap
						var heatmap = h337.create(config);

						// let's get some data
						var data = {
							min: 1,
							max: result.max,
							data: result.data
						};

						heatmap.setData(data);

						jQuery("#loader").css("display", "none");
						<?php } ?>
					});
				}
			}).error(function (error) {
				alert("ERROR (" + error.status + "): " + error.responseText);
			});
		}

		<?php if($_GET['map'] == "scroll") { ?>
		jQuery("#grd_step").live("change", function () {
			if ($(this).val() >= 50 && $(this).val() <= 500) {
				get_map_data();
			} else {
				alert("Please select a value in the range of 50 to 500.");
			}
		})
		<?php } else {?>
		try {
			if (jQuery.browser.msie) {
				if (parseInt(jQuery.browser.version, 10) < 9) {
					jQuery('#ie_message').html("Use IE9+ to see this heat map");
				}
			}
		} catch (e) {

		}
		<?php } ?>
		get_view();
	});
</script>
<style type="text/css">
	body {
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
		background: #f5f5f5;
	}

	.spy-frame {
		display: block;
		width: 0;
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
			<a class="brand" href="javascript: window.close()" style="padding-top: 0; padding-bottom: 0">
				<img src="<?php echo $this->getBrandLogo(); ?>" alt="logo" style="max-height: 40px;" class="center"/>
			</a>

			<div id="loader" style="margin-top: 10px;">
				<span id="loader-text">Retrieving Data:</span>&nbsp;&nbsp;<img src="<?php echo $home_url; ?>/images/loader.gif"/>
			</div>
		</div>
	</div>
</div>
</body>
</html>