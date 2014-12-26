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
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
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
					<li><a href="<?php echo $this->PLUGIN_URL ?>"><i class="icon-th-list"></i> Projects</a></li>
					<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>?upayments"><i class="icon-money"></i>
							Payments</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?helpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>"><i class=" icon-comments-alt"></i>
							Support</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?usersettings"><i class=" icon-wrench"></i> Account
							Settings</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="analytics-block">
					<h2><span><a href="<?php echo $this->PLUGIN_URL ?>?upayments"><i class="icon-money"></i>
								Payments</a> &gt; Change Package</span> Change Package</h2>

					<div class="table-holder">


						<div class="form-wizard">
							<div class="navbar steps">
								<div class="navbar-inner" style="padding-top: 10px">
									<ul class="row-fluid">
										<?php
										$first = "active";
										$second = "";
										$third = "";

										if (isset($_POST['package_id'])) {
											$first  = " ";
											$second = "active";
										}

										?>
										<li class="span4 <?php echo $first ?>">
											<img
												src="<?php echo $this->PLUGIN_URL ?>/images/pay-choose.png" <?php if ($first == "") echo 'style="opacity: 0.5"' ?> />
										</li>
										<li class="span4 <?php echo $second ?>">
											<img
												src="<?php echo $this->PLUGIN_URL ?>/images/pay-edit.png" <?php if ($second == "") echo 'style="opacity: 0.5"' ?> />
										</li>
										<li class="span4 <?php echo $third ?>">
											<img
												src="<?php echo $this->PLUGIN_URL ?>/images/pay-confirm.png" <?php if ($third == "") echo 'style="opacity: 0.5"' ?> />
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div>
							<?php
							$user = current_user();
							$calc = init_balance_calculator($user);

							if (isset($_POST['package_id'])) { //step 2
								//post vars
								$pack_id            = $_POST['package_id'];
								$extradomains_count = hmt_isset($_POST['extradomains'], 0);
								$pay_interval       = hmt_isset($_POST['pay_interval'], 'weekly');
								$prev_plan_id       = $_POST['prev_plan_id'];
								$trial_amount       = $_POST['trial_amount'];
								$trial_shift        = $_POST['trial_shift'];
								$order_total        = $_POST['order_total'];
								//print_r(compact('trial_amount', 'trial_shift', 'order_total'));
								//create "inactive" plan and save it.
								$now                        = time();
								$plan                       = get_plan_for($this->PACKAGES[$pack_id], $pack_id);
								$is_plan_free               = is_plan_free($plan);
								$plan['extradomains_count'] = $extradomains_count;
								$plan['pay_interval']       = $pay_interval;
								$plan['start_date']         = $now;
								$plan['end_date']           = 0;

								ensure_plans_unserialized($user);
								$activePlan             = get_active_plan($user);
								$activePlan['end_date'] = $now;
								replace_plan($user->plans, $activePlan);
								$user->plans[] = $plan;
								if($is_plan_free) {
									$user->status = 7;
								}
								update_user($user);
								if($is_plan_free) {
									header("Location: " . home_url() . "?upayments");
									die();
								} else {
									include($this->MARKUP_PATH . 'forms/frm-submit-plan.php');
								}
							} else { //step 1
								$plan        = get_active_plan($user);
								$balance     = $calc->Balance();
								$plan_cost   = $calc->CalcPlanPayment($plan);
								$form_action = $this->PLUGIN_URL . '?changepackage';
								include($this->MARKUP_PATH . 'forms/frm-change-plan.php');
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

	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"
	        type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
	        type="text/javascript"></script>
	<!--[if lt IE 9]>
	<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/excanvas.js"></script>
	<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/respond.js"></script>
	<![endif]-->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js"
	        type="text/javascript"></script>
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

			//del package
			jQuery('.deluser').click(function () {
				jQuery('#deluseraction').attr('href', jQuery(this).attr('to-del'));
			})


		});
	</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
