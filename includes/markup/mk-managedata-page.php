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
if (!is_user_logged_in( $loggedin_user ) && IS_KEY_VALID) header('location: ' . admin_url() . '?login');
$user = current_user();
$cur_status_id = detect_user_status($user);
$ui_enabled = validate_user_status($cur_status_id);
if (!$ui_enabled) die("Subscription status issue");
?>
<?php $_GET["mdata"] = rawurlencode($_GET["mdata"]); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->PLUGIN_URL ?>css/datepicker.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->PLUGIN_URL ?>css/style.css"/>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"/>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<strong class="logo"><a href="<?php echo $this->PLUGIN_URL ?>"><img
					src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
		<ul id="nav">
			<?php if ( isset( $_SESSION['return_to_admin'] ) && $_SESSION['return_to_admin'] ) { ?>
				<li><a href="<?php echo $this->PLUGIN_URL ?>?return_admin">Return to Admin</a></li>
			<?php } ?>
			<li><a href="<?php echo $this->PLUGIN_URL ?>">Projects</a></li>
			<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank">Support</a></li>
			<li><a href="<?php echo $this->PLUGIN_URL ?>?logout">Log Out</a></li>
		</ul>
	</div>
	<div id="main">
		<div class="container">
			<div class="headbar">
				<em class="date"><?php echo date("F d, Y") ?></em>
			</div>
			<div id="sidebar">
				<ul class="sidenav">
					<li><a href="<?php echo $this->PLUGIN_URL ?>?project=<?php echo $_GET['mdata'] ?>"><i
								class="icon-th-list"></i> Dashboard</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?analytics=<?php echo $_GET['mdata'] ?>"><i
								class="icon-bar-chart"></i> User Sessions</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?hmaps=<?php echo $_GET['mdata'] ?>"><i
								class="icon-dashboard"></i> Heat Maps</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?ppages=<?php echo $_GET['mdata'] ?>"><i
								class="icon-star"></i> Popular Pages</a></li>
					<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>?mdata=<?php echo $_GET['mdata'] ?>"><i
								class="icon-hdd"></i> Manage Data</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?settings=<?php echo $_GET['mdata'] ?>"><i
								class="icon-cogs"></i> Settings</a></li>
					<li><a href="<?php echo admin_url() ?>?helpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" tarfet="_blank"><i
								class=" icon-comments-alt"></i> Support</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="analytics-block">
					<h2><span>Data Info and Management</span> Manage Data</h2>

					<div class="table-holder">
						<form action="" method="post" class="form-horizontal" id="manage_data_form">
							<input name="manage" value="tables" type="hidden"/>
							<input name="from" value="" type="hidden"/>
							<input name="to" value="" type="hidden"/>
							<input name="what" value="" type="hidden"/>
							<input name="page" value="<?php echo $_GET['page']; ?>" type="hidden"/>

							<?php
							global $wpdb, $loggedin_user;

							//manage data
							if (isset($_POST['manage']) && $_POST['manage'] == "tables") {

								switch ($_POST['what']) {
									case 'sessions':
										$table2 = T_PREFIX . 'main_' . $loggedin_user[2];
										$wpdb->query("DELETE FROM $table2 WHERE `project` = '" . $_GET["mdata"] . "' AND session_time >= '" . strtotime($_POST['from']) . "' AND session_time <= '" . (strtotime($_POST['to']) + 82800 + 3599) . "'");
										break;
									case 'clicks':
										$table2 = T_PREFIX . 'clicks_' . $loggedin_user[2];
										$wpdb->query("DELETE FROM $table2 WHERE `project` = '" . $_GET["mdata"] . "' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
										break;
									case 'eye':
										$table2 = T_PREFIX . 'mmove_' . $loggedin_user[2];
										$wpdb->query("DELETE FROM $table2 WHERE `project` = '" . $_GET["mdata"] . "' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
										break;
									case 'scroll':
										$table2 = T_PREFIX . 'scroll_' . $loggedin_user[2];
										$wpdb->query("DELETE FROM $table2 WHERE `project` = '" . $_GET["mdata"] . "' AND  date >= '$_POST[from]' AND date <= '$_POST[to]'");
										break;
									case 'popular':
										$table2 = T_PREFIX . 'popular_' . $loggedin_user[2];
										$wpdb->query("DELETE FROM $table2 WHERE `project` = '" . $_GET["mdata"] . "'");
										break;
								}

							}


							$table1 = T_PREFIX . 'main_' . $loggedin_user[2];
							$table2 = T_PREFIX . 'clicks_' . $loggedin_user[2];
							$table3 = T_PREFIX . 'mmove_' . $loggedin_user[2];
							$table4 = T_PREFIX . 'scroll_' . $loggedin_user[2];
							$table5 = T_PREFIX . 'popular_' . $loggedin_user[2];
							$query = 'SELECT TABLE_SCHEMA AS "Database", TABLE_NAME AS "Table",
									ROUND(SUM((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024),2) AS Size
									FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA like "%' . DB_NAME . '%" AND
									(TABLE_NAME = "' . $table1 . '" OR TABLE_NAME = "' . $table2 . '" OR
									TABLE_NAME = "' . $table3 . '" OR TABLE_NAME = "' . $table4 . '" OR
									TABLE_NAME = "' . $table5 . '")';

							$size_res = $wpdb->get_results($query);
							$total = 0;
							if($size_res)
								$total = $size_res[0]->Size;
							?>



							<div class="control-group">
								<label class="control-label">Data Total Size</label>

								<div class="controls">
									<div class="btn-group btn-layout">
										<button disabled type="button" class="btn btn-mini" disabled>
											<strong><?php echo $total; ?> MB</strong>
										</button>
									</div>
									<a class="help-ico" data-trigger="hover" rel="popover"
									   data-original-title="Data Total Size"
									   data-content="Size in MB of all HeatMapTracker MySQL tables">lnk</a>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">Tables</label>

								<div class="controls">
									<div class="btn-group btn-group-vertical all-special btn-layout btn-what"
									     data-toggle="buttons-radio">
										<button type="button" class="btn active btn-success btn-mini btn-sessions"
										        data-value="center" style="width:162px;">
											Sessions
										</button>
										<button type="button" class="btn btn-mini btn-clicks" data-value="left"
										        style="width:162px;">
											Clicks Heatmap
										</button>
										<button type="button" class="btn btn-mini btn-eye" data-value="right"
										        style="width:162px;">
											Eye-tracking Heatmap
										</button>
										<button type="button" class="btn btn-mini btn-scroll" data-value="right"
										        style="width:162px;">
											Scroll Heatmap
										</button>
										<button type="button" class="btn btn-mini btn-popular" data-value="right"
										        style="width:162px;">
											Popular Pages
										</button>
									</div>
									<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Tables"
									   data-content="All HeatMapTracker tables. Please select table to delete data">lnk</a>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">Date Range</label>

								<div class="controls">
									<div class="btn-group all-special btn-md" data-toggle="buttons-radio">
										<button type="button" class="btn active btn-success btn-mini btn-md-day"
										        data-value="2">
											Last Day
										</button>
										<button type="button" class="btn btn-mini btn-md-week" data-value="2">
											Last Week
										</button>
										<button type="button" class="btn btn-mini btn-md-month" data-value="2">
											Last Month
										</button>
										<button type="button" class="btn btn-mini btn-md-range" data-value="2">
											Date Range
										</button>
									</div>
									<a class="help-ico" data-trigger="hover" rel="popover"
									   data-original-title="Date Range"
									   data-content="Choose date range to delete data from the selected table">lnk</a>
								</div>
								<br/>

								<div class="controls date-md-range-buttons">
									<button disabled type="button"
									        class="btn btn-primary width-auto btn-mini  from-date-heatmap" id="dp6"
									        data-date-format="yyyy-mm-dd" data-date="2012-02-20">
										<strong>From</strong> <span>2012-02-20</span>
									</button>
									<button disabled type="button"
									        class="btn btn-primary width-auto btn-mini to-date-heatmap" id="dp7"
									        data-date-format="yyyy-mm-dd" data-date="2012-02-23">
										<strong>To</strong> <span>2012-02-23</span>
									</button>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary width-auto save-button">
									Delete
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<br/>
		<?php echo date("Y") ?> &copy; <?php echo $this->OPTIONS['brandname'] ?>
		v. <?php echo $this->OPTIONS['version']; ?>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/excanvas.js"></script>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/respond.js"></script>
<![endif]-->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
<!-- IMPORTANT! jquery.slimscroll.min.js depends on jquery-ui-1.10.1.custom.min.js -->

<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script type="text/javascript" src="<?php echo $this->PLUGIN_URL ?>js/bootstrap-datepicker.js"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();
		jQuery('.help-ico').popover({'placement': 'right'});

		//all pages/special page buttons
		jQuery('.all-special button').click(function () {
			if (!jQuery(this).hasClass("active")) {
				jQuery(this).parent().find("button").removeClass("btn-success");
				jQuery(this).addClass("btn-success")

				if (jQuery(this).hasClass("btn-special")) {
					jQuery(".pagesposts").removeAttr("disabled");
				} else {
					jQuery(".pagesposts").attr("disabled", "");
				}
			}
		})
		//datepickers
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();
		if (dd < 10) {
			dd = '0' + dd
		}
		if (mm < 10) {
			mm = '0' + mm
		}
		var _today = yyyy + '-' + mm + '-' + dd;
		var __today = yyyy + '-' + mm + '-' + dd;
		//day
		var dayago = new Date(today.getTime() - 1 * 24 * 60 * 60 * 1000);
		dd = dayago.getDate();
		mm = dayago.getMonth() + 1; //January is 0!
		yyyy = dayago.getFullYear();
		if (dd < 10) {
			dd = '0' + dd
		}
		if (mm < 10) {
			mm = '0' + mm
		}
		var _dayago = yyyy + '-' + mm + '-' + dd;
		var __dayago = yyyy + '-' + mm + '-' + dd;
		//week
		var weekago = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
		dd = weekago.getDate();
		mm = weekago.getMonth() + 1; //January is 0!
		yyyy = weekago.getFullYear();
		if (dd < 10) {
			dd = '0' + dd
		}
		if (mm < 10) {
			mm = '0' + mm
		}
		var _weekago = yyyy + '-' + mm + '-' + dd;
		var __weekago = yyyy + '-' + mm + '-' + dd;
		//month
		var monthago = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
		dd = monthago.getDate();
		mm = monthago.getMonth() + 1; //January is 0!
		yyyy = monthago.getFullYear();
		if (dd < 10) {
			dd = '0' + dd
		}
		if (mm < 10) {
			mm = '0' + mm
		}
		var _monthago = yyyy + '-' + mm + '-' + dd;
		var __monthago = yyyy + '-' + mm + '-' + dd;

		var _from = _dayago, _to = _today;
		var __from = __dayago, __to = __today;

		jQuery('#dp4').datepicker()
			.on('changeDate', function (ev) {
				jQuery(this).find("span").html(jQuery(this).data('date'));
				_from = jQuery(this).data('date');
				jQuery('#dp4').datepicker('hide');
			});
		jQuery('#dp5').datepicker()
			.on('changeDate', function (ev) {
				jQuery(this).find("span").html(jQuery(this).data('date'));
				jQuery('#dp5').datepicker('hide');
				_to = jQuery(this).data('date');
			});

		jQuery(".btn-d-day").click(function () {
			_from = _dayago;
			_to = _today
		});
		jQuery(".btn-d-week").click(function () {
			_from = _weekago;
			_to = _today
		});
		jQuery(".btn-d-month").click(function () {
			_from = _monthago;
			_to = _today
		});

		var _map = "click";
		var _click_opts = jQuery('#click_urls').html();
		var _move_opts = jQuery('#move_urls').html();
		var _scroll_opts = jQuery('#scroll_urls').html();

		jQuery('#move_urls').remove();
		jQuery('#scroll_urls').remove();

		jQuery(".btn-h-click").click(function () {
			_map = "click";
			jQuery('.heat-urls').html("");
			jQuery('.heat-urls').append('<optgroup label="By URL">' + _click_opts + '</optgroup>')
		});
		jQuery(".btn-h-move").click(function () {
			_map = "mmove";
			jQuery('.heat-urls').html("");
			jQuery('.heat-urls').append('<optgroup label="By URL">' + _move_opts + '</optgroup>')
		});
		jQuery(".btn-h-scroll").click(function () {
			_map = "scroll";
			jQuery('.heat-urls').html("");
			jQuery('.heat-urls').append('<optgroup label="By URL">' + _scroll_opts + '</optgroup>')
		});


		jQuery('.from-date-heatmap').attr("data-date", _weekago).find("span").html(_weekago);
		jQuery('.to-date-heatmap').attr("data-date", _today).find("span").html(_today);


		//manage data
		jQuery('#dp6').datepicker()
			.on('changeDate', function (ev) {
				jQuery(this).find("span").html(jQuery(this).data('date'));
				__from = jQuery(this).data('date');
				jQuery("#manage_data_form input[name$='from']").val(__from);
				jQuery('#dp6').datepicker('hide');
			});
		jQuery('#dp7').datepicker()
			.on('changeDate', function (ev) {
				jQuery(this).find("span").html(jQuery(this).data('date'));
				jQuery('#dp7').datepicker('hide');
				__to = jQuery(this).data('date');
				jQuery("#manage_data_form input[name$='to']").val(__to);
			});

		jQuery(".btn-md button").click(function () {
			setTimeout(function () {
				if (jQuery(".btn-md-range").hasClass("active")) {
					jQuery(".date-md-range-buttons button").removeAttr("disabled");
				} else {
					jQuery(".date-md-range-buttons button").attr("disabled", "");
				}
			}, 100);
		})

		jQuery(".btn-what button").click(function () {
			setTimeout(function () {
				if (jQuery(".btn-popular").hasClass("active")) {
					jQuery(".btn-md button").attr("disabled", "");
				} else {
					jQuery(".btn-md button").removeAttr("disabled");
				}
			}, 100);
		})

		jQuery("#manage_data_form input[name$='from']").val(__from);
		jQuery("#manage_data_form input[name$='to']").val(__to);
		jQuery("#manage_data_form input[name$='what']").val("sessions");

		jQuery(".btn-md-day").click(function () {
			__from = __dayago;
			__to = __today
			jQuery("#manage_data_form input[name$='from']").val(__from);
			jQuery("#manage_data_form input[name$='to']").val(__to);
		});
		jQuery(".btn-md-week").click(function () {
			__from = __weekago;
			__to = __today

			jQuery("#manage_data_form input[name$='from']").val(__from);
			jQuery("#manage_data_form input[name$='to']").val(__to);
		});
		jQuery(".btn-md-month").click(function () {
			__from = __monthago;
			__to = __today

			jQuery("#manage_data_form input[name$='from']").val(__from);
			jQuery("#manage_data_form input[name$='to']").val(__to);
		});

		jQuery(".btn-sessions").click(function () {
			jQuery("#manage_data_form input[name$='what']").val("sessions");
		});
		jQuery(".btn-clicks").click(function () {
			jQuery("#manage_data_form input[name$='what']").val("clicks");
		});
		jQuery(".btn-eye").click(function () {
			jQuery("#manage_data_form input[name$='what']").val("eye");
		});
		jQuery(".btn-scroll").click(function () {
			jQuery("#manage_data_form input[name$='what']").val("scroll");
		});
		jQuery(".btn-popular").click(function () {
			jQuery("#manage_data_form input[name$='what']").val("popular");
		});

	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
