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
<?php $_GET["ppages"] = rawurlencode($_GET["ppages"]); ?>
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
					<li><a href="<?php echo $this->PLUGIN_URL ?>?project=<?php echo $_GET['ppages'] ?>"><i
								class="icon-th-list"></i> Dashboard</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?analytics=<?php echo $_GET['ppages'] ?>"><i
								class="icon-bar-chart"></i> User Sessions</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?hmaps=<?php echo $_GET['ppages'] ?>"><i
								class="icon-dashboard"></i> Heat Maps</a></li>
					<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>?ppages=<?php echo $_GET['ppages'] ?>"><i
								class="icon-star"></i> Popular Pages</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?mdata=<?php echo $_GET['ppages'] ?>"><i
								class="icon-hdd"></i> Manage Data</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?settings=<?php echo $_GET['ppages'] ?>"><i
								class="icon-cogs"></i> Settings</a></li>
					<li><a href="<?php echo admin_url() ?>?helpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank"><i
								class=" icon-comments-alt"></i> Support</a></li>
				</ul>
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
						$points_src = $wpdb->get_results("SELECT * FROM $popular_table WHERE `project` = '" . $_GET["ppages"] . "' ORDER BY `points` DESC LIMIT 50");
						$points_total = $wpdb->queryUniqueValue("SELECT sum(`points`) FROM $popular_table WHERE `project` = '" . $_GET["ppages"] . "'");

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
<script type="text/javascript" src="<?php echo $this->PLUGIN_URL ?>js/jquery.flot.js"></script>
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
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["ppages"]."' AND  session_time >= '".strtotime($_POST['from'])."' AND session_time <= '".(strtotime($_POST['to'])+82800+3599)."'");
					break;
				case 'clicks':
					$table2 = T_PREFIX.'clicks_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["ppages"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
				case 'eye':
					$table2 = T_PREFIX.'mmove_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["ppages"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
				case 'scroll':
					$table2 = T_PREFIX.'scroll_'.$loggedin_user[2];
					$wpdb->get_results("DELETE FROM $table2 WHERE `project` = '".$_GET["ppages"]."'  date >= '$_POST[from]' AND date <= '$_POST[to]'");
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
			{ label: "<?php echo ((strlen($value->page_url) > 73) ? substr($value->page_url,0,70).'...' : $value->page_url)  ?>", data: <?php echo round($value->points*100/$points_total); ?>},
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
