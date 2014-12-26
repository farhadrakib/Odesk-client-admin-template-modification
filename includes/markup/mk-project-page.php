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
<?php
$original_project = $_GET["project"];
$_GET["project"] = rawurlencode($_GET["project"]);
?>
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
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/jqvmap.css" media="screen" rel="stylesheet"
	      type="text/css"/>
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
					<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>?project=<?php echo $_GET['project'] ?>"><i
								class="icon-th-list"></i> Dashboard</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?analytics=<?php echo $_GET['project'] ?>"><i
								class="icon-bar-chart"></i> User Sessions</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?hmaps=<?php echo $_GET['project'] ?>"><i
								class="icon-dashboard"></i> Heat Maps</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?ppages=<?php echo $_GET['project'] ?>"><i
								class="icon-star"></i> Popular Pages</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?mdata=<?php echo $_GET['project'] ?>"><i
								class="icon-hdd"></i> Manage Data</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?settings=<?php echo $_GET['project'] ?>"><i
								class="icon-cogs"></i> Settings</a></li>
					<li><a href="<?php echo admin_url() ?>?helpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>"><i class=" icon-comments-alt"></i>
							Support</a></li>
				</ul>
			</div>
			<div id="content">
				<?php
				global $wpdb;

				$pages_q = "SELECT COUNT( distinct `page_url`) FROM `" . T_PREFIX . 'clicks_' . $user->user_key . "` WHERE `project` = '" . $_GET['project'] . "'";
				$pages_count = $wpdb->queryUniqueValue($pages_q);

				$session_q = "SELECT COUNT( `session_id`) FROM `" . T_PREFIX . 'main_' . $user->user_key . "` WHERE `project` = '" . $_GET['project'] . "'";
				$session_count = $wpdb->queryUniqueValue($session_q);


				$delta_data_max = time();
				$delta_data_min = time() - ((7) * 24 * 60 * 60);
				$usr_uniq_q = "SELECT COUNT( distinct `user_id` ) FROM `" . T_PREFIX . 'main_' . $user->user_key . "` WHERE `project` = '" . $_GET['project'] . "' AND `session_start` >  " . ($delta_data_min) . " AND `session_start` <  " . ($delta_data_max);
				$usr_uniq_res = $wpdb->queryUniqueValue($usr_uniq_q);

				?>
				<div class="info-block">
					<div class="info-box green">
						<span>TOTAL VISITORS</span>
						<strong><?php echo $usr_uniq_res; ?></strong>
					</div>
					<div class="info-box orange">
						<span>TRACKING PAGES</span>
						<strong><?php echo $pages_count; ?></strong>
					</div>
					<div class="info-box blue">
						<span>TOTAL SESSIONS</span>
						<strong><?php echo $session_count; ?></strong>
					</div>
				</div>
				<div class="code-block">
					<span class="text">COPY HEAT MAP TRACKING CODE:</span>
					<strong class="title">Your Heat Map Tracking Code</strong>

					<div class="holder">
						<textarea class="code" rows="10"><?php include('views/mk-hmtrackerjs-project.php'); ?></textarea>
						<a class="btn-code" href="#">SELECT ALL</a>
					</div>
				</div>
				<div class="graphs-block">
					<div class="graph-box">
						<h2>20 Days Activity</h2>

						<div id="site_statistics_content" class="hide">
							<div id="site_statistics" class="chart"></div>
						</div>
					</div>
					<div class="graph-box">
						<h2>Regional Stats</h2>

						<div id="region_statistics_content" class="hide">
							<div class="btn-toolbar no-top-space clearfix">
								<div class="btn-group pull-right">
									<button class="btn btn-mini dropdown-t	oggle" data-toggle="dropdown">
										Select Region
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="javascript:;" id="regional_stat_world">World</a></li>
										<li><a href="javascript:;" id="regional_stat_usa">USA</a></li>
										<li><a href="javascript:;" id="regional_stat_europe">Europe</a></li>
										<li><a href="javascript:;" id="regional_stat_russia">Russia</a></li>
										<li><a href="javascript:;" id="regional_stat_germany">Germany</a></li>
									</ul>
								</div>
							</div>
							<div id="vmap_world" class="vmaps  chart hide"></div>
							<div id="vmap_usa" class="vmaps chart hide"></div>
							<div id="vmap_europe" class="vmaps chart hide"></div>
							<div id="vmap_russia" class="vmaps chart hide"></div>
							<div id="vmap_germany" class="vmaps chart hide"></div>
						</div>
					</div>
					<div id="content">
				<div class="analytics-block">
					<h2><span>HEAT MAP GENERATOR</span> Heat Maps</h2>

					<div class="table-holder">
						<h5>Top 10 <a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Top 10"
						              data-content="10 most popular pages. The percentage is based on the total viewing time.">lnk</a>
						</h5>

						<div id="pie" style="width:800px; height: 400px; margin: 0 auto">
							Pie Chart
						</div>
						<h5>Top 50 <a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Top 50"
						              data-content="50 most popular pages">lnk</a></h5>
						<?php
						global $wpdb;
						$popular_table = T_PREFIX . 'popular_' . $loggedin_user[2];
						$points_src = $wpdb->get_results("SELECT * FROM $popular_table WHERE `project` = '" . $_GET["project"] . "' ORDER BY `points` DESC LIMIT 50");
						$points_total = $wpdb->queryUniqueValue("SELECT sum(`points`) FROM $popular_table WHERE `project` = '" . $_GET["project"] . "'");

						foreach ($points_src as $key => $value) {
							?>
							<div class="rating">
								<div class="progress progress-info">
									<a href="<?php echo $value->page_url ?>" target="_blank"
									   class="l-up"><?php echo HMTrackerFN::sec2hms($value->points) ?>
										| <?php echo $value->page_url; ?></p></a>

									<div class="bar"
									     style="width: <?php echo round($value->points * 100 / $points_total); ?>%"><a
											href="<?php echo $value->page_url ?>" target="_blank"
											class="l-down"><?php echo HMTrackerFN::sec2hms($value->points) ?>
											| <?php echo $value->page_url ?></p></a></div>
								</div>
							</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			
				</div>
			</div>
			<!-- END PAGE CONTAINER-->
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
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/jquery.vmap.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.peity.min.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/flot/jquery.flot.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	<?php
		$coutnries_q = "SELECT distinct `user_id`, COUNT(`user_id`) as `count` FROM `".T_PREFIX.'main_'.$user->user_key."` WHERE `project` = '".$_GET["project"]."' group by `user_id`";
		$coutnries_res = $wpdb->get_results($coutnries_q);
		$coutnries_collection = array();
		$objGeoIP = new hmtracker_GeoIP();
		foreach ($coutnries_res as $coutnries_res_key => $coutnries_res_value) {
			$usrData = explode("~", $coutnries_res_value->user_id);

			$objGeoIP->search_ip($usrData[0]);
			if ($objGeoIP->found())
			{
				$c_code = strtolower($objGeoIP->getCountryCode());
				if(!isset($coutnries_collection[$c_code])) $coutnries_collection[$c_code] = 0;
				$coutnries_collection[$c_code] += $coutnries_res_value->count;
			}

		}
		$_data_str = '{';
		foreach ($coutnries_collection as $country_code => $country_count) {
			$_data_str .= '"'.$country_code.'":"'.$country_count.'",';
		}
		$_data_str .= '}';
		echo 'var sample_data = '.$_data_str.';';
	?>
	var activity = [];
	var dates = [];
	var time = [];
	var time_format = [];
	<?php
	if(isset($this -> PROJECTS[$_GET['project']]['settings']['opt_record_tz'])) date_default_timezone_set($this -> PROJECTS[$_GET['project']]['settings']['opt_record_tz']);
	for ($i=20, $j=0; $i > -2; $i--, $j++) {

		$date = date("m.d.y");

		if($i > -1) {
			$delta_data_max = strtotime((date("d")-$i)." ".(date("F"))." ".date("Y"));
			$delta_data_min = strtotime((date("d")-($i+1))." ".(date("F"))." ".date("Y"));
		} else {
			$delta_data_max = time();
			$delta_data_min = time() - (((date("H") * 60) + date("i")) * 60);
		}

		/*$delta_time = ((date("H") * 60) + date("i")) * 60;
		$delta_data_max = time() - ($i * 24 * 60 * 60);
		$delta_data_min = time() - (($i+1) * 24 * 60 * 60) - $delta_time;*/



		//Activity
		$act_q = "SELECT COUNT(*) FROM `".T_PREFIX.'main_'.$user->user_key."` WHERE `project` = '".$_GET["project"]."' AND `session_start` >  ".($delta_data_min)." AND `session_start` <  ".($delta_data_max);
		$time_q = "SELECT sum(`session_end` - `session_start`) FROM `".T_PREFIX.'main_'.$user->user_key."` WHERE `project` = '".$_GET["project"]."' AND `session_start` >  ".($delta_data_min)." AND `session_start` <  ".($delta_data_max);
		$act_res = $wpdb->queryUniqueValue($act_q);
		$time_res = $wpdb->queryUniqueValue($time_q);
		echo "activity[".$j."]=[".$j.",".$act_res."]; \n";
		echo "dates[".$j."]='".date("M d, Y",$delta_data_min)."'; \n";
		echo "time[".$j."]=[".$j.",".(empty($time_res)?0:$time_res/100)."]; \n";
		echo "time_format[".$j."]='".HMTrackerFN::sec2hms($time_res)."'; \n";
	}
	?>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();
		Index.initJQVMAP(); // init index page's custom scripts init index page's custom scripts
		Index.initCharts(); // init index page's custom scripts

                jQuery(".code-block textarea").focus(function() {
                    jQuery(this).select();
                });
                
		jQuery('.btn-code').click(function () {
			jQuery(this).parent().find('.code').focus().select();
			return false;
		})

		jQuery('#createproject').click(function () {
			if (jQuery('input[name="projectname"]').val() == "") {
				jQuery('input[name="projectname"]').focus();
				return false
			}
			if (jQuery('textarea[name="projectdescription"]').val() == "") {
				jQuery('textarea[name="projectdescription"]').focus();
				return false
			}

			var post = {}
			post.action = 'create';
			post.name = encodeURIComponent(jQuery('input[name="projectname"]').val());
			post.description = jQuery('textarea[name="projectdescription"]').val();

			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>?hmtrackeractions', post, function (data) {
				jQuery('#createproject').button('reset')
				if (data == 'ok') {
					location.reload();
				}
			});
		})
		jQuery('.delproject').click(function () {
			jQuery('#delprojtitle').text(jQuery(this).attr('data-value'));
			jQuery('#delprojectaction').attr('data-value', jQuery(this).attr('data-value'));
		})

		jQuery('#delprojectaction').click(function () {
			var post = {}
			post.action = 'delete';
			post.name = jQuery(this).attr('data-value');

			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>?hmtrackeractions', post, function (data) {
				jQuery('#createproject').button('reset')
				if (data == 'ok') {
					location.reload();
				}
			});
		})


	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
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
global $loggedin_user;
$user = current_user();
$cur_status_id = detect_user_status($user);
$ui_enabled = validate_user_status($cur_status_id);
if (!$ui_enabled) die("Subscription status issue");
?>
<?php $_GET["project"] = rawurlencode($_GET["project"]); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


<script type="text/javascript" src="<?php echo $this->PLUGIN_URL ?>js/jquery.flot.pie.js"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();

		//popular pages
		<?php
		//manage data
		if(isset($_POST['manage']) && $_POST['manage'] == "tables"){
			
			switch($_POST['what']){
				case 'sessions':
					$table2 = T_PREFIX.'main_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["project"]."' AND  session_time >= '".strtotime($_POST['from'])."' AND session_time <= '".(strtotime($_POST['to'])+82800+3599)."'");
					break;
				case 'clicks':
					$table2 = T_PREFIX.'clicks_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["project"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
				case 'eye':
					$table2 = T_PREFIX.'mmove_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["project"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
				case 'scroll':
					$table2 = T_PREFIX.'scroll_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["project"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
			}
			
		}
		echo $points_total;
		?>

		var data = [
			<?php
					$total = 9;
					foreach ($points_src as $key => $value) {
						if($total < 0) break;
					?>
			{ label: "<?php echo ($value->page_url)  ?>", data: <?php echo round($value->points*100/$points_total); ?>},
			<?php
				$total--;
			}
			?>
		];

		jQuery.plot(jQuery("#pie"), data,
			{
				series: {
					pie: {
						show: true,
						offset: {
							left: -200
						},
						radius: 0.8,
						label: {
							show: true,
							radius: 1,
							formatter: function (label, series) {
								return '<div style="font-size:8pt;text-align:center;padding:2px;">' + Math.round(series.percent) + '%</div>';
							},
							background: { opacity: 0.8 }
						}
					}
				}, grid: {
				hoverable: true,
				clickable: true
			}
			});

	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>


