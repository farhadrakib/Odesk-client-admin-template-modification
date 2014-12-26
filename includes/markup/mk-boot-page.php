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
<?php
$user                = current_user();
$active_plan         = get_active_plan( $user );
$max_domains_reached = count( $this->PROJECTS ) >= $active_plan['pack_domains'] + $active_plan['extradomains_count'];
$cur_status_id       = detect_user_status( $user );
$ui_enabled          = validate_user_status( $cur_status_id );
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
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"/>
</head>
<body>
<div id="wrapper">
<div id="header">
	<strong class="logo"><a href="<?php echo $this->PLUGIN_URL ?>"><img
				src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
	<ul id="nav">
		<?php
		if ( isset( $_SESSION['return_to_admin'] ) && $_SESSION['return_to_admin'] ) {
			?>
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
			<em class="date"><?php echo @date( "F d, Y" ) ?></em>

			<div class="pull-right">&nbsp;</div>
			<div class="pull-right"><?php show_status( true ); ?> </div>
		</div>
		<div id="sidebar">
			<ul class="sidenav">
				<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>"><i class="icon-th-list"></i>
						Projects</a></li>
				<li><a href="<?php echo $this->PLUGIN_URL ?>?upayments"><i class="icon-money"></i> Payments</a></li>
				<li><a href="<?php echo $this->PLUGIN_URL ?>?helpvideos"><i class="icon-facetime-video"></i> Help
						Videos</a></li>
				<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>"><i class=" icon-comments-alt"></i>
						Support</a></li>
				<li><a href="<?php echo $this->PLUGIN_URL ?>?usersettings"><i class=" icon-wrench"></i> Account
						Settings</a></li>
			</ul>
		</div>
		<div id="content-holder">
			<div class="analytics-block">
				<h2><span>VIEW AND MANAGE PROJECTS</span>
					Projects
				</h2>

				<?php if ( is_array( $this->PROJECTS ) && count( $this->PROJECTS ) > 0 ) {
					if ( ( $ui_enabled && ! $max_domains_reached ) || $user->status == 6 || $user->status == 8 ) {
						?>
						<a href="#projectModal" role="button" class="btn btn-primary btn-mini width-auto"
						   data-toggle="modal">
							+ Create
						</a>
						<br/><br/>
					<?php } else { ?>
						<div class="control-group">
							<div class="label label-warning">
								Your domain limit has been reached. Please click
								<a href="<?php echo $this->PLUGIN_URL ?>?changepackage">Change Package</a> to upgrade
							</div>
						</div>

					<?php } ?>
				<?php } ?>

				<div id="content">

					<?php
					if ( $this->PROJECTS === false ) {
						$this->PROJECTS = array();
					}
					if ( count( $this->PROJECTS ) < 1 ) {
						?>
						<div class="form-wizard">
							<div class="navbar steps">
								<div style="padding-top: 10px">
									<ul class="row-fluid">
										<li class="span5 <?php if ( count( $this->PROJECTS ) == 0 ) {
											echo 'active';
										} ?> ">
											<img
												src="<?php echo $this->PLUGIN_URL ?>/images/p-setup.png" <?php if ( count( $this->PROJECTS ) == 1 ) {
												echo 'style="opacity: 0.5"';
											} ?> />
										</li>
										<li class="span5 <?php if ( count( $this->PROJECTS ) == 1 ) {
											echo 'active';
										} ?> ">
											<img
												src="<?php echo $this->PLUGIN_URL ?>/images/c-setup.png" <?php if ( count( $this->PROJECTS ) == 0 ) {
												echo 'style="opacity: 0.5"';
											} ?> />
										</li>
									</ul>
								</div>
							</div>
							<div class="tab-content">
								<div class="tab-pane <?php if ( count( $this->PROJECTS ) == 0 ) {
									echo 'active';
								} ?>"
								     id="tab1">
									<form class="form-horizontal no-padding no-margin" method="post"
									      action="<?php echo admin_url() ?>?hmtrackeractions">
										<input type="hidden" name="action" value="create"/>

										<div class="control-group">
											<label class="control-label"></label>

											<div class="controls">
												<h4 class="center">Enter project details</h4>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Project Name</label>

											<div class="controls">
												<input style="width: 490px" type="text" name="projectname2"
												       required/>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Project Description</label>

											<div class="controls">
												<textarea style="width: 490px" class="input-xlarge" rows="3"
												          name="projectdescription2" required></textarea>
											</div>
										</div>
										<div class="form-actions clearfix">
											<button type="button"
											        class="btn btn-primary <?php echo( $ui_enabled ? "createproject2 " : "" ); ?>width-auto"<?php echo( $ui_enabled ? "" : " disabled" ); ?>>
												Continue
											</button>
										</div>
									</form>
								</div>
								<div class="tab-pane <?php if ( count( $this->PROJECTS ) == 1 ) {
									echo 'active';
								} ?>"
								     id="tab2">
									<h4></h4>
									<h4>Please complete step 1</h4>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<?php global $wpdb; ?>
						<?php foreach ( $this->PROJECTS as $key => $value ) { ?>
							<div class="project-block">
								<?php
								$pages_q     = "SELECT COUNT( distinct `page_url`) FROM `" . T_PREFIX . 'clicks_' . $user->user_key . "` WHERE `project` = '" . $key . "'";
								$pages_count = $wpdb->queryUniqueValue( $pages_q );

								$session_q     = "SELECT COUNT( `session_id`) FROM `" . T_PREFIX . 'main_' . $user->user_key . "` WHERE `project` = '" . $key . "'";
								$session_count = $wpdb->queryUniqueValue( $session_q );


								$delta_data_max = time();
								$delta_data_min = time() - ( ( 7 ) * 24 * 60 * 60 );
								$usr_uniq_q     = "SELECT COUNT( distinct `user_id` ) FROM `" . T_PREFIX . 'main_' . $user->user_key . "` WHERE `project` = '" . $key . "' AND `session_start` >  " . ( $delta_data_min ) . " AND `session_start` <  " . ( $delta_data_max );
								$usr_uniq_res   = $wpdb->queryUniqueValue( $usr_uniq_q );

								?>
								<div class="sec-head">
									<strong
										class="title"><span>PROJECT:</span>  <?php echo rawurldecode( $key ) ?> <?php if ( $value['description'] != "" ): ?> (<?php echo $value['description']; ?>) <?php endif; ?>
									</strong>
									<ul class="sub-links">
										<?php if ( $ui_enabled ) { ?>
											<li><a href="<?php echo admin_url() ?>?project=<?php echo $key ?>"
											       data-toggle="modal">PROJECT DASHBOARD</a></li>
											<li><a href="<?php echo admin_url() ?>?settings=<?php echo $key ?>"
											       data-toggle="modal">SETTINGS</a></li>
											<li><a href="#delproject" role="button" class="delproject"
											       data-toggle="modal" data-value="<?php echo $key ?>">DELETE
													PROJECT</a></li>
										<?php } ?>
										<?php if ( ! $ui_enabled ) { ?>
											<li><a href="#">PROJECT DASHBOARD</a></li>
											<li><a href="#">SETTINGS</a></li>
											<li><a href="#">DELETE PROJECT</a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="info-block">
									<div class="info-box green">
										<span>TOTAL VISITORS</span>
										<strong><?php echo $usr_uniq_res; ?></strong>
									</div>
									<div class="info-box orange">
										<span>TRACKING PAGES</span>
										<strong><?php echo ( empty( $pages_count ) ) ? 0 : $pages_count; ?></strong>
									</div>
									<div class="info-box blue">
										<span>TOTAL SESSIONS</span>
										<strong><?php echo $session_count; ?></strong>
									</div>
								</div>
								<div class="code-block">
									<div class="holder">
										<textarea class="code"
										          rows="1"><?php include( 'views/mk-hmtrackerjs-boot.php' ); ?></textarea>
										<a class="btn-code" href="#">SELECT ALL</a>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<br/>
	<?php echo @date( "Y" ) ?> &copy; <?php echo $this->OPTIONS['brandname'] ?>
	v. <?php echo $this->OPTIONS['version']; ?>
</div>
</div>

<!-- MODALS-->
<div id="projectModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="projectModal"
     aria-hidden="true">
	<div class="modal-header">
		<h3 id="myModalLabel3">New Project</h3>
	</div>
	<div class="modal-body">
		<p>&nbsp;</p>

		<form class="form-horizontal no-padding no-margin" method="post"
		      action="<?php echo admin_url() ?>?r_key=<?php echo $_GET['rkey'] ?>&restoreit">
			<div class="control-group">
				<label class="control-label">Project Name</label>

				<div class="controls">
					<input style="width: 290px" type="text" name="projectname" required/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Project Description</label>

				<div class="controls">
					<textarea style="width: 290px" class="input-xlarge" rows="3" name="projectdescription"
					          required></textarea>
				</div>
			</div>
		</form>

	</div>
	<div class="modal-footer">
		<a class="btn btn-primary width-auto" data-dismiss="modal" aria-hidden="true">Close</a>
		<?php if ( $ui_enabled ): ?><a class="btn btn-primary width-auto createproject">Create</a><?php endif; ?>
		<?php if ( ! $ui_enabled ): ?><a class="btn btn-primary width-auto">Create</a><?php endif; ?>
	</div>
</div>
<div id="delproject" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
     aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel1">Delete <span id="delprojtitle"></span></h3>
	</div>
	<div class="modal-body">
		<p>Please confirm deleting this project and all its data</p>
	</div>
	<div class="modal-footer">
		<a class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</a>
		<a class="btn btn-primary" id="delprojectaction" data-value="">Delete</a>
	</div>
</div>
<div id="delprojectdata" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3"
     aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel3">Delete All Data for <span id="delprojdatatitle"></span></h3>
	</div>
	<div class="modal-body">
		<p>Please confirm deleting project data</p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		<button id="delprojectdataaction" class="btn btn-danger" data-value="">Delete</button>
	</div>
</div>
<div id="help_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
     aria-hidden="true" style="width: 828px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel1">Help Video</h3>
	</div>
	<div class="modal-body" style=" max-height:650px; overflow: hidden">
		<iframe width="800" height="600" src="//www.youtube.com/embed/hThHYK9qc5c" frameborder="0"
		        allowfullscreen></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- MODALS END-->


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
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.peity.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/form-wizard.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();
		Index.initPeityElements(); // init pierty elements
		FormWizard.init();

		jQuery(".code-block textarea").focus(function () {
			jQuery(this).select();
		});

		jQuery('.btn-code').click(function () {
			jQuery(this).parent().find('.code').focus().select();
			return false;
		})

		jQuery('.createproject').click(function () {
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


		jQuery('textarea[name="projectdescription2"]').keyup(function () {
			var $th = jQuery(this);
			$th.val($th.val().replace(/[^a-zA-Z0-9 ,.\n]/g, function (str) {
				$th.popover({
					title: 'You typed " ' + str + ' "',
					content: "Please use only letters and numbers"
				}).popover("show");
				setTimeout(function () {
					jQuery('textarea[name="projectdescription2"]').popover("hide").popover('destroy');
				}, 2000);
				return '';
			}));
		});
		jQuery('textarea[name="projectdescription"]').keyup(function () {
			var $th = jQuery(this);
			$th.val($th.val().replace(/[^a-zA-Z0-9 ,.\n]/g, function (str) {
				$th.popover({
					title: 'You typed " ' + str + ' "',
					content: "Please use only letters and numbers",
					placement: 'top'
				}).popover("show");
				setTimeout(function () {
					jQuery('textarea[name="projectdescription"]').popover("hide").popover('destroy');
				}, 2000);
				return '';
			}));
		});

		jQuery('input[name="projectname2"]').keyup(function () {
			var $th = jQuery(this);
			$th.val($th.val().replace(/[^a-zA-Z0-9 ,.\n&]/g, function (str) {
				$th.popover({
					title: 'You typed " ' + str + ' "',
					content: "Please use only letters and numbers"
				}).popover("show");
				setTimeout(function () {
					jQuery('input[name="projectname2"]').popover("hide").popover('destroy');
				}, 2000);
				return '';
			}));
		});

		jQuery('input[name="projectname"]').keyup(function () {
			var $th = jQuery(this);
			$th.val($th.val().replace(/[^a-zA-Z0-9 ,.\n&]/g, function (str) {
				$th.popover({
					title: 'You typed " ' + str + ' "',
					content: "Please use only letters and numbers",
					placement: 'bottom'
				}).popover("show");
				setTimeout(function () {
					jQuery('input[name="projectname"]').popover("hide").popover('destroy');
				}, 2000);
				return '';
			}));
		});


		jQuery('.createproject2').click(function () {
			if (jQuery('input[name="projectname2"]').val() == "") {
				jQuery('input[name="projectname2"]').focus();
				return false
			}
			if (jQuery('textarea[name="projectdescription2"]').val() == "") {
				jQuery('textarea[name="projectdescription2"]').focus();
				return false
			}

			var post = {}
			post.action = 'create';
			post.name = encodeURIComponent(jQuery('input[name="projectname2"]').val());
			post.description = jQuery('textarea[name="projectdescription2"]').val();

			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>?hmtrackeractions', post, function (data) {
				jQuery('#createproject').button('reset')
				if (data == 'ok') {
					location.reload();
				}
			});
		})
		jQuery('.delproject').click(function () {
			jQuery('#delprojtitle').text(decodeURIComponent(jQuery(this).attr('data-value')));
			jQuery('#delprojectaction').attr('data-value', jQuery(this).attr('data-value'));
		})
		jQuery('.delprojectdata').click(function () {
			jQuery('#delprojdatatitle').text(decodeURIComponent(jQuery(this).attr('data-value')));
			jQuery('#delprojectdataaction').attr('data-value', jQuery(this).attr('data-value'));
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


		jQuery('#delprojectdataaction').click(function () {
			var post = {}
			post.action = 'deletedata';
			post.name = jQuery(this).attr('data-value');

			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>?hmtrackeractions', post, function (data) {
				jQuery('#createproject').button('reset')
				if (data == 'ok') {
					location.reload();
				}
			});
		})
		jQuery('.help-ico').popover({'placement': 'right'});

	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
