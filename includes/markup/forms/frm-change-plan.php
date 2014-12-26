<?php
/**
 * This file renders change plan calculator for existing user.
 * expects template variables:
 * $plan, $user, $form_action, $errors(optional)
 */
//print_r(compact('plan'));
//$cost_main = number_format($plan['cost_'.$pay_interval], 2);
//$balance += 20.48;
global $HMTrackerPro_USERSTATUS_NAME;

$statuses = get_option( $HMTrackerPro_USERSTATUS_NAME . $user->id );

$cost_extra  = number_format( $plan[ 'extradomain_' . $pay_interval ], 2 );
$extra_count = $plan['extradomains_count'];
$extra_text  = $extra_count > 0 ? ' + ' . $extra_count . ' extra' : '';
$balance_fmt = number_format( $balance, 2 );
$plan_cost   = number_format( $plan_cost, 2 );

$current_plan_domains = $plan["cost_{$plan['pay_interval']}"];
$current_plan_extra   = $plan['extradomains_count'] * $plan["cost_{$plan['pay_interval']}"];
$current_plan_total   = $current_plan_domains + $current_plan_extra;
$active_settings      = $plan['pack_domains'] . ' domains included ' . $extra_text;
// TODO - need to change user status for free packages back to active state upon plan change
?>
</div>
<?php if ( ! empty( $errors ) ) : ?>

	<strong style="color: #f00">
		<?php echo implode( '<br />', $errors ) ?>
	</strong>

<?php endif; ?>

<form id="planform" class="form-horizontal no-padding no-margin" method="post" action="<?php echo $form_action; ?>"
      style="overflow: hidden; clear: both">
	<input type="hidden" id="hdn_order_total" name="order_total" value="0"/>
	<input type="hidden" id="hdn_balance" name="balance" value="<?php echo $balance_fmt; ?>"/>
	<input type="hidden" id="hdn_trial_amount" name="trial_amount" value="0"/>
	<input type="hidden" id="hdn_trial_shift" name="trial_shift" value="0"/>

	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<label class="control-label"></label>

				<div class="controls">
					<h4>Current Plan</h4>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Base Package</label>

				<div class="controls">
					<div class="input-append input-mini">
						<span class="add-on"><strong><?php echo isset( $plan['title'] ) ? $plan['title'] : "Free"; ?></strong></span>
					</div>
				</div>
			</div>
			<?php
			$user_status = detect_user_status();
			if ( $user_status != 6 ) {
				?>
				<div class="control-group">
					<label class="control-label">Start Date</label>

					<div class="controls">
						<div class="input-append input-mini">
							<span class="add-on"><strong><?php echo @date( "Y-m-d", $plan['start_date'] ); ?></strong></span>
						</div>
					</div>
				</div>
				<?php if ( !is_plan_free($plan) && $user_status != 7 ) { ?>
					<div class="control-group">
						<label class="control-label">Time Left</label>

						<div class="controls">
							<div class="input-append input-mini">
								<span class="add-on"><strong><?php echo "{$statuses[1][0]} hours"; ?></strong></span>
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Payments Schedule</label>

						<div class="controls">
							<div class="input-append input-mini">
								<span class="add-on"><strong><?php echo ucwords( $plan['pay_interval'] ); ?></strong></span>
							</div>
						</div>
					</div>
				<?php } ?>
				<div class="control-group">
					<label class="control-label">Domains</label>

					<div class="controls">
						<div class="input-append input-mini">
						<span class="add-on"><strong><?php echo $plan["pack_domains"];
								echo " + " . ( $plan['extradomains_count'] ) . " extra domains " ?></strong></span>
						</div>
					</div>
				</div>
				<?php if ( !is_plan_free($plan) && $user_status != 7 ) { ?>
					<div class="control-group">
						<label class="control-label"><?php echo ucwords( $plan['pay_interval'] ); ?> Cost</label>

						<div class="controls">
							<div class="input-append input-mini">
						<span
							class="add-on"><strong><?php echo "{$current_plan_domains} {$plan['currency_code']}"; ?></strong></span>
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Extra Domain Cost</label>

						<div class="controls">
							<div class="input-append input-mini">
						<span
							class="add-on"><strong><?php echo "{$current_plan_extra} {$plan['currency_code']}"; ?></strong></span>
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Total <?php echo ucwords( $plan['pay_interval'] ); ?> Cost</label>

						<div class="controls">
							<div class="input-append input-mini">
						<span
							class="add-on"><strong><?php echo "{$current_plan_total} {$plan['currency_code']}"; ?></strong></span>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<div class="span6">
			<div class="control-group">
				<label class="control-label"></label>

				<div class="controls">
					<h4>New Plan</h4>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Base Package</label>

				<div class="controls">
					<select id="package_id" name="package_id">
						<?php
						$project_count = count( $this->PROJECTS );
						$i             = 0;
						foreach ( $this->PACKAGES as $package_id => $package_data ) {
							if ( $plan['pack_id'] == $package_id || (isset($package_data['free_trial']) && $package_data['free_trial'] > 0)) {
								continue;
							}
							if ( $project_count <= ($package_data["domains"]) ) {
								?>
								<option<?php echo $i ++ == 0 ? ' selected="selected"' : ''; ?> value="<?php echo $package_id; ?>" currency="<?php echo $package_data['currency_code']; ?>"><?php echo $package_data['title']; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>

			<div id="payments_container" class="control-group">
				<label class="control-label">Payments Schedule</label>

				<div class="controls">
					<select id="ppay_interval" name="pay_interval">
						<option id='ppay_weekly' value="weekly">
							Weekly (<?php echo $package['currency_code'] ?>)
						</option>
						<option id='ppay_biweekly' value="biweekly">
							Biweekly (<?php echo $package['currency_code'] ?>)
						</option>
						<option id='ppay_monthly' value="monthly">
							Monthly (<?php echo $package['currency_code'] ?>)
						</option>
						<option id='ppay_annually' value="annually">
							Annually (<?php echo $package['currency_code'] ?>)
						</option>
					</select>
				</div>
			</div>
			<div id="domains" class="control-group hide">
			</div>
			<div id="extrad_container" class="control-group">
				<label class="control-label">Extra Domains</label>

				<div class="controls">
					<input id="pextradomains" name="extradomains" style="width: 68px" min="0" max="1000" step="1" value="0" type="number" />
					<span id="pextradomain_cost"></span>
				</div>
			</div>
			<div id="first_payment" class="control-group hide">
			</div>
			<div id="second_payment" class="control-group hide">
			</div>
			<div id="third_payment" class="control-group hide">
			</div>
			<div id="extradomain_payment" class="control-group hide">
			</div>
			<div id="msg_order_total" class="control-group hide">
			</div>
		</div>
	</div>

	<div class="alert alert-warn">
		<button class="close" data-dismiss="alert">×</button>
		<p>By clicking continue button I agree to close current plan and to start another one immediately.</p>

		<p>I understand that during this process tracking for my domains may be temporary disabled.</p>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary save-button fldsubmitLicense width-auto">
			Continue
		</button>
	</div>

</form>
<div style="clear:both;"></div>
<script type="text/javascript">

	jQuery(document).ready(function () {
		var packs = <?php echo json_encode($this->PACKAGES); ?>;
		var plan = <?php echo json_encode($plan); ?>;
		var plan_start_date = '<?php echo @date("F d, Y H:i:s", $plan['start_date']); ?>';
		var now = '<?php echo @date("F d, Y H:i:s"); ?>';
		var balance = <?php echo $balance; ?>;
		var plan_cost = <?php echo $plan_cost; ?>;
		var plan_interval = '<?php echo $pay_interval; ?>';
		var min_first = 0.50;// 1 cent but in practice can be changed to doubled transaction fee.

		function fmtMoney($float) {
			return $float.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
		}

		Date.prototype.addDays = function (days) {
			this.setDate(this.getDate() + parseInt(days));
			return this;
		}

		function calcPlanHandler() {
			jQuery("#first_payment, #second_payment, #third_payment, #extradomain_payment, #msg_order_total").hide();

			var currency = jQuery("#package_id option:selected").attr("currency");
			var pack_id = jQuery('#package_id').val();
			var new_plan = packs[pack_id];
			if (typeof new_plan.weekly === "undefined") {
				new_plan.weekly = 0;
			}
			if (typeof new_plan.biweekly === "undefined") {
				new_plan.biweekly = 0;
			}
			if (typeof new_plan.monthly === "undefined") {
				new_plan.monthly = 0;
			}
			if (typeof new_plan.annually === "undefined") {
				new_plan.annually = 0;
			}
			//extradomains panel
			var extra_enabled = new_plan.extradomains_enabled == true ||
				new_plan.extradomains_enabled == 'true' ||
				new_plan.extradomains_enabled == 1 ||
				new_plan.extradomains_enabled == '1';
			var free_package = new_plan.weekly == 0 && new_plan.biweekly == 0 && new_plan.monthly == 0 && new_plan.annually == 0;
			console.log(free_package + ", " + new_plan.weekly + ", " + new_plan.biweekly + ", " + new_plan.monthly + ", " + new_plan.annually);
			jQuery('#payments_container').removeClass('hide');
			if (free_package) {
				jQuery('#payments_container').addClass('hide');
			}
			jQuery('#extrad_container').removeClass('hide');
			if (!extra_enabled) {
				jQuery('#extrad_container').addClass('hide');
				jQuery('#pextradomains').val(0);
			}


			//interval prices
			jQuery('#ppay_weekly').addClass("hide");
			if (new_plan.weekly > 0) {
				jQuery('#ppay_weekly').html('Weekly (' + currency + ' ' + new_plan.weekly + ')');
				jQuery('#ppay_weekly').removeClass("hide");
			}
			jQuery('#ppay_biweekly').addClass("hide");
			if (new_plan.biweekly > 0) {
				jQuery('#ppay_biweekly').html('Biweekly (' + currency + ' ' + new_plan.biweekly + ')');
				jQuery('#ppay_biweekly').removeClass("hide");
			}
			jQuery('#ppay_monthly').addClass("hide");
			if (new_plan.monthly > 0) {
				jQuery('#ppay_monthly').html('Monthly (' + currency + ' ' + new_plan.monthly + ')');
				jQuery('#ppay_monthly').removeClass("hide");
			}
			jQuery('#ppay_annually').addClass("hide");
			if (new_plan.annually > 0) {
				jQuery('#ppay_annually').html('Annually ( ' + currency + ' ' + new_plan.annually + ')');
				jQuery('#ppay_annually').removeClass("hide");
			}

			//order totals
			var pay_interval = jQuery('#ppay_interval').val();

			var extra_domains = parseInt(jQuery('#pextradomains').val(), 10) || 0;
			var total = parseFloat(new_plan[pay_interval]);
			var extradommain_cost = 0;
			if (extra_domains > 0) {
				extradommain_cost = extra_domains * parseFloat(new_plan['extradomain_' + pay_interval]);
				total += extradommain_cost;
				showPayment('extradomain_payment', 'Extra Domains Amount', extradommain_cost, currency);
			} else {
				jQuery('#extradomain_payment').hide();
			}
			var interval_days = getIntervalDays(pay_interval);
			var paid_days = interval_days * balance / total;
			total_fmt = fmtMoney(total);
			paid_days_round = 0;
			if (paid_days === parseInt(paid_days) && paid_days > 0) {//small-amount trial period for a day diff
				paid_days_round = Math.ceil(paid_days);
				new_delta = total * (paid_days_round - paid_days) / interval_days;
				//alert('paid_days_round:'+paid_days_round+',paid_days:'+paid_days);
				new_minutes = parseInt((paid_days_round - paid_days) * (60 * 24), 10);
				first_fmt = fmtMoney(0);
				if (parseFloat(new_delta) >= min_first) { //all less 1 cent is gifted
					//old_delta = plan_cost*(paid_days - parseInt(paid_days, 10))/getIntervalDays(plan_interval);
					//balance_delta = new_delta;
					first_fmt = fmtMoney(new_delta);
					if(first_fmt >= 0 ) {
						showPayment('second_payment', 'First Payment Amount', first_fmt, currency);
					}
					showPayment('third_payment', 'Additional Minutes', new_minutes, 'minutes');
				}
				else {
					jQuery('#second_payment, #third_payment').hide();
				}
				showPayment('first_payment', 'Billing Starts in', paid_days_round, paid_days_round === parseInt(paid_days_round) ? "days" : '');
				if(first_fmt >= 0 ) {
					jQuery('#hdn_trial_amount').val(first_fmt);
				}
				jQuery('#hdn_trial_shift').val(paid_days_round);

			} else {//must pay us dued balance
				first_fmt = fmtMoney(total - balance);
				if(first_fmt >= 0) {
					showPayment('first_payment', 'First Payment Amount', first_fmt, currency);
					showPayment('second_payment', 'Next Payments Every', parseInt(interval_days), 'days');
					jQuery('#hdn_trial_amount').val(first_fmt);
				}
				jQuery('#hdn_trial_shift').val(getIntervalDays(pay_interval));
			}

//			console.log("BALANCE:", balance);
//			console.log("MIN_FIRST:", min_first);
//			console.log("INTERVAL_DAYS:", interval_days);
//			console.log("PAID_DAYS", paid_days);
//			console.log("PAID_DAYS_ROUND", paid_days_round);
//			console.log("NEW_DELTA", new_delta);
//			console.log("NEW_MINUTES", new_minutes);
//			console.log("FIRST_FMT", first_fmt);
//			console.log("PAY_INTERVAL", pay_interval);
//			console.log("TOTAL", total);
			if(first_fmt >= 0) {
//				jQuery('#msg_order_total').html('<div class="span6">Payments Amount:</div><strong>' + total_fmt + '</strong> ' + currency);
				showPayment('msg_order_total', 'Payments Amount', total_fmt, currency);
				jQuery('#hdn_order_total').val(total_fmt);
			}
			showPayment("domains", "Domains", new_plan.domains);
		}

		function getIntervalDays(pay_interval) {
			switch (pay_interval) {
				case 'weekly':
					return 7;
				case 'biweekly':
					return 14;
				case 'monthly':
					return 30;
				case 'annually':
					return 365.25;
				default:
					return 1;
			}
		}

		function showPayment(id, label, amount, currency) {
			if (typeof currency === "undefined") {
				currency = "";
			}

			jQuery('#' + id).html('<label class="control-label">' + label + '</label><div class="controls"><div class="input-append input-mini"><span class="add-on"><strong>' + amount + '</strong> ' + currency + '</spsan></div></div>').show();

		}

		jQuery('#ppay_interval, #pextradomains, #package_id').bind('change', calcPlanHandler);
		jQuery('#package_id').trigger('change');
	});
</script>