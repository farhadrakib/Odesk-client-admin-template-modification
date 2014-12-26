<?php

function detect_register_step() {
	if ( isset( $_POST['cur_register_step'] ) ) {
		return array( $_POST['cur_register_step'], $_POST['prev_register_step'] );
	}

	return array( 'start', '' );
}

function validate_start_form( &$hmt ) {
	global $HMTrackerPro_USERSTATUS_NAME;

	$errors = array();
	if ( ! ( $captcha_solved = captcha::solved() ) ) {
		$errors[] = '<strong>Captcha Error</strong>. Please try again';
	}

	if ( $captcha_solved ) {
		$error = false;

		$uid = create_user_key( $_POST['email'] );

		//check if user in DB
		$user = get_user_by( 'user_key', $uid );
		if ( $user && $user->status != 0 ) {
			$error    = true;
			$errors[] = "<strong>Signup Error</strong>. Email {$_POST['email']} already exists";
		}

		//create user plan and user record
		if ( ! $error ) {
			$_POST['user_key']               = $uid; //we save it for next wizard step on success.
			$u_package                       = get_plan_for( $hmt->PACKAGES[ $_GET['package'] ], $_GET['package'] );
			$u_package['extradomains_count'] = 0;
			$u_package['pay_interval']       = 'weekly';
			$status                          = 0; //created - waiting first payment

			if ( is_plan_free($u_package) ) {
				//Free limited package
				$status = 7;
			}

			if ( ! $user ) {
				$create_user_result = create_user_step( $hmt, $user, $uid, $u_package, $status );
				$error              = $create_user_result[0];
				$errors             = $create_user_result[1];
			} else {
				$user->status            = 0;
				$user->last_status_check = time();
				$user->business_name     = HMTrackerFN::hmtracker_secure( $_POST["bname"] );
				$user->password          = md5( sha1( HMTrackerFN::hmtracker_secure( $_POST["pass1"] ) ) );
				$user->website           = HMTrackerFN::hmtracker_secure( $_POST["site"] );
				$user->plans             = serialize( array( $u_package ) );
				update_user( $user );

				update_option( $hmt->PROJECTS_NAME . $uid, array() );
				$statuses = array( 1 => array( 0 ), 2 => array( 0 ) );
				update_option( $HMTrackerPro_USERSTATUS_NAME . $user->id, $statuses );
			}
		}
	}

	return $errors;
}

function validate_choose_plan( $user ) {
	$errors = array();

	$plan                 = get_active_plan( $user );
	$plan['pay_interval'] = $_POST['pay_interval'];
	if ( isset( $_POST['extradomains'] ) ) {
		$plan['extradomains_count'] = intval( $_POST['extradomains'] );
	}

	require_once( 'balance.php' );
	$calc           = new BalanceCalculator();
	$payment_amount = $calc->CalcPlanPayment( $plan );
	if ( (float) $_POST['order_total'] != $payment_amount ) {
		$errors[] = "<strong>Summary Error</strong>. Order total mismatch ({$_POST['order_total']} vs $payment_amount)";
	}

	if ( empty( $errors ) ) {
		$user->plans = array( $plan );
		update_user( $user );
	}

	return $errors;
}

function create_user_step( &$hmt, $user, $uid, $u_package, $status ) {
	global $wpdb, $HMTrackerPro_USERSTATUS_NAME;

	$error       = false;
	$errors      = array();
	$table_users = T_PREFIX . $hmt->OPTIONS['dbtable_name_users'];
	$q           = "INSERT INTO `" . $table_users . "` (`email`, `password`, `business_name`, `website`, `user_key`, `plans`, `status`, `last_status_check`)
					 VALUES ('" . HMTrackerFN::hmtracker_secure( $_POST["email"] ) . "',
					 '" . md5( sha1( HMTrackerFN::hmtracker_secure( $_POST["pass1"] ) ) ) . "',
					 '" . HMTrackerFN::hmtracker_secure( $_POST["bname"] ) . "',
					 '" . HMTrackerFN::hmtracker_secure( $_POST["site"] ) . "',
					 '" . $uid . "',
					 '" . serialize( array( $u_package ) ) . "',
					 $status,
					 " . time() . ")";
	$res         = $wpdb->query( $q );

	if ( ! $res ) {
		$error    = true;
		$errors[] = '<strong>Database Error</strong>. Please try again later';
	} else {
		$remaining_hours =  $u_package['free_trial'] > 0 ? $u_package['free_trial'] * 24 : 0;
		$hours = array();
		$hours[1] = $remaining_hours;
		$hours[2] = $remaining_hours + 24;
		$statuses = array( $hours );
		add_option( $HMTrackerPro_USERSTATUS_NAME . $wpdb->lastInsertedId(), $statuses );
		add_option( $hmt->PROJECTS_NAME . $uid, array() );
	}

	return array( $error, $errors );
}