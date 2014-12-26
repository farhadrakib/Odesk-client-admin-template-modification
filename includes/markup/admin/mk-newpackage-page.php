<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
					<h2><span><a href="<?php echo $this->PLUGIN_URL ?>?packages">Packages</a> &gt; Create</span>

						New Package</h2>

					<div class="table-holder">

						<form action="#" method="POST" class="form-horizontal" id="settings_form">

							<div class="control-group">
								<label class="control-label">Package Title</label>

								<div class="controls">
									<input type="text" id="ptitle" class="" value=""/>
								</div>
							</div>


							<div class="control-group">
								<label class="control-label">Domain Count</label>

								<div class="controls">
									<div class="input-append input-mini">
										<input style="width: 50px" id="pdomains" min="1" step="1" value="1"
										       type="number">
										<span class="add-on">domains</span>
									</div>
								</div>
							</div>


							<div class="control-group">
								<label class="control-label">Currency</label>

								<div class="controls">
									<select id="currency_code">
										<option>USD</option>
										<option>EUR</option>
										<option>GBP</option>
										<option>AUD</option>
										<option>CHF</option>
										<option>CAD</option>
										<option>ILS</option>
									</select>
								</div>
							</div>


							<div class="control-group">
								<label class="control-label">Weekly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pweekly" min="0" step="1" value="0"
									       type="number">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Biweekly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pbiweekly" min="1" step="1" value="0"
									       type="number">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label">Monthly Cost</label>

								<div class="controls">
									<input style="width: 50px" id="pmonthly" min="1" step="1" value="0"
									       type="number">
								</div>
							</div>

							<div class="accordion" id="accordion1" style="height: auto;">
								<div class="accordion-group">
									<div class="accordion-heading">
										<a class="accordion-toggle collapse in" id="pextradomains_toggle"
										   data-toggle="collapse" data-parent="#accordion1" href="#collapse_1">
											<i class="icon-ok-circle"></i>
											<span>Extra Domains Enabled</span>
										</a>
									</div>
									<div id="collapse_1" class="accordion-body collapse in">
										<div class="accordion-inner collapse in" style="padding-left:0px;">
											<div class="control-group">
												<label class="control-label">Weekly Cost</label>

												<div class="controls">
													<input style="width: 50px" id="pextradomain_weekly" min="1"
													       max="999" step="1" value="0" type="number">
												</div>
											</div>
											<div class="control-group">
												<label class="control-label">Biweekly Cost</label>

												<div class="controls">
													<input style="width: 50px" id="pextradomain_biweekly" min="1"
													       max="999" step="1" value="0" type="number">
												</div>
											</div>
											<div class="control-group">
												<label class="control-label">Monthly Cost</label>

												<div class="controls">
													<input style="width: 50px" id="pextradomain_monthly" min="1"
													       max="999" step="1" value="0" type="number">
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>

							<div class="form-actions">
								<button type="button" class="btn btn-primary width-auto save-button"
								        data-loading-text="Creating...">
									Create Package
								</button>
							</div>
						</form>

					</div>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
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
			/*if (jQuery('#pmonthly').val() <= 0 && jQuery('#pbiweekly').val() <= 0 && jQuery('#pweekly').val() <= 0) {
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
			}*/

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

			post.action = "newpackage";

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
