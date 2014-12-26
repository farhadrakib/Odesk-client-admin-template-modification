<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */

if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}

/*
 * Public requests
 */
//activation processing
if ( isset( $_GET["hmtrackerregister"] ) ) {
	if ( isset( $_POST['fldTask'] ) && $_POST['fldTask'] == 'deregister' ) {
		if ( hmtrackerspy_regpost() ) {
			$option                = get_option( $this->OPTION_NAME );
			$option["license"]     = "";
			$option["license_key"] = "";
			update_option( $this->OPTION_NAME, $option );
			die( "Deregistered Successfully" );
		}
		die();
	}
}
//restoring processing
if ( isset( $_GET["hmtrackerrestore"] ) ) {
	if ( hmtrackerspy_restore_access() ) {
		require_once( dirname( __FILE__ ) . '/../markup/mk-messagepage-startrestore-success.php' );
	} else {
		require_once( dirname( __FILE__ ) . '/../markup/mk-messagepage-startrestore-fail.php' );
	}
	die();
}
if ( isset( $_GET["rkey"] ) ) {
	if ( hmtrackerspy_restore_access_key() ) {
		require_once( dirname( __FILE__ ) . '/../markup/mk-restore-page.php' );
	} else {
		require_once( dirname( __FILE__ ) . '/../markup/mk-messagepage-keysteprestore-fail.php' );
	}
	die();
}
if ( isset( $_GET["restoreit"] ) ) {
	if ( hmtrackerspy_restore_access_at_the_end() ) {
		require_once( dirname( __FILE__ ) . '/../markup/mk-messagepage-restore-success.php' );
	} else {
		require_once( dirname( __FILE__ ) . '/../markup/mk-messagepage-restore-fail.php' );
	}
	die();
}
//activation page
if ( ! IS_KEY_VALID ) {
	$backendregister = true;
	require_once( dirname( __FILE__ ) . '/../markup/mk-registerconfig-page.php' );
	die();
}

//login page
if ( isset( $_GET["login"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-login-page.php' );
	die();
}

//when we generate js
if ( isset( $_GET["hmtrackerjs"] ) ) {
	require_once( dirname( __FILE__ ) . '/fn-js-processing.php' );
	die();
}

//when we get data from js
if ( isset( $_GET["hmtrackerdata"] ) ) {
	require_once( dirname( __FILE__ ) . '/fn-data-processing.php' );
	header( "Content-type: application/javascript" );
	die( $_GET['callback'] . '([])' );
}

//sign up
if ( isset( $_GET['package'] ) && ! empty( $_GET['package'] ) ) {
	require_once( dirname( __FILE__ ) . '/captcha.php' );
	require_once( dirname( __FILE__ ) . '/../markup/mk-registeruser-page.php' );
	die();
}

//paypal thankyou redirect - ALWAYS handle it BEFORE ipn handler, 
//as paypal sends same transaction vars to thankyou url...
if ( isset( $_GET['paypal_thankyou'] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-thankyou-page.php' );
	die();
}

//ipn async 
if ( isset( $_GET['ipn'] ) && ( ! empty( $_POST ) || true ) ) {
	require_once( dirname( __FILE__ ) . '/fn-ipn.php' );
	die();
}


/*
 * Private requests
 */

//when we login
if ( isset( $_POST["username"] ) && isset( $_POST["password"] ) ) {
	$banned_ip = HMTrackerFN::getRealIp();
	if ( ( $banned_ip != "" && isset( $this->BANNED_LOGINS ) && array_key_exists( $banned_ip, $this->BANNED_LOGINS ) && $this->BANNED_LOGINS[ $banned_ip ] < LOGIN_ATTEMPTS ) || $banned_ip == "" || ! array_key_exists( $banned_ip, $this->BANNED_LOGINS ) ) {
		if ( loginCheck( HMTrackerFN::hmtracker_secure( $_POST["username"] ), $_POST["password"], "Login or Password incorrect" ) ) {
			unset( $this->BANNED_LOGINS[ $banned_ip ] );
			update_option( $this->BANNED_LOGINS_NAME, $this->BANNED_LOGINS );
			header( 'location: ' . admin_url() );
			die();
		} else {
			if ( isset( $this->BANNED_LOGINS[ $banned_ip ] ) ) {
				$this->BANNED_LOGINS[ $banned_ip ] ++;
			} else {
				$this->BANNED_LOGINS[ $banned_ip ] = 1;
			}
			update_option( $this->BANNED_LOGINS_NAME, $this->BANNED_LOGINS );
			if ( $this->BANNED_LOGINS[ $banned_ip ] < LOGIN_ATTEMPTS ) {
				logout( 'login', 'Login or Password incorrect.<br />You have ' . ( LOGIN_ATTEMPTS - $this->BANNED_LOGINS[ $banned_ip ] ) . " login attempts left." );
			} else {
				wp_maill( $this->OPTIONS["email"], LOGIN_ATTEMPTS . " Failed Login Attempts", "From the following IP " . LOGIN_ATTEMPTS . " failed attempts was detected: \n\n" . $banned_ip . "\n\nHeatMapTracker\n" . admin_url() );
				if ( $this->OPTIONS["name"] == $_POST["username"] ) {
					$_POST['uemail'] = $this->OPTIONS["email"];
					hmtrackerspy_restore_access();
				}
				logout( 'login', 'Sorry, your IP was blocked. Please, contact support to unblock' );
			}
		}
	} else {
		logout( 'login', 'Sorry, your IP was blocked. Please, contact support to unblock' );
	}
}

//when we logout
if ( isset( $_GET["logout"] ) ) {
	logout();
	header( "Location:" . admin_url() );
	die();
}

//regular user access
ensure_logged_in();

if ( isset( $_GET["changeudata"] ) ) {
	changeUserData();
	die();
}

//User Payments
if ( isset( $_GET["upayments"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-upayments-page.php' );
	die();
}
//Change Package
if ( isset( $_GET["changepackage"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-changepackage-page.php' );
	die();
}

//user settings
if ( isset( $_GET["usersettings"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-usersettings-page.php' );
	die();
}
//help videos
if ( isset( $_GET["helpvideos"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-videos-page.php' );
	die();
}
//project pages
if ( isset( $_GET["project"] ) ) {
	//heavy geo class - include only if really needed
	require_once( dirname( __FILE__ ) . '/geoip.php' );
	require_once( dirname( __FILE__ ) . '/../markup/mk-project-page.php' );
	die();
}
if ( isset( $_GET["analytics"] ) ) {
	//heavy geo class - include only if really needed
	require_once( dirname( __FILE__ ) . '/geoip.php' );
	require_once( dirname( __FILE__ ) . '/../markup/mk-analytics-page.php' );
	die();
}
if ( isset( $_GET["hmaps"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-heatmaps-page.php' );
	die();
}
if ( isset( $_GET["ppages"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-popularpages-page.php' );
	die();
}
if ( isset( $_GET["settings"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-settings-page.php' );
	die();
}
if ( isset( $_GET["mdata"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/mk-managedata-page.php' );
	die();
}

//when we process ajax ections
if ( isset( $_GET["hmtrackeractions"] ) ) {
	require_once( dirname( __FILE__ ) . '/fn-actions-processing.php' );
	die();
}

if ( isset( $_GET["player_frame"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/track/mk-player-frame.php' );
	die();
}
if ( isset( $_GET["heatmap_frame"] ) ) {
	require_once( dirname( __FILE__ ) . '/../markup/track/mk-hmap-frame.php' );
	die();
}

//when we see user actions
if ( isset( $_GET["hmtrackerview"] ) ) {
	//heavy geo class - include only if really needed
	require_once( dirname( __FILE__ ) . '/geoip.php' );
	require_once( dirname( __FILE__ ) . '/../markup/track/mk-player-view.php' );
	die();
}
//heatmaps interface
if ( isset( $_GET["hmtrackerheatmap"] ) ) {
	// Check if we need to redirect because of schema mismatch
	$u = parse_url( $_GET['url'] );
	$h = parse_url( siteURL( false ) );
	if ( $h['scheme'] != $u['scheme'] ) {
		header( "Location: {$u['scheme']}://{$h['host']}{$h['path']}?{$h['query']}" );
		die();
	}
	require_once( dirname( __FILE__ ) . '/../markup/mk-heatmap-page.php' );
	die();
}
//save settings
if ( isset( $_GET["hmtrackersettings"] ) ) {
	require_once( dirname( __FILE__ ) . '/fn-settings-processing.php' );
	die();
}

if ( isset( $_GET['return_admin'] ) && isset( $_SESSION['return_to_admin'] ) && $_SESSION['return_to_admin'] ) {
	$option = get_option( $this->OPTION_NAME );
	loginCheck( $option['email'], $option['password'], "Login or Password incorrect", false );
	header( "Location: " . home_url() );
	die();
}

/*
 * Private requests for superadmin only
 */

if ( is_admin() ) {

	if ( isset( $_GET['client_login'] ) ) {
		$user = get_user_by( "id", $_GET['client_login'] );
		loginCheck( $user->email, $user->password, "Login or Password incorrect", false );
		$_SESSION['return_to_admin'] = true;
		header( "Location: " . home_url() );
		die();
	}

//automatic software updater
	if ( isset( $_GET["update_start"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-update-page.php' );
		die();
	}

//extrauser
	if ( isset( $_GET["extrauser"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-extrauser-page.php' );
		die();
	}
	if ( isset( $_GET["edituser"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-edituser-page.php' );
		die();
	}

	if ( isset( $_GET["devhelpvideos"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-adminvideos-page.php' );
		die();
	}

//All payments
	if ( isset( $_GET["payments"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-payments-page.php' );
		die();
	}

//admin settings
	if ( isset( $_GET["adminsettings"] ) ) {
		if ( isset( $_POST['form'] ) && $_POST["form"] == 'admin' ) {
			$_POST['fldBHelp'] = trim( htmlentities( stripslashes( $_POST['fldBHelp'] ), ENT_QUOTES ) );
			changeAdminData();
			header( "Location: " . home_url() . "?adminsettings&submit=true" );
			die();
		}
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-adminsettings-page.php' );
		die();
	}

	if ( isset( $_GET["about"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-about-page.php' );
		die();
	}

	if ( isset( $_GET["packages"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-packages-page.php' );
		die();
	}
	if ( isset( $_GET["newpackage"] ) || isset( $_GET["editpackage"] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-package-page.php' );
		die();
	}

	if ( isset( $_GET['rds'] ) ) {
		require_once( dirname( __FILE__ ) . '/../markup/admin/mk-rds-page.php' );
		die();
	}

}

?>