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
if (!is_user_logged_in( $loggedin_user ) && IS_KEY_VALID) header('location: ' . admin_url() . '?login'); ?>
<?php
$user = current_user();
$cur_status_id = detect_user_status($user);
$ui_enabled = validate_user_status($cur_status_id);
if (!$ui_enabled) die("Subscription status issue");
?>
<?php $_GET["analytics"] = rawurlencode($_GET["analytics"]); ?>
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
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->PLUGIN_URL ?>css/flags.css"/>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
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
					<li><a href="<?php echo $this->PLUGIN_URL ?>?project=<?php echo $_GET['analytics'] ?>"><i
								class="icon-th-list"></i> Dashboard</a></li>
					<li><a class="active"
					       href="<?php echo $this->PLUGIN_URL ?>?analytics=<?php echo $_GET['analytics'] ?>"><i
								class="icon-bar-chart"></i> User Sessions</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?hmaps=<?php echo $_GET['analytics'] ?>"><i
								class="icon-dashboard"></i> Heat Maps</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?ppages=<?php echo $_GET['analytics'] ?>"><i
								class="icon-star"></i> Popular Pages</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?mdata=<?php echo $_GET['analytics'] ?>"><i
								class="icon-hdd"></i> Manage Data</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?settings=<?php echo $_GET['analytics'] ?>"><i
								class="icon-cogs"></i> Settings</a></li>
					<li><a href="<?php echo admin_url() ?>?helpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank"><i
								class=" icon-comments-alt"></i> Support</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="analytics-block">
					<h2><span>VIEW RECORDED SESSIONS</span> Analytics</h2>

					<div class="table-holder">
						<label class="pull-left">
							<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="perpage">
								<input type="hidden" name="analytics"
								       value="<?php echo rawurldecode($_GET["analytics"]) ?>"/>
								<input type="hidden" name="order_by"
								       value="<?php echo (isset($_GET["order_by"])) ? $_GET["order_by"] : "session_start" ?>"/>
								<input type="hidden" name="s"
								       value="<?php echo (isset($_GET["s"])) ? $_GET["s"] : "" ?>"/>
								per page <select size="1" name="perpage" class="input-small perpage"
								                 style="margin-bottom: 0">
									<?php
									for ($i = 10; $i < 510; $i += 10) {
										?>
										<option
											value="<?php echo $i ?>" <?php if ((isset($_GET['perpage']) && $i == $_GET['perpage']) || (!isset($_GET['perpage']) && $i == 30)): ?> selected="selected"<?php endif; ?>><?php echo $i ?></option>
									<?php } ?>
								</select>
							</form>
						</label>
						<label class="pull-left">&nbsp;&rsaquo;&nbsp;</label>
						<label class="pull-left">
							<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="orderby">
								<input type="hidden" name="analytics"
								       value="<?php echo rawurldecode($_GET["analytics"]) ?>"/>
								<input type="hidden" name="perpage"
								       value="<?php echo (isset($_GET["perpage"])) ? $_GET["perpage"] : "10" ?>"/>
								<input type="hidden" name="order_by"
								       value="<?php echo (isset($_GET["order_by"])) ? $_GET["order_by"] : "session_start" ?>"/>
								<input type="hidden" name="s"
								       value="<?php echo (isset($_GET["s"])) ? $_GET["s"] : "" ?>"/>
								order by
								<select size="1" name="order_by" class="input-medium perpage" style="margin-bottom: 0">
									<option <?php echo ($_GET["order_by"] == "session_start") ? 'selected' : "" ?>
										value="session_start">Session Date
									</option>
									<option <?php echo ($_GET["order_by"] == "session_time") ? 'selected' : "" ?>
										value="session_time">Session Time
									</option>
								</select>
							</form>
						</label>
						<label class="pull-left">&nbsp;&rsaquo;&nbsp;</label>
						<label class="pull-left">
							<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="orderby">
								<input type="hidden" name="analytics"
								       value="<?php echo rawurldecode($_GET["analytics"]) ?>"/>
								<input type="hidden" name="perpage"
								       value="<?php echo (isset($_GET["perpage"])) ? $_GET["perpage"] : "10" ?>"/>
								<input type="hidden" name="order_by"
								       value="<?php echo (isset($_GET["order_by"])) ? $_GET["order_by"] : "session_start" ?>"/>
								<input type='text' value="<?php echo (isset($_GET["s"])) ? $_GET["s"] : "" ?>"
								       style="margin: 0; height: 12px;" name="s">
								<button type="submit" class="btn btn-mini btn-primary width-auto "> search</button>
							</form>
						</label>

						<div class="pull-right">
							<button class="btn btn-mini btn-primary width-auto " id="deleteall">Delete Selected</button>
						</div>


						<form id="to_del_form"
						      action="<?php echo admin_url() . '?analytics=' . $_GET["analytics"] . (isset($_GET['perpage']) ? '&perpage=' . $_GET['perpage'] : '') . (isset($_GET['paged']) ? '&paged=' . $_GET['paged'] : '') ?>"
						      method="post">

							<table border="0" width="100%" cellpadding="0" cellspacing="0"
							       class="table table-bordered table-striped bs-table" id="gcheck">
								<tr>
									<th style="width:8px;"><input type="checkbox" class="group-checkable"
									                              data-set="#gcheck .checkboxes"/></th>
									<th class=" minwidth-1"><span>Play</span></th>
									<th class=" minwidth-1"><span>OS, Browser</span></th>
									<th class=" minwidth-1"><span>User IP, Country</span></th>
									<th class=""><span>Session Time</span></th>
									<th class=""><span>Session Date</span></th>
									<th class=""><span>Viewed Pages</span></th>
									<th class="table-header-options "><span>Options</span></th>
								</tr>
								<?php

								global $wpdb, $loggedin_user;
								if (isset($this->PROJECTS[$_GET['analytics']]['settings']['opt_record_tz'])) date_default_timezone_set($this->PROJECTS[$_GET['analytics']]['settings']['opt_record_tz']);
								//delete
								if (isset($_GET['action']) && $_GET['action'] == 'delete') {

									global $wpdb;
									$table    = T_PREFIX . 'main_' . $loggedin_user[2];
									$entry_id = (is_array($_REQUEST['session'])) ? $_REQUEST['session'] : array($_REQUEST['session']);
									foreach ($entry_id as $id) {
										$wpdb->query("DELETE FROM `" . T_PREFIX . 'main_' . $loggedin_user[2] . "` WHERE id = $id");
									}
								}
								//delete batch
								if (isset($_POST['todel']) && is_array($_POST['todel']) && count($_POST['todel']) > 0) {
									global $wpdb;
									$table    = T_PREFIX . 'main_' . $loggedin_user[2];
									$entry_id = (is_array($_POST['todel'])) ? $_POST['todel'] : array($_POST['todel']);
									foreach ($entry_id as $id) {
										$wpdb->query("DELETE FROM $table WHERE id = $id");
									}
								}


								// if we have a result loop over the result

								$order_by = (isset($_GET['order_by'])) ? (($_GET['order_by'] == "session_time") ? "`session_end` - `session_start`" : "`" . $_GET['order_by'] . "`") : "`session_start`";

								$search = (isset($_GET['s']) && $_GET['s'] != "") ? " AND `session_spydata` like '%" . $_GET['s'] . "%'" : "";

								$q = "SELECT * FROM `" . T_PREFIX . 'main_' . $loggedin_user[2] . "` WHERE `project` = '" . $_GET["analytics"] . "' " . $search . " ORDER BY " . $order_by . " DESC";
								//pagination
								$perpage = (isset($_GET['perpage'])) ? $_GET['perpage'] : 30;


								$totalitems = $wpdb->get_var("SELECT COUNT(*) FROM `" . T_PREFIX . 'main_' . $loggedin_user[2] . "` WHERE `project` = '" . $_GET["analytics"] . "' " . $search);
								$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';


								if (!empty($paged) && !empty($perpage)) {
									$offset = $paged;
									$q .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
								} else {
									$q .= ' LIMIT ' . (int)$perpage;
								}

								$r = $wpdb->query($q);
								$nr = $wpdb->numRows($r);


								$objGeoIP = new hmtracker_GeoIP();
								$counter = 0;
								if ($nr > 0) {
									while ($a = mysql_fetch_assoc($r)) {

										//extract viewed pages
										$page_history = "";
										$arr_data     = json_decode($a['session_spydata']);
										$inc          = 0;
										if (is_array($arr_data))
											foreach ($arr_data as $key => $value) {
												$pg = "";
												foreach ($value as $kkey => $vvalue) $pg = $kkey;
												$str = explode("/", $pg);
												if ($str[count($str) - 1] != "")
													$page_history .= '<a href="' . $pg . '" target="_blank">' . $str[count($str) - 1] . '</a>' . ((count($arr_data) > ($inc + 1)) ? ' <b style="color:#f00">></b> ' : '');
												else
													$page_history .= '<a href="' . $pg . '" target="_blank">' . $str[count($str) - 2] . '</a>' . ((count($arr_data) > ($inc + 1)) ? ' <b style="color:#f00">></b> ' : '');
												$inc++;
											}
										//split user id
										$usrData = explode("~", $a['user_id']);

										//get geo info
										$country = "not found";
										$objGeoIP->search_ip($usrData[0]);
										if ($objGeoIP->found()) {
											$country = $objGeoIP->getCountryName();
											$fclass  = "flag-" . $objGeoIP->getCountryCode();
										}

										//build table row
										?>
										<tr class="rows <?php echo $a["id"]; ?>">
											<td><input type="checkbox" class="checkboxes" name="todel[]"
											           value="<?php echo $a["id"]; ?>"/></td>
											<td class="options-width"><?php echo '<a href="' . admin_url() . '?hmtrackerview=&session=' . $a["id"] . '" target="_blank" style="height: 11px;" class="btn btn-mini btn-info"><img src="' . $this->PLUGIN_URL . 'images/play-btn.png" width="9" height="10" style="vertical-align:baseline;" /></a>' ?></td>
											<td><?php echo $usrData[1] ?></td>
											<td><?php echo $usrData[0] ?> <i
													class="<?php echo $fclass ?>"></i> <?php echo $country ?></td>
											<td><?php echo HMTrackerFN::sec2hms(($a["session_end"] - $a["session_start"])) ?></td>
											<td><?php echo date("m.d.y, g:i a", $a["session_time"]) ?></td>
											<td><?php echo $page_history ?></td>
											<td class="options-width"><?php echo '<a href="' . admin_url() . '?analytics=' . $_GET["analytics"] . '&paged=' . (isset($_GET['perpage']) ? '&perpage=' . $_GET['perpage'] : '') . (isset($_GET['order_by']) ? '&order_by=' . $_GET['order_by'] : '') . (isset($_GET['s']) ? '&s=' . $_GET['s'] : '') . '&action=delete&session=' . $a["id"] . '">delete</a>' ?></td>
										</tr>
										<?php $counter++;
									}
								}
								?>

							</table>

							<?php
							echo pnp_pagination($totalitems, $perpage, 5, $paged, admin_url() . '?analytics=' . $_GET["analytics"] . (isset($_GET['perpage']) ? '&perpage=' . $_GET['perpage'] : '') . (isset($_GET['order_by']) ? '&order_by=' . $_GET['order_by'] : '') . (isset($_GET['s']) ? '&s=' . $_GET['s'] : ''));
							?>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins


		jQuery('.perpage').change(function () {
			jQuery(this).parent().submit();
		});

		jQuery('#gcheck .group-checkable').change(function () {
			var set = jQuery(this).attr("data-set");
			var checked = jQuery(this).is(":checked");
			jQuery(set).each(function () {
				if (checked) {
					jQuery(this).attr("checked", true);
				} else {
					jQuery(this).attr("checked", false);
				}
			});
			jQuery.uniform.update(set);
		});

		jQuery('#deleteall').click(function () {
			jQuery('#to_del_form').submit();
		});


	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
