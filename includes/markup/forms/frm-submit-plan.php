<?php
/**
 * This file renders chose plan calculator
 * expects template variables:
 * required: $user (with plans prop unserialized),
 *             $order_total;
 * optional: $trial_amount(the amount of the first payment),
 *             $trial_shift(shift in days for the next payment after the first),
 *             $plan (if null, takes active),
 *             $prev_plan_id (plan that was active before starting package change)
 */
$plan             = ! isset( $plan ) ? get_active_plan( $user ) : $plan;
$pay_interval     = $plan['pay_interval'];
$cost_main        = number_format( $plan[ 'cost_' . $pay_interval ], 2 );
$cost_extra       = number_format( $plan[ 'extradomain_' . $pay_interval ], 2 );
$extra_count      = $plan['extradomains_count'];
$extra_text       = $extra_count > 0 ? ' + ' . $extra_count . ' extra' : '';
$paypal_item_name = $plan['title'] . " + " . $extra_count . " extra domains";
$total_extra      = number_format( $cost_extra * $extra_count, 2 );

$paypal_order_total = number_format( $order_total, 2 );

$mode = isset( $trial_amount ) && isset( $trial_shift ) ? 'change' : 'register';

$paypal_custom = pack_paypal_custom( $user, $plan );
if ( $mode == 'register' ) {
	$paypal_custom_get = $paypal_custom;
} else {
	$paypal_custom_get = pack_paypal_custom( $user, get_plan_by_id( $user, $prev_plan_id ), $plan );
}

$paypal_return_url = admin_url() . "?paypal_thankyou=" . $paypal_custom_get;
?>
<?php if ( ! empty( $errors ) ) : ?>

	<strong style="color: #f00">
		<?php echo implode( '<br />', $errors ) ?>
	</strong>

<?php endif; ?>
<form action="<?php echo $this->PAYPAL_URL; ?>" method="post">
	<input type="hidden" name="no_shipping" value="1">
	<?php if ( $mode == 'register' ) : ?>
		<input type="hidden" name="cur_register_step" value="submit_plan"/>
		<input type="hidden" name="prev_register_step" value="<?php echo $current_step ?>"/>
		<input type="hidden" name="user_key" value="<?php echo $user->user_key ?>"/>
		<p>

		<h3><?php echo $plan['title'] ?> ( <?php echo $plan['pack_domains'] ?> domains
			included <?php echo $extra_text ?> )</h3>
		<strong>Business Name</strong>: <?php echo $user->business_name ?><br/>
		<strong>Website</strong>: <?php echo $user->website; ?><br/>
		<strong>E-mail</strong>: <?php echo $user->email; ?><br/>
		<strong>Payments</strong>:
		<ul>
			<?php if($package['free_trial'] > 0) {?>
				<li>Free Trial Days: <?php echo $package['free_trial']; ?>;</li>

			<?php } ?>
			<li><?php echo ucfirst( $plan['pay_interval'] ) ?>;</li>
			<li>Base package: <?php echo "{$cost_main} {$package['currency_code']}"; ?>;</li>
			<?php if ( (float) $total_extra > 0 ) { ?>
				<li>
					Extra domains: <?php echo "{$total_extra} ( {$extra_count} domains x {$cost_extra} {$plan['currency_code']} ) {$package['currency_code']}"; ?>
				</li>
			<?php } ?>
		</ul>
		<strong>Total domains</strong>: <?php echo( $plan['pack_domains'] + $extra_count ); ?><br/>
		<strong>Total <?php echo $plan['pay_interval'] ?>
			: <?php echo $paypal_order_total; ?> <?php echo $package['currency_code'] ?>;</strong><br/>

		</p>
	<?php
	elseif ( $mode == 'change' ):
		$is_closed    = is_plan_closed( $user, $prev_plan_id ) || is_plan_free($package);
		$status_text  = $is_closed ? 'No active subscriptions' : 'You can continue but Paypal subscription for current plan is still active';
		$status_class = $is_closed ? 'success' : 'warn';
		?>
		<div class="alert alert-<?php echo $status_class; ?>">
			<button class="close" data-dismiss="alert">×</button>
			<h5 id="subscr_canceled"><?php echo $status_text; ?>&nbsp;</h5>
			<?php if(!$is_closed) { ?> <button id="btn_check_subscr" type="button" class="btn btn-primary width-auto">Check Again</button> <?php } ?>
		</div>
		<p>Please go to your PayPal account and cancel your previous plan subscription if you have not done that
			yet!</p>
		<p>Press button below to check if we have received your previous subscription cancellation.</p>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#btn_check_subscr').bind('click', function () {
					var post = {}
					post.action = 'check_subscr';
					post.plan_id = '<?php echo $plan['id']; ?>';
					jQuery(this).button('loading');
					jQuery.post('<?php echo admin_url() ?>?hmtrackeractions', post, function (data) {
						jQuery('#btn_check_subscr').button('reset');
						is_closed = data == 'ok';
						status_text = is_closed ? 'Subscription is closed' : 'Subscription is still active';
						status_class = is_closed ? 'success' : 'warn';
						jQuery('#subscr_canceled').html(status_text);
						jQuery('#subscr_canceled')
							.parent()
							.removeClass('alert-warn alert-success')
							.addClass('alert-' + status_class);
						if (is_closed) jQuery('#btn_check_subscr').hide();
					});
				});
			});
		</script>
		<input type="hidden" name="a1" value="<?php echo $trial_amount; ?>">
		<input type="hidden" name="p1" value="<?php echo $trial_shift; ?>">
		<input type="hidden" name="t1" value="D">

	<?php endif; ?>
	<!-- Identify your business so that you can collect the payments. -->
	<input type="hidden" name="business" value="<?php echo $this->OPTIONS['paypal_email']; ?>">

	<!-- IPN -->
	<input type="hidden" name="notify_url" value="<?php echo admin_url(); ?>?ipn"/>

	<!-- return -->
	<input type="hidden" name="return" value="<?php echo $paypal_return_url; ?>?"/>
	<input type="hidden" name="rm" value="2"/><!-- 1: simple get, no vars; 2: Post with txn vars -->

	<!-- Specify a Subscribe button. -->
	<input type="hidden" name="cmd" value="_xclick-subscriptions">
	<!-- Identify the subscription. -->
	<input type="hidden" name="item_name" value="<?php echo $paypal_item_name ?>">
	<input type="hidden" name="item_number" value="1">

	<!--custom -->
	<input type="hidden" name="custom" value="<?php echo $paypal_custom; ?>">

	<!-- Set the terms of the regular subscription. -->
	<input type="hidden" name="currency_code" value="<?php echo $package['currency_code'] ?>">

	<?php if ( $pay_interval == 'weekly' ): ?>
		<input type="hidden" name="a3" value="<?php echo $paypal_order_total ?>">
		<input type="hidden" name="p3" value="1">
		<input type="hidden" name="t3" value="W">
	<?php elseif ( $pay_interval == 'biweekly' ): ?>
		<input type="hidden" name="a3" value="<?php echo $paypal_order_total ?>">
		<input type="hidden" name="p3" value="2">
		<input type="hidden" name="t3" value="W">
	<?php
	elseif ( $pay_interval == 'monthly' ): ?>
		<input type="hidden" name="a3" value="<?php echo $paypal_order_total ?>">
		<input type="hidden" name="p3" value="1">
		<input type="hidden" name="t3" value="M">
	<?php
	elseif ( $pay_interval == 'annually' ): ?>
		<input type="hidden" name="a3" value="<?php echo $paypal_order_total ?>">
		<input type="hidden" name="p3" value="1">
		<input type="hidden" name="t3" value="Y">
	<?php endif; ?>

	<?php if($package['free_trial'] > 0) { ?>
		<input type="hidden" name="a1" value="0">
		<input type="hidden" name="p1" value="<?php echo $package['free_trial']; ?>>">
		<input type="hidden" name="t1" value="D">
	<?php } ?>

	<!-- Set recurring payments until canceled. -->
	<input type="hidden" name="src" value="1">

	<button type="button" href="<?php echo admin_url() ?>?upayments" class="btn btn-primary width-auto"
	        style="float: left">Cancel
	</button>

	<input type="submit" class="btn btn-primary width-auto " value="Checkout" style="float: left; margin-left: 10px;"/>
</form>