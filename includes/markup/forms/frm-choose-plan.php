<?php
/**
 * This file renders choose plan calculator for new user registration.
 * expects template variables:
 * $package, $user_key, $errors(optional)
 */

$free_package = $package['weekly'] == 0 && $package['biweekly'] == 0 && $package['monthly'] == 0 && $package['annually'] == 0;

?>


<!-- BEGIN LOGIN FORM -->
<h4>Configure Package "<?php echo $package['title'] ?>" ( <?php echo $package['domains'] ?> domains included )</h4>

<?php if ( ! empty( $errors ) ) { ?>

	<strong style="color: #f00">
		<?php echo implode( '<br />', $errors ) ?>
	</strong>

<?php } ?>

<form id="planform" class="form-vertical no-padding no-margin" method="post"
      action="<?php echo admin_url() ?>?package=<?php echo $package_id ?>" style="overflow: hidden; clear: both">
	<input type="hidden" name="cur_register_step" value="choose_plan"/>
	<input type="hidden" name="prev_register_step" value="<?php echo $current_step ?>"/>
	<input type="hidden" name="user_key" value="<?php echo $user_key ?>"/>
	<input type="hidden" id="hdn_order_total" name="order_total" value="<?php echo $_POST['order_total'] ?>"/>

	<?php if(!$free_package) { ?>
	<div class="control-group">
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class=" icon-money">Payment Plan</i></span>
				<select id="ppay_interval" name="pay_interval">
					<?php if ( $package['weekly'] > 0 ) { ?>
						<option value="weekly">
							Weekly (<?php echo $package['weekly'] ?> <?php echo $package['currency_code'] ?>)
						</option>
					<?php } ?>
					<?php if ( $package['biweekly'] > 0 ) { ?>
						<option value="biweekly">
							Biweekly (<?php echo $package['biweekly'] ?> <?php echo $package['currency_code'] ?>)
						</option>
					<?php }; ?>
					<?php if ( $package['monthly'] > 0 ) { ?>
						<option value="monthly">
							Monthly (<?php echo $package['monthly'] ?> <?php echo $package['currency_code'] ?>)
						</option>
					<?php } ?>
					<?php if ( $package['annually'] > 0 ) { ?>
						<option value="annually">
							Annually (<?php echo $package['annually'] ?> <?php echo $package['currency_code'] ?>)
						</option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>
	<?php if ( $package['extradomains_enabled'] ) { ?>
		<div class="control-group">
			<div class="controls">
				<div class="input-prepend">
					<span class="add-on"><i class="icon-plus">Extra Domains</i> </span>
					<input id="pextradomains" name="extradomains" style="width: 68px" min="0" max="1000" step="1"
					       value="0" type="number">
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="control-group">
		<div class="controls">
			<div id="msg_order_total"></div>
		</div>
	</div>
	<?php } else { ?>
		<div class="control-group">
			<div class="controls">
				<div>Free Package</div>
			</div>
		</div>
		<input type="hidden" name="free_package" value="1">
	<?php } ?>
	<br/><br/><br/>
	<input type="submit" id="login-btn" class="btn btn-block btn-inverse fldchooseplan" value="Continue"/>

</form>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var pack = <?php echo json_encode($package); ?>;

		function calcPlanHandler() {
			var pay_interval = jQuery('#ppay_interval').val();
			var extra_domains = parseInt(jQuery('#pextradomains').val(), 10) || 0;
			var total = parseFloat(pack[pay_interval]), extra = 0;
			if (extra_domains > 0) {
				total += extra_domains * parseFloat(pack['extradomain_' + pay_interval]);
			}
			total = total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			jQuery('#msg_order_total').html('Order total: <strong>' + total + '</strong> <?php echo $package['currency_code'] ?>');
			jQuery('#hdn_order_total').val(total);
		}

		jQuery('#ppay_interval, #pextradomains').bind('change', calcPlanHandler);
		calcPlanHandler();
	});
</script>
