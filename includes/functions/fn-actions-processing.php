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

if ( is_user() ) :
	$user = current_user();
	$plan = get_active_plan( $user );
//regular user ajax actions
	switch ( $_POST['action'] ) {
		case 'create': //project
			$this->PROJECTS[ $_POST['name'] ]                                        = array();
			$this->PROJECTS[ $_POST['name'] ]['description']                         = $_POST['description'];
			$this->PROJECTS[ $_POST['name'] ]['settings']                            = array();
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_black_ips']           = array( '127.0.0.1' );
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_status']       = true;
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_all']          = "true";
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_special']      = array();
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_mousemove']    = true;
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_pagescroll']   = true;
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_interval']     = 1;
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_kill_session'] = 100;
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_record_tz']           = @date_default_timezone_get();
			$this->PROJECTS[ $_POST['name'] ]['settings']['opt_ignore_query']        = 0;
			update_option( $this->PROJECTS_NAME . $loggedin_user[2], $this->PROJECTS );
			die( 'ok' );
			break;
		case 'delete': //project and data
			global $wpdb;
			$user   = current_user();
			$table1 = T_PREFIX . "main_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table1 WHERE `project` = '" . $_POST["name"] . "'" );
			$table2 = T_PREFIX . "clicks_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table2 WHERE `project` = '" . $_POST["name"] . "'" );
			$table3 = T_PREFIX . "mmove_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table3 WHERE `project` = '" . $_POST["name"] . "'" );
			$table4 = T_PREFIX . "scroll_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table4 WHERE `project` = '" . $_POST["name"] . "'" );
			$table5 = T_PREFIX . "popular_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table5 WHERE `project` = '" . $_POST["name"] . "'" );
			unset( $this->PROJECTS[ $_POST['name'] ] );
			update_option( $this->PROJECTS_NAME . $loggedin_user[2], $this->PROJECTS );
			die( 'ok' );
			break;
		case 'deletedata': //project data only
			global $wpdb;
			$user   = current_user();
			$table1 = T_PREFIX . "main_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table1 WHERE `project` = '" . $_POST["name"] . "'" );
			$table2 = T_PREFIX . "clicks_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table2 WHERE `project` = '" . $_POST["name"] . "'" );
			$table3 = T_PREFIX . "mmove_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table3 WHERE `project` = '" . $_POST["name"] . "'" );
			$table4 = T_PREFIX . "scroll_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table4 WHERE `project` = '" . $_POST["name"] . "'" );
			$table5 = T_PREFIX . "popular_" . $user->user_key;
			$wpdb->query( "DELETE FROM $table5 WHERE `project` = '" . $_POST["name"] . "'" );
			die( 'ok' );
			break;
		case 'add_ip':
			$option = $this->PROJECTS[ $_POST['opt_record_to'] ]["settings"];
			if ( ! isset( $option['opt_black_ips'] ) || ! is_array( $option['opt_black_ips'] ) ) {
				$option['opt_black_ips'] = array();
			}
			$option['opt_black_ips'][ $_POST['ip'] ]               = $_POST['ip'];
			$this->PROJECTS[ $_POST['opt_record_to'] ]["settings"] = $option;
			update_option( $this->PROJECTS_NAME . $loggedin_user[2], $this->PROJECTS );
			die( $_POST['ip'] );
			break;
		case 'del_ip':
			$option = $this->PROJECTS[ $_POST['opt_record_to'] ]["settings"];
			unset( $option['opt_black_ips'][ $_POST['ip'] ] );
			$this->PROJECTS[ $_POST['opt_record_to'] ]["settings"] = $option;
			update_option( $this->PROJECTS_NAME . $loggedin_user[2], $this->PROJECTS );
			die( 'ok' );
			break;
		case 'check_subscr':
			$is_closed = is_plan_closed( current_user(), $_POST['plan_id'] );
			die( $is_closed ? 'ok' : 'no' );
			break;
		case 'add_tracking_domain':
			$domain    = $_POST['domain'];
			$domains   = &$this->USER_DOMAINS['opt_tracking_domains'];
			$exists    = in_array( $domain, $domains );
			$has_slots = count( $domains ) < $this->USER_DOMAINS['opt_max_tracking_domains'];
			if ( ! $exists && $has_slots ) {
				$domains[] = $domain;
				update_option( $this->USER_DOMAINS_NAME . $loggedin_user[2], $this->USER_DOMAINS );
				die( $domain );
			} else if ( $exists ) {
				die( 'exists' );
			} else if ( ! $has_slots ) {
				die( 'overflow' );
			}
			break;
		case 'del_tracking_domain':
			$domain  = $_POST['domain'];
			$domains = &$this->USER_DOMAINS['opt_tracking_domains'];
			foreach ( $domains as $i => $d ) {
				if ( $d == $domain ) {
					unset( $domains[ $i ] );
					update_option( $this->USER_DOMAINS_NAME . $loggedin_user[2], $this->USER_DOMAINS );
					break;
				}
			}
			die( 'ok' );
			break;
		default:
			die( 'wrong option' );
			break;
	}
endif;

ensure_admin();

switch ( $_POST['action'] ) {
	case 'newpackage':
		$uid = create_guid( 'package' );
		unset( $_POST['action'] );
		$this->PACKAGES[ $uid ] = $_POST;
		$packages               = get_option( $this->PACKAGES_NAME );
		if ( $packages === false ) {
			add_option( $this->PACKAGES_NAME, $this->PACKAGES );
		} else {
			update_option( $this->PACKAGES_NAME, $this->PACKAGES );
		}
		die( 'ok' );
		break;
	case 'delpackage':
		unset( $this->PACKAGES[ $_POST['to_del'] ] );
		update_option( $this->PACKAGES_NAME, $this->PACKAGES );
		die( 'ok' );
		break;
	case 'savepackage':
		unset( $_POST['action'] );
		$this->PACKAGES[ $_POST['id'] ] = $_POST;
		update_option( $this->PACKAGES_NAME, $this->PACKAGES );
		die( 'ok' );
		break;
	case 'del_ban_ip':
		unset( $this->BANNED_LOGINS[ $_POST['ip'] ] );
		update_option( $this->BANNED_LOGINS_NAME, $this->BANNED_LOGINS );
		die( 'ok' );
		break;
	default:
		die( 'wrong option' );
		break;
}
