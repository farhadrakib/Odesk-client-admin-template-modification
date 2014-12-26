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
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"/>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<strong class="logo"><a href="<?php echo $this->PLUGIN_URL ?>"><img
					src="<?php echo $this->OPTIONS['brandlogo'] ?>" alt="logo"/></a></strong>
		<ul id="nav">
			<li><a href="<?php echo $this->PLUGIN_URL ?>?adminsettings">Admin Dashboard</a></li>
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
					<li><a href="<?php echo $this->PLUGIN_URL ?>"><i class="icon-group"></i> Users</a></li>
					<li><a href="<?php echo $this->PLUGIN_URL ?>?payments"><i class="icon-money"></i> Payments</a></li>
					<li><a class="active" href="<?php echo $this->PLUGIN_URL ?>?packages"><i class="icon-briefcase"></i>
							Package Manager</a></li>
					<li><a href="<?php echo admin_url() ?>?devhelpvideos"><i class="icon-facetime-video"></i> Help
							Videos</a></li>
					<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>"><i class=" icon-comments-alt"></i>
							Support</a></li>
					<li><a href="<?php echo admin_url() ?>?adminsettings"><i class=" icon-wrench"></i> Admin
							Settings</a></li>
					<li><a href="<?php echo admin_url() ?>?about"><i class=" icon-info-sign"></i> About This
							Software</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="analytics-block">
					<h2><span><a href="<?php echo $this->PLUGIN_URL ?>?packages">Packages</a> &gt; Edit</span>

						Edit Package</h2>

					<div class="table-holder">
						<form action="#" method="POST" class="form-horizontal" id="settings_form">

							<div class="control-group">
								<label class="control-label">Package Title</label>

								<div class="controls">
									<input type="text" id="ptitle" class=""
									       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['title'] ?>"/>
								</div>
							</div>


							<div class="control-group">
								<label class="control-label">Domain Count</label>

								<div class="controls">
									<input style="width: 50px" id="pdomains" min="1" max="999" step="1"
									       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['domains'] ?>"
									       type="number">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Currency</label>

								<div class="controls">
									<select id="currency_code" readonly>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "USD" ? "selected='selected'" : "") ?>>
											USD
										</option>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "EUR" ? "selected='selected'" : "") ?>>
											EUR
										</option>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "GBP" ? "selected='selected'" : "") ?>>
											GBP
										</option>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "AUD" ? "selected='selected'" : "") ?>>
											AUD
										</option>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "CHF" ? "selected='selected'" : "") ?>>
											CHF
										</option>
										<option <?php echo($this->PACKAGES[$_GET["editpackage"]]['currency_code'] == "CAD" ? "selected='selected'" : "") ?>>
											CAD
										</option>
									</select>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Weekly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pweekly" min="1" max="99999" step="1"
									       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['weekly'] ?>"
									       type="number">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Biweekly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pbiweekly" min="1" max="99999" step="1"
									       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['biweekly'] ?>"
									       type="number">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Monthly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pmonthly" min="1" max="99999" step="1"
									       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['monthly'] ?>"
									       type="number">
								</div>
							</div>
							<?php
							$extra_enabled = $this->PACKAGES[$_GET["editpackage"]]['extradomains_enabled'];
							$collapse_in = $extra_enabled ? ' in' : '';
							?>
							<div class="control-group">
								<div class="accordion" id="accordion1" style="height: auto;">
									<div class="accordion-group">
										<div class="accordion-heading">
											<a id="pextradomains_toggle" class="accordion-toggle" data-toggle="collapse"
											   data-parent="#accordion1" href="#collapse_1">
												<i class="<?php echo $extra_enabled ? 'icon-ok-circle' : 'icon-off' ?>"></i>
												<span>Extra Domains <?php echo $extra_enabled ? 'Enabled' : 'Disabled' ?></span>
											</a>
										</div>
										<div id="collapse_1" class="accordion-body collapse<?php echo $collapse_in; ?>">
											<div class="accordion-inner" style="padding-left:0px;">
												<div class="control-group">
													<label class="control-label">Weekly Cost</label>

													<div class="controls">
														<input style="width: 50px" id="pextradomain_weekly" min="1"
														       max="999" step="1"
														       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['extradomain_weekly'] ?>"
														       type="number">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label">Biweekly Cost</label>

													<div class="controls">
														<input style="width: 50px" id="pextradomain_biweekly" min="1"
														       max="999" step="1"
														       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['extradomain_biweekly'] ?>"
														       type="number">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label">Monthly Cost</label>

													<div class="controls">
														<input style="width: 50px" id="pextradomain_monthly" min="1"
														       max="999" step="1"
														       value="<?php echo $this->PACKAGES[$_GET["editpackage"]]['extradomain_monthly'] ?>"
														       type="number">
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="form-actions">
								<button type="button" class="btn btn-primary width-auto save-button"
								        data-loading-text="Saving...">
									Save Package
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
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();

		jQuery('.help-ico').popover({'placement': 'right'});
		jQuery('#pextradomains_toggle').click(function () {
			var icon = jQuery(this).children('i');
			var wasOk = icon.hasClass('icon-ok-circle');
			icon.removeClass('icon-ok-circle icon-off');
			icon.addClass(wasOk ? 'icon-off' : 'icon-ok-circle');

			var title = jQuery(this).children('span');
			title.text(wasOk ? 'Extra Domains Disabled' : 'Extra Domains Enabled');
			return true;
		});

		//save settings
		jQuery('.save-button').click(function () {

			jQuery("#settings_form .alert").remove();
			//validating
			var isvalid = true;
			if (jQuery('#pmonthly').val() <= 0 && jQuery('#pbiweekly').val() <= 0 && jQuery('#pweekly').val() <= 0) {
				if (jQuery('#pmonthly').val() <= 0) {
					jQuery('#pmonthly').focus();
					isvalid = false;
				}
				if (jQuery('#pbiweekly').val() <= 0) {
					jQuery('#pbiweekly').focus();
					isvalid = false;
				}
				if (jQuery('#pweekly').val() <= 0) {
					jQuery('#pweekly').focus();
					isvalid = false;
				}
			}
			if (jQuery('#pdomains').val() <= 0) {
				jQuery('#pdomains').focus();
				isvalid = false;
			}
			if (jQuery('#ptitle').val() == "") {
				jQuery('#ptitle').focus();
				isvalid = false;
			}

			var hasextra = jQuery('#pextradomains_toggle').children('i').hasClass('icon-ok-circle');
			if (hasextra) {
				if (jQuery('#pweekly').val() > 0 && jQuery('#pextradomain_weekly').val() <= 0) {
					jQuery('#pextradomain_weekly').focus();
					isvalid = false;
				}
				if (jQuery('#pbiweekly').val() > 0 && jQuery('#pextradomain_biweekly').val() <= 0) {
					jQuery('#pextradomain_biweekly').focus();
					isvalid = false;
				}
				if (jQuery('#pmonthly').val() > 0 && jQuery('#pextradomain_monthly').val() <= 0) {
					jQuery('#pextradomain_monthly').focus();
					isvalid = false;
				}
			}


			if (!isvalid) {
				jQuery("#settings_form").prepend(alerter("Please fix next field", 1));
				return false;
			}

			//post var
			var post = {};
			post.title = jQuery('#ptitle').val();
			post.domains = jQuery('#pdomains').val();
			post.weekly = jQuery('#pweekly').val();
			post.biweekly = jQuery('#pbiweekly').val();
			post.monthly = jQuery('#pmonthly').val();
			post.currency_code = jQuery('#currency_code').val();

			post.extradomains_enabled = hasextra ? 1 : 0;

			//if (hasextra) //save even if off...
			//{
			post.extradomain_weekly = jQuery('#pextradomain_weekly').val();
			post.extradomain_biweekly = jQuery('#pextradomain_biweekly').val();
			post.extradomain_monthly = jQuery('#pextradomain_monthly').val();
			//}

			post.id = '<?php echo $_GET["editpackage"]; ?>';
			post.action = "savepackage";

			//sending
			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>/?hmtrackeractions', post, function (data) {
				jQuery('.save-button').button('reset');
				if (data != "ok") {
					jQuery("#settings_form").prepend(alerter(data, 1));
				} else {
					location.href = "<?php echo $this -> PLUGIN_URL ?>?packages";
				}

			});

		})


	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
