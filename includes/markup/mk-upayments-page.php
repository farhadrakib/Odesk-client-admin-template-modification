<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
} ?>
<?php
global $loggedin_user;
if ( ! is_user_logged_in( $loggedin_user ) && IS_KEY_VALID ) {
	header( 'location: ' . admin_url() . '?login' );
} ?>
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
				<em class="date"><?php echo date( "F d, Y" ) ?></em>
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
					<h2><span>VIEW PAYMENTS LIST</span> Payments</h2>

					<div class="table-holder">


						<?php
						$user      = current_user();
						$updackage = get_active_plan( $user );
						?>



						<form action="#" method="POST" class="form-horizontal" id="">
							<div class="control-group">
								<label class="control-label"></label>

								<div class="controls">
									<h4>Package "<?php echo ( $updackage ) ? $updackage["title"] : "Free"; ?>"</h4>
									<?php show_status( true ); ?>
								</div>
							</div>
							<?php if ( $updackage ) { ?>
								<div class="control-group">
									<label class="control-label">Domains</label>

									<div class="controls">
										<div class="input-append input-mini">
										<span class="add-on"><strong><?php echo $updackage["pack_domains"];
												echo " + " . ( $updackage['extradomains_count'] ) . " extra domains " ?></strong></span>
										</div>
									</div>
								</div>
								<?php
								$user_status = detect_user_status();
								if ( $user_status != 7 ) {
									?>
									<div class="control-group">
										<label class="control-label">Payments</label>

										<div class="controls">
											<div class="input-append input-mini">
										<span
											class="add-on"><strong><?php echo ucfirst( $updackage["pay_interval"] ); ?></strong> (<?php echo "{$updackage['cost_' . $updackage["pay_interval"]]} {$updackage['currency_code']}"; ?>
											+ <?php echo $updackage['extradomains_count'] * $updackage[ 'extradomain_' . $updackage["pay_interval"] ] . " {$updackage['currency_code']}"; ?>
											)</span>
											</div>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
							<div class="control-group">
								<label class="control-label"></label>

								<div class="controls">
									<!-- a target="_blank" href="<?php echo $this->PAYPAL_URL ?>?cmd=_manage-paylist" class="btn btn-primary btn-mini">Manage Subscriptions</a-->
									<?php if(has_available_packages($this->PACKAGES, $this->PROJECTS)) { ?>
										<a href="<?php echo $this->PLUGIN_URL ?>?changepackage" class="btn btn-primary btn-mini">Change Package</a>
									<?php } ?>
									<?php if ( $updackage && $user_status != 7 ) { ?>
										<a href="#cancelModal" class="btn btn-danger btn-mini" data-toggle="modal">Cancel Subscription</a>
									<?php } ?>
								</div>
							</div>
						</form>

						<?php ?>
						<label class="pull-right">
							<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="perpage">
								<input type="hidden" name="upayments" value=""/>
								<input type="hidden" name="order_by"
								       value="<?php echo ( isset( $_GET["order_by"] ) ) ? $_GET["order_by"] : "session_start" ?>"/>
								<input type="hidden" name="s"
								       value="<?php echo ( isset( $_GET["s"] ) ) ? $_GET["s"] : "" ?>"/>
								per page <select size="1" name="perpage" class="input-small perpage"
								                 style="margin-bottom: 0">
									<?php
									for ( $i = 10; $i < 510; $i += 10 ) {
										?>
										<option
											value="<?php echo $i ?>" <?php if ( ( isset( $_GET['perpage'] ) && $i == $_GET['perpage'] ) || ( ! isset( $_GET['perpage'] ) && $i == 30 ) ): ?> selected="selected"<?php endif; ?>><?php echo $i ?></option>
									<?php } ?>
								</select>
							</form>
						</label>


						<form id="to_del_form"
						      action="<?php echo admin_url() . '?upayments=' . ( isset( $_GET['perpage'] ) ? '&perpage=' . $_GET['perpage'] : '' ) . ( isset( $_GET['paged'] ) ? '&paged=' . $_GET['paged'] : '' ) ?>"
						      method="post">

							<table border="0" width="100%" cellpadding="0" cellspacing="0"
							       class="table table-bordered table-striped bs-table" id="gcheck">
								<tr>
									<th class=" minwidth-1"><span>Id</span></th>
									<th class=" minwidth-1"><span>Amount</span></th>
									<th class=" minwidth-1"><span>Status</span></th>
									<th class=" minwidth-1"><span>Type</span></th>
									<th class=" minwidth-1"><span>Date</span></th>
								</tr>
								<?php

								global $wpdb;
								$search = " WHERE `user_id` = '" . $user->id . "' AND `txn_type`  IN('payment','refund', 'manual')";
								$q      = "SELECT * FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_payments'] . "` " . $search;

								//pagination
								$perpage = ( isset( $_GET['perpage'] ) ) ? $_GET['perpage'] : 30;

								$totalitems = $wpdb->get_var( "SELECT COUNT(*) FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_payments'] . "` " . $search );
								$paged      = ! empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';


								if ( ! empty( $paged ) && ! empty( $perpage ) ) {
									$offset = $paged;
									$q .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
								} else {
									$q .= ' LIMIT ' . (int) $perpage;
								}
								$r = $wpdb->query( $q );


								$nr = $wpdb->numRows( $r );


								$counter = 0;
								if ( $nr > 0 ) {
									while ( $a = mysql_fetch_assoc( $r ) ) {

										//build table row
										?>
										<tr class="rows <?php echo $a["id"]; ?>">
											<td> <?php echo $a["txnid"]; ?></td>
											<td> <?php echo $a["payment_amount"]; ?></td>
											<td> <?php echo $a["payment_status"]; ?></td>
											<td> <?php echo $a["txn_type"]; ?></td>
											<td> <?php echo $a["createdtime"]; ?></td>
										</tr>
										<?php $counter ++;
									}
								}
								?>

							</table>

							<?php
							echo pnp_pagination( $totalitems, $perpage, 5, $paged, admin_url() . '?upayments=' . ( isset( $_GET['perpage'] ) ? '&perpage=' . $_GET['perpage'] : '' ) . ( isset( $_GET['order_by'] ) ? '&order_by=' . $_GET['order_by'] : '' ) . ( isset( $_GET['s'] ) ? '&s=' . $_GET['s'] : '' ) );
							?>
						</form>


					</div>
				</div>
			</div>
		</div>
		<br/>
		<?php echo date( "Y" ) ?> &copy; <?php echo $this->OPTIONS['brandname'] ?>
		v. <?php echo $this->OPTIONS['version']; ?>
	</div>
</div>
<div id="cancelModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="projectModal"
     aria-hidden="true">
	<div class="modal-header">
		<h3 id="myModalLabel3">Cancel Subscription</h3>
	</div>
	<div class="modal-body">
		<h4>Canceling a recurring payment, subscription or automatic billing agreement</h4>
		<ol>
			<li>Log in to your PayPal account.</li>
			<li>Click <strong>Profile</strong> near the top of the page.</li>
			<li>Click <strong>My money</strong>.</li>
			<li>Click <strong>Update</strong> in the <strong>My preapproved payments</strong> section.</li>
			<li>Click <strong>Cancel</strong>, <strong>Cancel automatic billing</strong>, or <strong>Cancel
					subscription</strong> and follow the instructions.
			</li>
		</ol>
	</div>
	<div class="modal-footer">
		<a class="btn btn-primary width-auto" data-dismiss="modal" aria-hidden="true">Close</a>
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
