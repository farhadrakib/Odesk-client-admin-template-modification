<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
$edit = isset( $_GET["editpackage"] );
?>
	<div id="content">
		<div class="analytics-block">
			<h2>
						<span>
							<a href="<?php echo $this->PLUGIN_URL ?>?packages">Packages</a> &gt; Edit
						</span>
				<?php echo $edit ? "Edit Package" : "New Package"; ?>
			</h2>

			<div class="table-holder">
				<form action="#" method="POST" class="form-horizontal" id="settings_form">

					<div class="control-group">
						<label class="control-label">Package Title</label>

						<div class="controls">
							<input type="text" id="ptitle" class="" value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['title'] : ""; ?>"/>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Domain Count</label>

						<div class="controls">
							<input style="width: 50px" id="pdomains" min="1" max="999" step="1" value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['domains'] : "1"; ?>" type="number">
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Free Trial (Days)</label>

						<div class="controls">
							<input style="width: 50px" id="pfree_trial" min="0" max="999" step="1" value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['free_trial'] : "0"; ?>" type="number">
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Currency</label>

						<div class="controls">
							<select id="currency_code" <?php echo $edit ? "readonly" : ""; ?>>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "USD" ? "selected='selected'" : "" ) : "" ); ?>>
									USD
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "EUR" ? "selected='selected'" : "" ) : "" ); ?>>
									EUR
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "GBP" ? "selected='selected'" : "" ) : "" ); ?>>
									GBP
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "AUD" ? "selected='selected'" : "" ) : "" ); ?>>
									AUD
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "CHF" ? "selected='selected'" : "" ) : "" ); ?>>
									CHF
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "CAD" ? "selected='selected'" : "" ) : "" ); ?>>
									CAD
								</option>
								<option <?php echo( $edit ? ( $this->PACKAGES[ $_GET["editpackage"] ]['currency_code'] == "ILS" ? "selected='selected'" : "" ) : "" ); ?>>
									ILS
								</option>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Weekly Cost</label>

						<div class="controls">
							<input style="width: 50px" id="pweekly" min="0" max="99999" step="1"
							       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['weekly'] : "0"; ?>"
							       type="number">
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Biweekly Cost</label>

						<div class="controls">
							<input style="width: 50px" id="pbiweekly" min="0" max="99999" step="1"
							       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['biweekly'] : "0"; ?>"
							       type="number">
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Monthly Cost</label>

						<div class="controls">
							<input style="width: 50px" id="pmonthly" min="0" max="99999" step="1"
							       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['monthly'] : "0"; ?>"
							       type="number">
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Annual Cost</label>

						<div class="controls">
							<input style="width: 50px" id="pannually" min="0" max="99999" step="1"
							       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['annually'] : "0"; ?>"
							       type="number">
						</div>
					</div>
					<?php
					$extra_enabled = $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['extradomains_enabled'] : true;
					$collapse_in   = $extra_enabled ? ' in' : '';
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
												       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['extradomain_weekly'] : "0"; ?>"
												       type="number">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Biweekly Cost</label>

											<div class="controls">
												<input style="width: 50px" id="pextradomain_biweekly" min="1"
												       max="999" step="1"
												       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['extradomain_biweekly'] : "0"; ?>"
												       type="number">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Monthly Cost</label>

											<div class="controls">
												<input style="width: 50px" id="pextradomain_monthly" min="1"
												       max="999" step="1"
												       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['extradomain_monthly'] : "0"; ?>"
												       type="number">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Annual Cost</label>

											<div class="controls">
												<input style="width: 50px" id="pextradomain_annually" min="1"
												       max="999" step="1"
												       value="<?php echo $edit ? $this->PACKAGES[ $_GET["editpackage"] ]['extradomain_annually'] : "0"; ?>"
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
	<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
	<script>
		function validateForm() {

			var isvalid = true;
			if (jQuery("#ptype").val() == "normal") {
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
				if (jQuery('#pannually').val() <= 0) {
					jQuery('#pannually').focus();
					isvalid = false;
				}
			}

			return isvalid;
		}

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

//			jQuery("#ptype").change(function () {
//				switch (jQuery(this).val()) {
//					case "normal":
//						jQuery("#free-package").hide();
//						jQuery("#not-free-package").show();
//						break;
//					case "free":
//						jQuery("#not-free-package").hide();
//						jQuery("#free-package").show();
//						break;
//				}
//			});
//
			//save settings
			jQuery('.save-button').click(function () {

				jQuery("#settings_form .alert").remove();
				//validating
				var isvalid = true;

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
					if (jQuery('#pannually').val() > 0 && jQuery('#pextradomain_annually').val() <= 0) {
						jQuery('#pextradomain_annually').focus();
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
				post.free_trial = jQuery('#pfree_trial').val();
				post.type = jQuery("#ptype").val();
				post.weekly = jQuery('#pweekly').val();
				post.biweekly = jQuery('#pbiweekly').val();
				post.monthly = jQuery('#pmonthly').val();
				post.annually = jQuery('#pannually').val();
				post.currency_code = jQuery('#currency_code').val();

				post.extradomains_enabled = hasextra ? 1 : 0;

				//if (hasextra) //save even if off...
				//{
				post.extradomain_weekly = jQuery('#pextradomain_weekly').val();
				post.extradomain_biweekly = jQuery('#pextradomain_biweekly').val();
				post.extradomain_monthly = jQuery('#pextradomain_monthly').val();
				post.extradomain_annually = jQuery('#pextradomain_annually').val();
					//}

				<?php if(isset($_GET["editpackage"])) { ?>
				post.id = '<?php echo $_GET["editpackage"]; ?>';
				<?php } ?>
				post.action = '<?php echo isset($_GET["editpackage"]) ? "savepackage" : "newpackage"; ?>';

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
<?php include( "mk-footer.php" ); ?>