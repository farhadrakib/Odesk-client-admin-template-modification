<?php
if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly');

set_time_limit(30); //paypal waits 30 seconds only for any IPN notification to respond with 200 status
ignore_user_abort(); //we are wanting

// Check that transaction id doesnt already exist. Die if it does
if(isset($_POST['txn_id']) && get_payment_by('txnid', $_POST['txn_id'])) {
	die();
}

log_ipn_message();
//TESTS
//print_r($_POST);
register_shutdown_function('hmt_shutdown');

// TESTS

require_once('balance.php');


$errors = array();

// Check if valid IPN message
if(!hmt_verify_paypal_ipn($this->PAYPAL_URL)) {
	$errors[] = "IPN Verification failed";
	goto process_errors;
}


function hmt_shutdown()
{

	global $HMTrackerPro_OPTION_NAME;
	$option = get_option($HMTrackerPro_OPTION_NAME);

	if (!is_null($e = error_get_last())) {
		print "this is not html:\n\n" . print_r($e, true);
		$option['last_ipn_error']    = array();
		$option['last_ipn_error'][0] = $e;
		$option['last_ipn_error'][1] = $_POST;
		update_option($HMTrackerPro_OPTION_NAME, $option);
	}
}


//STEP 2: IDENTIFY user and plan
$unpack = unpack_paypal_custom($_POST["custom"]);

if (is_array($unpack))
	$custom = extract($unpack, EXTR_OVERWRITE);

if (empty($user_key) || empty($plan_id)) {
	//seems like fake effort: need notify admin
	$errors[] = "Unknown custom received: " . $_POST["custom"];
	//try to recover custom from parent txn...
	if (isset($_POST['parent_txn_id'])) {
		if (!($payment = get_payment_by('txnid', $_POST['parent_txn_id'])))
			goto process_errors;

		$user    = get_user_by('id', $payment->user_id);
		$plan_id = $payment->plan_id;
	}
}

//detect user
$user = !empty($user) ? $user : get_user_by('user_key', $user_key);
if (empty($user)) {
	$payer_email = hmt_isset($vars['payer_email']);
	$payer_key   = create_user_key($payer_email);
	if ($payer_key != $user_key) {
		$user = get_user_by('user_key', $payer_key);
		if (empty($user)) //no need to spam admin about old notifications for purged users
			exit(0);
	}
}

//detect plan
//print_r(compact('user_key', 'plan_id', 'user' ));	
$plan = get_plan_by_id($user, $plan_id);
if (!$plan) { //try recover plan as active for the user...
	$plan = get_active_plan($user);
	if (!empty($plan))
		$errors[] = "Warning! Unknown plan in custom vars: '$plan_id', user: {$user->email}.
		User plan {$plan['id']}('" . $plan['title'] . "') choosen as closest matching.";
	else {
		//well, get any of his plans...
		$plan     = $user->plans[count($user->plans) - 1];
		$errors[] = "Warning: Unknown plan in custom vars: '$plan_id', user: {$user->email}.
		Last user plan " . $plan['title'] . " choosen.";
	}
}

//STEP 3: DO TRANSACTION PROCESSING
$process_res = hmt_process_paypal($_POST, $user, $plan);
if (is_array($process_res)) {
	$errors = array_merge($errors, $process_res);
}


//jump here when need finish 
process_errors:
if (!empty($errors)) {
	send_payment_alert_email($errors);
	if (in_array('Database Error', $errors)) { //db is down, let paypal resend it later
		header('HTTP/1.0 500 Internal Server Error');
		exit;
	}
	exit(0);
} else {
	header('HTTP/1.1 200 OK');
	if (hmt_isset($_POST['txn_type']) == 'subscr_payment')
		send_payment_email($user);
	exit(0);
}

/*
 * ============ PayPal Processing Functions ============= 
 */

function hmt_verify_paypal_ipn($paypal_url)
{
	// STEP 1: Read POST data
	$raw_posts = explode('&', file_get_contents('php://input'));
	$posts     = array();
	foreach ($raw_posts as $keyval) {
		$keyval = explode('=', $keyval);
		if (count($keyval) == 2)
			$posts[$keyval[0]] = urldecode($keyval[1]);
	}

	$get_magic_quotes_exists = false;
	$req                     = 'cmd=_notify-validate';
	if (function_exists('get_magic_quotes_gpc'))
		$get_magic_quotes_exists = true;

	foreach ($posts as $key => $value) {
		if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
			$value = urlencode(stripslashes($value));
		else
			$value = urlencode($value);
		$req .= "&$key=$value";
	}

	// STEP 2: Post IPN data back to paypal to validate
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $paypal_url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	$res = curl_exec($ch);

	curl_close($ch);

	// STEP 3: Inspect IPN validation result and act accordingly
	$process = false;
	if (strcmp($res, "VERIFIED") == 0) {
		return true;
	}

	return false;
}

function hmt_process_paypal($vars, &$user, &$plan)
{

	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option($HMTrackerPro_OPTION_NAME);

	$errors = array();
	if (strcasecmp($option['paypal_email'], $_POST['receiver_email']) != 0) {
		$errors[] = 'Invalid receiver email. actual: "'
			. $vars['receiver_email'] . '", expected: "'
			. $option['paypal_email'] . '"';
	}

	if ($vars['mc_currency'] != $plan['currency_code']) {
		$errors[] = 'Invalid transaction currency';
	}

	if (in_array($vars['payment_status'], array('Reversed', 'Reversal', 'Refunded')))
		$process = hmt_paypal_refund($vars, $user, $plan);
	else if (in_array($vars['txn_type'], array('cart', 'web_accept')))
		$errors[] = $vars['txn_type'] . " transactions are not currently supported!";
	else
		$process = hmt_paypal_recurring($vars, $user, $plan);

	if (is_array($process))
		$errors = array_merge($errors, $process);

	return $errors;
}

function hmt_paypal_recurring($vars, &$user, &$plan = null)
{
	$errors = array();

	if (!is_array($vars)) {
		$errors[] = 'No Postdata variables from Paypal for subscription transaction. PayPal IPN ignored.';

		return $errors;
	}

	$currency         = $plan['currency_code'];
	$txn_type         = hmt_isset($vars['txn_type']);
	$payment_currency = hmt_isset($vars['mc_currency']);
	$payment_status   = hmt_isset($vars['payment_status'], '');
	if ($payment_currency == 'USD')
		$payment_amount = hmt_isset($vars['payment_gross'], hmt_isset($vars['amount3'], 0));
	if ($payment_currency != 'USD' || $payment_amount == 0)
		$payment_amount = hmt_isset($vars['mc_gross'], hmt_isset($vars['mc_amount3'], 0));

	$txn_id         = hmt_isset($vars['txn_id'], '');
	$receiver_email = hmt_isset($vars['receiver_email']);
	$payer_email    = hmt_isset($vars['payer_email']);
	//not (yet) used vars...
//	$payer_id = hmt_isset($vars['payer_id']);
//	$invoice = hmt_isset($vars['invoice']);
//	$subscr_id = hmt_isset($vars['subscr_id']);
//	$payment_gross = hmt_isset($vars['payment_gross']);
//	$payment_fee = hmt_isset($vars['mc_fee']);
//	$item_name = hmt_isset($vars['item_name']);
//	$item_number = hmt_isset($vars['item_number']);
//	$first_name = hmt_isset($vars['first_name']);
//	$last_name = hmt_isset($vars['last_name']);

	$user_id = $user->id;
	$plan_id = $plan['id'];

	$payment = array
	(
		'txnid'          => $txn_id,
		'payment_amount' => $payment_amount,
		'payment_status' => $payment_status,
		'txn_type'       => str_replace('subscr_', '', $txn_type),
		'user'           => $payer_email,
		'user_id'        => $user_id,
		'plan_id'        => $plan_id
	);

	$inserted_id = true;
	switch ($txn_type) {
		case 'subscr_signup': //comes with/before/after first payment.
			create_user_tables($user->user_key);
			$inserted_id = add_payment($payment);
			foreach ($user->plans as $i => $p) {
				if ($p['id'] == $plan['id']) {
					$user->plans[$i]['start_date'] = time();
					break;
				}
			}
			update_user($user);
			if($plan['free_trial'] && $plan['free_trial'] > 0) {
				$initial_balance = calculate_initial_credit($user, $plan, $plan['free_trial'] * 24);
				create_payment($user, $initial_balance, $plan);
			}
			break;

		case 'subscr_payment':
			if (!tables_created($user->user_key)) {
				header('HTTP/1.1 500 Internal Server Error');
			}

			if ($payment_status != 'Completed') {
				$errors[] = 'Payment status is "' . $vars['payment_status'] . '". PayPal IPN ignored.';
				break;
			}
			if (!plan_has_payments($payment['plan_id'])) {
				$now                = time();
				$inserted_id        = add_payment($payment, $now);
				$plan['start_date'] = $now;
				replace_plan($user->plans, $plan);
				//update_user( $user );
				detect_user_status($user, true);
			} else {
				$inserted_id = add_payment($payment);
				detect_user_status($user, true);
			}
			break;
		case 'subscr_eot':
		case 'subscr_cancel':
			$payment['txn_type'] = 'cancel';
			$inserted_id         = add_payment($payment);
			if (empty($plan['end_date'])) {
				$plan['end_date'] = time();
				replace_plan($user->plans, $plan);
				update_user($user);
			}
			//detect_user_status( $user, true );
			break;
	}

	if (!$inserted_id)
		$errors[] = 'Database Error';

	$_SESSION['txn_id'] = $inserted_id;

	return $errors;
}

function hmt_paypal_refund($vars, &$user, &$plan)
{
	echo 'in hmt_paypal_refund';
	$errors         = array();
	$txn_type       = hmt_isset($vars['txn_type']);
	$payment_status = hmt_isset($vars['payment_status']);
	$txn_id         = hmt_isset($vars['txn_id']);
	$payer_email    = hmt_isset($vars['payer_email']);
	$user_id        = $user->id;
	$plan_id        = $plan['id'];
	//$parent_txn_id = hmt_isset($vars['parent_txn_id']);
	//$payer_id = hmt_isset($vars['payer_id']);
	//$payment_amount = hmt_isset($vars['mc_gross']);

	$payment = array
	(
		'txnid'          => $txn_id,
		'payment_amount' => $payment_amount,
		'payment_status' => $payment_status,
		'txn_type'       => 'refund',
		'user'           => $payer_email,
		'user_id'        => $user_id,
		'plan_id'        => $plan_id,
	);

	if (!($_SESSION['txn_id'] = refund_payment($payment)))
		$errors[] = 'Database Error';
	else
		$errors[] = 'Refund transaction initiated!';

	update_user_status($user_id, 4);

	return $errors;
}

function log_ipn_message()
{
	if (!HMT_LOG_IPN)
		return;

	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option   = get_option($HMTrackerPro_OPTION_NAME);
	$unpacked = unpack_paypal_custom($_POST["custom"]);
	if (is_array($unpacked))
		$custom = extract($unpacked, EXTR_OVERWRITE);
	$user = get_user_by('user_key', $user_key);
	if ($user)
		$user_id = $user->id;
	else
		$user_id = 0;

	$txn_id    = hmt_isset($_SESSION['txn_id'], 0);
	$pay_email = mysql_real_escape_string(hmt_isset($_POST['payer_email'], ''));
	$postdata  = mysql_real_escape_string(file_get_contents('php://input'));
	$status    = $txn_id > 0 ? 1 : 0;

	$table_ipn = T_PREFIX . $option['dbtable_name_ipn'];
	$q         = "INSERT INTO `" . $table_ipn . "` (
					`tx_id`,
					`user_id`,
					`pay_email`,
					`paysys`,
					`status`,
					`postdata`,
					`date`)
			VALUES ( $txn_id, $user_id, '$pay_email', 'paypal', $status, '$postdata', NOW() )
			;";
	$res       = $wpdb->query($q);
	//echo $q;
	$wpdb->lastInsertedId();
	//session_destroy();
}

function send_payment_email($user)
{
	//global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option($HMTrackerPro_OPTION_NAME);

	wp_maill($user->email, "Payment Received", "New payment was received:\n\n
	
	Item: " . $_POST['item_name'] . "\n
	Amount: " . $_POST['mc_gross'] . ' ' . $_POST['mc_currency'] . "\n
	Date: " . $_POST['subscr_date'] . "\n
	
	\n" . $option["brandname"] . "\n" . admin_url());
}

function send_payment_alert_email($errors)
{
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option($HMTrackerPro_OPTION_NAME);

	wp_maill($option['email'], "IPN Errors", "We have found IPN issue, please contact support. Add the following info:\n\n
	Errors: " . serialize($errors) . "\n
	Post: " . serialize($_POST) . "\n
	\n" . $option["brandname"] . "\n" . admin_url());

	$option['last_ipn_error'] = array($errors, $_POST);
	update_option($HMTrackerPro_OPTION_NAME, $option);
	//header("HTTP/1.0 200 Ok");
}
 