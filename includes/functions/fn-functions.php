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
function is_admin() {
	return user_role() == "admin";
}

function is_user() {
	return user_role() == "user";
}

/**
 * Detects if user not logged in and redirects to login page
 */
function ensure_logged_in() {
	global $loggedin_user;
	if ( ! is_user_logged_in( $loggedin_user ) ) {
		header( 'location: ' . admin_url() . '?login' );
		die();
	}
}

/**
 * Detects if user is not admin and redirects to login page or dies with message.
 */
function ensure_admin( $redirectToLogin = false ) {
	if ( ! is_admin() ) {
		if ( $redirectToLogin ) {
			header( 'location: ' . admin_url() . '?login' );
			die();
		} else {
			echo "here";
			die( "Only admin can access this section" );
		}
	}
}

function tables_created( $uid ) {
	global $wpdb;
	$table     = T_PREFIX . 'main_' . $uid;
	$structure = "SELECT 1 FROM $table";
	$res       = $wpdb->query( $structure );
	if ( ! $res ) {
		return false;
	} else {
		return true;
	}
}

function create_user_tables( $uid ) {
	global $wpdb;
	$error     = false;
	$error_msg = "";

	$table     = T_PREFIX . 'main_' . $uid;
	$structure = "CREATE TABLE IF NOT EXISTS $table (
			      id int(99) NOT NULL AUTO_INCREMENT,
			      user_id VARCHAR(200) DEFAULT '' NOT NULL,
			      project VARCHAR(200) DEFAULT '' NOT NULL,
			      session_id VARCHAR(200) DEFAULT '' NOT NULL,
			      session_spydata text,
			      session_start int(9) NOT NULL,
			      session_end int(9) NOT NULL,
			      session_time int(9) NOT NULL,
			      KEY project (project),
			      UNIQUE KEY id (id) 
			    ) ENGINE=InnoDB";
	$res       = $wpdb->query( $structure );

	if ( ! $res ) {
		$error     = true;
		$error_msg = "Database error";
	}

	$table_click = T_PREFIX . 'clicks_' . $uid;
	$structure2  = "CREATE TABLE IF NOT EXISTS $table_click (
			      id int(99) NOT NULL AUTO_INCREMENT,
			      project VARCHAR(200) DEFAULT '' NOT NULL,
			      date DATE NOT NULL,
			      page_url VARCHAR(500) DEFAULT '' NOT NULL,
			      click_data text,
			      UNIQUE KEY id (id),
			      KEY project (project),
			      KEY page_url (page_url),
				  KEY date (`date`)
			    ) ENGINE=InnoDB";
	$res         = $wpdb->query( $structure2 );

	if ( ! $res ) {
		$error     = true;
		$error_msg = "Database error";
	}

	$table_mmove = T_PREFIX . 'mmove_' . $uid;
	$structure3  = "CREATE TABLE IF NOT EXISTS $table_mmove (
			      id int(99) NOT NULL AUTO_INCREMENT,
			      project VARCHAR(200) DEFAULT '' NOT NULL,
			      date DATE NOT NULL,
			      page_url VARCHAR(500) DEFAULT '' NOT NULL,
			      mmove_data text,
			      UNIQUE KEY id (id),
			      KEY project (project),
			      KEY page_url (page_url),
				  KEY date (`date`)
			    ) ENGINE=InnoDB";
	$res         = $wpdb->query( $structure3 );

	if ( ! $res ) {
		$error     = true;
		$error_msg = "Database error";
	}

	$table_scroll = T_PREFIX . 'scroll_' . $uid;
	$structure4   = "CREATE TABLE IF NOT EXISTS $table_scroll (
			      id int(99) NOT NULL AUTO_INCREMENT,
			      project VARCHAR(200) DEFAULT '' NOT NULL,
			      date DATE NOT NULL,
			      page_url VARCHAR(500) DEFAULT '' NOT NULL,
			      scroll_data text,
			      UNIQUE KEY id (id),
			      KEY project (project),
			      KEY page_url (page_url),
				  KEY date (`date`)
			    ) ENGINE=InnoDB;";
	$res          = $wpdb->query( $structure4 );

	if ( ! $res ) {
		$error     = true;
		$error_msg = "Database error";
	}

	$table_ppopular = T_PREFIX . 'popular_' . $uid;
	$structure5     = "CREATE TABLE IF NOT EXISTS $table_ppopular (
			      id int(99) NOT NULL AUTO_INCREMENT,
			      project VARCHAR(200) DEFAULT '' NOT NULL,
			      date DATE NOT NULL,
			      page_url VARCHAR(500) DEFAULT '' NOT NULL,
			      points int(99) NOT NULL,
			      UNIQUE KEY id (id),
			      KEY project (project),
			      KEY page_url (page_url)
			    ) ENGINE=InnoDB;";
	$res            = $wpdb->query( $structure5 );

	if ( ! $res ) {
		$error     = true;
		$error_msg = "Database error";
	}

	return $error || $error_msg;
}

function user_statuses_explained( $user = null, $statuses = null ) {
	$user = empty( $user ) ? current_user() : $user;
	global $HMTrackerPro_USERSTATUS_NAME;
	$statuses = empty( $statuses ) ? get_option( $HMTrackerPro_USERSTATUS_NAME . $user->id ) : $statuses;

	$statuses = array(
		0 => 'Created. Your account will be activated as soon as we receive your first payment.',
		1 => sprintf( 'Active. Tracking for your projects is live up to %s hours.', $statuses[1][0] ),
		2 => sprintf( 'Pending. Your subscription payment has not yet been received. Your account will stop receiving tracking data in %s hours', $statuses[2][0] ),
		3 => 'Suspended. Tracking is disabled. Please make a payment to reactivate your account.',
		4 => 'Refunded. Your account is disabled due to a refund request. Please contact support for details.',
		5 => 'Deleted. Will have to redirect to login after here.',
		6 => 'Free',
		7 => 'Active. Free.',
		8 => 'Active.',
		9 => 'Suspended. Tracking is disabled. Please contact Administrator.'
	);

	return $statuses;
}

function show_status( $force = false ) {
	$user = current_user();
	ensure_plans_unserialized( $user );
	//print_r(compact('user'));

	$cur_status_id = detect_user_status( $user, $force );
	$statuses      = user_statuses_explained( $user );
	$cur_status    = user_status_name( $cur_status_id );

	if ( ! empty( $user ) ) {
		$flag = true;
	} else {
		$flag = false;
	}
	if ( $flag ) {

		switch ( $cur_status_id ) {
			case 0:
			case 2:
				$class = "label-warning";
				break;
			case 1:
			case 6:
			case 7:
			case 8:
				$class = "label-success";
				break;
			case 3:
			case 4:
			case 9:
				$class = "label-important";
				break;

			default:
				break;
		}

		?>
		<span class="label <?php echo $class; ?>"> "<?php echo $statuses[ $cur_status_id ] ?>" (<a
				href="<?php echo admin_url() ?>?upayments">details</a>) <?php echo( isset( $message ) ? $message : "" ); ?></span>

	<?php

	} else {
		echo "error";
	}

}

function curPageURL() {
	$pageURL = 'http';
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER["HTTPS"] == "on" ) {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ( $_SERVER["SERVER_PORT"] != "80" ) {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	$pageURL = explode( '?', $pageURL );

	return $pageURL[0];
}

function delete_option( $name ) {
	global $wpdb;
	$table_options = T_PREFIX . "options";
	$line          = $wpdb->query( "DELETE FROM $table_options WHERE `name`='" . $name . "'" );
}

function add_option( $name, $data ) {
	global $wpdb;
	$table_options = T_PREFIX . "options";
	$line          = $wpdb->query( "INSERT INTO $table_options (`data`,`name`) VALUES ('" . serialize( $data ) . "', '" . $name . "')" );
}

function update_option( $name, $data ) {
	global $wpdb;
	$table_options = T_PREFIX . "options";
	$line          = $wpdb->query( "UPDATE $table_options SET`data`='" . serialize( $data ) . "' WHERE `name` = '" . $name . "'" );
}

function get_option( $name ) {
	global $wpdb;
	$table_options = T_PREFIX . "options";
	try {
		$sdata = $wpdb->queryUniqueValue( "SELECT data FROM $table_options WHERE name='" . $name . "'" );
		if ( $sdata ) {
			return unserialize( $sdata );
		} else {
			return false;
		}
	} catch ( Exception $e ) {
		return false;
	}
}

function get_options( $names ) {
	if ( ! is_array( $names ) ) {
		return false;
	}

	$res = array();
	foreach ( $names as $name ) {
		$res[ $name ] = false;
	}

	global $wpdb;
	$table_options = T_PREFIX . "options";
	$str_names     = implode( "','", $names );
	$q             = "SELECT name, data
			FROM $table_options 
			WHERE name IN ('$str_names')";
//			die($q);
	$sdata = $wpdb->get_results( $q, - 1, true );
	if ( $sdata ) {
		foreach ( $sdata as $row ) {
			try {
				$res[ $row->name ] = unserialize( $row->data );
			} catch ( Exception $e ) {
			}
		}
	}
	unset( $sdata );

	return $res;
}

function wp_maill( $to, $subject, $message ) {
	require_once( dirname( __FILE__ ) . '/../swift/lib/swift_required.php' );
	switch ( PHP_MAILER_TRANSPORT ) {
		case 1:
			if ( ! PHP_MAILER_SSL ) {
				$transport = Swift_SmtpTransport::newInstance( PHP_MAILER_SERVER, PHP_MAILER_PORT )
				                                ->setUsername( PHP_MAILER_USERNAME )
				                                ->setPassword( PHP_MAILER_PASSWORD );
			}
			if ( PHP_MAILER_SSL ) {
				$transport = Swift_SmtpTransport::newInstance( PHP_MAILER_SERVER, PHP_MAILER_PORT, 'ssl' )
				                                ->setUsername( PHP_MAILER_USERNAME )
				                                ->setPassword( PHP_MAILER_PASSWORD );
			}

			break;
		case 2:
			$transport = Swift_SendmailTransport::newInstance( '/usr/sbin/sendmail -bs' );
			break;
		case 3:
			$transport = Swift_MailTransport::newInstance();
			break;
	}

	$mailer = Swift_Mailer::newInstance( $transport );

	$message = Swift_Message::newInstance( $subject )
	                        ->setFrom( array( PHP_MAILER_SENDER_EMAIL => PHP_MAILER_SENDER_NAME ) )
	                        ->setTo( array( $to ) )
	                        ->setBody( $message );
	//echo "$to\n;$subject\n;$message\n;";
	$result = $mailer->send( $message );
}

function includes_url() {
}

function base_dir( $is_url = false ) {
	$base_dir = dirname( $_SERVER['PHP_SELF'] );
	$ending   = substr( $base_dir, - 1 );
	if ( $ending != DIRECTORY_SEPARATOR ) {
		$base_dir .= DIRECTORY_SEPARATOR;
	}

	if ( $is_url ) {
		return str_replace( DIRECTORY_SEPARATOR, '/', $base_dir );
	}

	return $base_dir;
}

function siteURL( $base_url = true, $trailing_slash = true, $makessl = false, $pathonly = false ) {
	$pageURL = 'http';
	if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
		$_SERVER['HTTPS'] = 'on';
	}
	if ( ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) || $makessl ) {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ( $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ) {
		$pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"];
	} else {
		$pageURL .= $_SERVER["HTTP_HOST"];
	}
	$pageURL .= base_dir( true );
	if ( $base_url ) {
		if ( substr( $pageURL, - 1 ) != DIRECTORY_SEPARATOR && $trailing_slash ) {
			$pageURL .= "/";
		}
	} else {
		$pageURL .= "?" . $_SERVER["QUERY_STRING"];
	}
	if ( ! $pathonly ) {
		return $pageURL;
	} else {
		return preg_replace( '%' . basename( $_SERVER['PHP_SELF'] ) . '.*%', '', $pageURL );
	}
}

function admin_url() {
	return siteURL( true, false );
}

function home_url() {
	return admin_url();
}

function wp_remote_get( $url ) {
	try {
		$ch = @ curl_init();
		@ curl_setopt( $ch, CURLOPT_URL, $url );
		@ curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		@ curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		@ curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		@ curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		@ curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = @ curl_exec( $ch );
	} catch ( Exception $e ) {
		$result = file_get_contents( $url );
	}

	return $result;
}

//error_reporting(E_ERROR);
function doGet( $sURL, $asVars, $bRequireResult = false ) {
	$sURL .= '?' . http_build_query( $asVars );
	$sResponse = wp_remote_get( $sURL );
	if ( $bRequireResult ) {
		if ( empty( $sResponse ) ) {
			sleep( 1 );
			$sResponse = wp_remote_get( $sURL );
			if ( $bRequireResult ) {
				if ( empty( $sResponse ) ) {
					sleep( 2 );
					$sResponse = wp_remote_get( $sURL );
				}
			}
		}
	}

	return trim( $sResponse );
}


class HMTrackerFN {
//secure post variables
	public static function hmtracker_secure( $string ) {

		// Checking if this PHP installation automatically adds slashes, remove them if so
		( get_magic_quotes_gpc() ) ? $string = stripslashes( $string ) : $string = $string;

		$string = trim( $string );
		$string = strip_tags( $string );

		//$string = htmlspecialchars($string); - htmlentities() covers more characters
		$string = htmlentities( $string );

		$string = @mysql_real_escape_string( $string );

		return $string;
	}

//seconds to hh:mm:ss
	public static function sec2hms( $sec, $padHours = true ) {

		$hms = "";

		// there are 3600 seconds in an hour, so if we
		// divide total seconds by 3600 and throw away
		// the remainder, we've got the number of hours
		$hours = intval( intval( $sec ) / 3600 );

		// add to $hms, with a leading 0 if asked for
		$hms .= ( $padHours )
			? str_pad( $hours, 2, "0", STR_PAD_LEFT ) . ':'
			: $hours . ':';

		// dividing the total seconds by 60 will give us
		// the number of minutes, but we're interested in
		// minutes past the hour: to get that, we need to
		// divide by 60 again and keep the remainder
		$minutes = intval( ( $sec / 60 ) % 60 );

		// then add to $hms (with a leading 0 if needed)
		$hms .= str_pad( $minutes, 2, "0", STR_PAD_LEFT ) . ':';

		// seconds are simple - just divide the total
		// seconds by 60 and keep the remainder
		$seconds = intval( $sec % 60 );

		// add to $hms, again with a leading 0 if needed
		$hms .= str_pad( $seconds, 2, "0", STR_PAD_LEFT );

		return $hms;
	}

//get user ip
	public static function getRealIp() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}


	public static function browser_detection( $which_test, $test_excludes = '' ) {
		/*
		uncomment the global variable declaration if you want the variables to be available on
		a global level throughout your php page, make sure that php is configured to support
		the use of globals first!
		Use of globals should be avoided however, and they are not necessary with this script
		/*
		/*
		global $dom_browser, $safe_browser, $browser_user_agent, $browser_name, $s_browser, $ie_version, $true_msie_version, $browser_version_number, $mobile_test, $a_mobile_data, $os_number, $os_type, $b_repeat, $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date, $math_version_number, $ua_type, $webkit_type, $webkit_type_number;
		*/

		static $dom_browser, $safe_browser, $browser_user_agent, $browser_name, $s_browser, $ie_version, $true_msie_version, $browser_version_number, $mobile_test, $a_mobile_data, $os_number, $os_type, $b_repeat, $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date, $math_version_number, $ua_type, $webkit_type, $webkit_type_number;

		/*
		this makes the test only run once no matter how many times you call it since
		all the variables are filled on the first run through, it's only a matter of
		returning the the right ones
		*/
		if ( ! $b_repeat ) {
			//initialize all variables with default values to prevent error
			$dom_browser            = false;
			$ua_type                = 'bot'; // default to bot since you never know with bots
			$safe_browser           = false;
			$a_os_data              = '';
			$os_number              = '';
			$os_type                = '';
			$browser_name           = '';
			$browser_version_number = '';
			$math_version_number    = '';
			$a_math_version_number  = '';
			$ie_version             = '';
			$true_msie_version      = '';
			$mobile_test            = '';
			$a_mobile_data          = '';
			$a_moz_data             = '';
			$moz_type               = '';
			$moz_version_number     = '';
			$moz_rv                 = '';
			$moz_rv_full            = '';
			$moz_release_date       = '';
			$a_unhandled_browser    = '';
			$a_webkit_data          = '';
			$webkit_type            = '';
			$webkit_type_number     = '';
			$b_success              = false; // boolean for if browser found in main test
			$b_os_test              = true;
			$b_mobile_test          = true;

			// set the excludes if required
			if ( $test_excludes ) {
				switch ( $test_excludes ) {
					case '1':
						$b_os_test = false;
						break;
					case '2':
						$b_mobile_test = false;
						break;
					case '3':
						$b_os_test     = false;
						$b_mobile_test = false;
						break;
					default:
						die( 'Error: bad $test_excludes parameter used: ' . $test_excludes );
						break;
				}
			}

			/*
			make navigator user agent string lower case to make sure all versions get caught
			isset protects against blank user agent failure. tolower also lets the script use
			strstr instead of stristr, which drops overhead slightly.
			*/
			$browser_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
			/*
			pack the browser type array, in this order
			the order is important, because opera must be tested first, then omniweb [which has safari
			data in string], same for konqueror, then safari, then gecko, since safari navigator user
			agent id's with 'gecko' in string.
			Note that $dom_browser is set for all  modern dom browsers, this gives you a default to use.

			array[0] = id string for useragent, array[1] is if dom capable, array[2] is working name
			for browser, array[3] identifies navigator useragent type

			Note: all browser strings are in lower case to match the strtolower output, this avoids
			possible detection errors

			Note: These are the navigator user agent types:
			bro - modern, css supporting browser.
			bbro - basic browser, text only, table only, defective css implementation
			bot - search type spider
			dow - known download agent
			lib - standard http libraries
			mobile - handheld or mobile browser, set using $mobile_test
			*/
			// known browsers, list will be updated routinely, check back now and then
			$a_browser_types = array(
				array( 'opera', true, 'op', 'bro' ),
				array( 'msie', true, 'ie', 'bro' ),
				// webkit before gecko because some webkit ua strings say: like gecko
				array( 'webkit', true, 'webkit', 'bro' ),
				// konq will be using webkit soon
				array( 'konqueror', true, 'konq', 'bro' ),
				// covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
				array( 'gecko', true, 'moz', 'bro' ),
				array( 'netpositive', false, 'netp', 'bbro' ), // beos browser
				array( 'lynx', false, 'lynx', 'bbro' ), // command line browser
				array( 'elinks ', false, 'elinks', 'bbro' ), // new version of links
				array( 'elinks', false, 'elinks', 'bbro' ), // alternate id for it
				array( 'links2', false, 'links2', 'bbro' ), // alternate links version
				array( 'links ', false, 'links', 'bbro' ), // old name for links
				array( 'links', false, 'links', 'bbro' ), // alternate id for it
				array( 'w3m', false, 'w3m', 'bbro' ), // open source browser, more features than lynx/links
				array( 'webtv', false, 'webtv', 'bbro' ), // junk ms webtv
				array( 'amaya', false, 'amaya', 'bbro' ), // w3c browser
				array( 'dillo', false, 'dillo', 'bbro' ), // linux browser, basic table support
				array( 'ibrowse', false, 'ibrowse', 'bbro' ), // amiga browser
				array( 'icab', false, 'icab', 'bro' ), // mac browser
				array( 'crazy browser', true, 'ie', 'bro' ), // uses ie rendering engine

				// search engine spider bots:
				array( 'googlebot', false, 'google', 'bot' ), // google
				array( 'mediapartners-google', false, 'adsense', 'bot' ), // google adsense
				array( 'yahoo-verticalcrawler', false, 'yahoo', 'bot' ), // old yahoo bot
				array( 'yahoo! slurp', false, 'yahoo', 'bot' ), // new yahoo bot
				array( 'yahoo-mm', false, 'yahoomm', 'bot' ), // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
				array( 'inktomi', false, 'inktomi', 'bot' ), // inktomi bot
				array( 'slurp', false, 'inktomi', 'bot' ), // inktomi bot
				array( 'fast-webcrawler', false, 'fast', 'bot' ), // Fast AllTheWeb
				array( 'msnbot', false, 'msn', 'bot' ), // msn search
				array( 'ask jeeves', false, 'ask', 'bot' ), //jeeves/teoma
				array( 'teoma', false, 'ask', 'bot' ), //jeeves teoma
				array( 'scooter', false, 'scooter', 'bot' ), // altavista
				array( 'openbot', false, 'openbot', 'bot' ), // openbot, from taiwan
				array( 'ia_archiver', false, 'ia_archiver', 'bot' ), // ia archiver
				array( 'zyborg', false, 'looksmart', 'bot' ), // looksmart
				array( 'almaden', false, 'ibm', 'bot' ), // ibm almaden web crawler
				array( 'baiduspider', false, 'baidu', 'bot' ), // Baiduspider asian search spider
				array( 'psbot', false, 'psbot', 'bot' ), // psbot image crawler
				array( 'gigabot', false, 'gigabot', 'bot' ), // gigabot crawler
				array( 'naverbot', false, 'naverbot', 'bot' ), // naverbot crawler, bad bot, block
				array( 'surveybot', false, 'surveybot', 'bot' ), //
				array( 'boitho.com-dc', false, 'boitho', 'bot' ), //norwegian search engine
				array( 'objectssearch', false, 'objectsearch', 'bot' ), // open source search engine
				array( 'answerbus', false, 'answerbus', 'bot' ), // http://www.answerbus.com/, web questions
				array( 'sohu-search', false, 'sohu', 'bot' ), // chinese media company, search component
				array( 'iltrovatore-setaccio', false, 'il-set', 'bot' ),
				// various http utility libaries
				array( 'w3c_validator', false, 'w3c', 'lib' ), // uses libperl, make first
				array( 'wdg_validator', false, 'wdg', 'lib' ), //
				array( 'libwww-perl', false, 'libwww-perl', 'lib' ),
				array( 'jakarta commons-httpclient', false, 'jakarta', 'lib' ),
				array( 'python-urllib', false, 'python-urllib', 'lib' ),
				// download apps
				array( 'getright', false, 'getright', 'dow' ),
				array( 'wget', false, 'wget', 'dow' ), // open source downloader, obeys robots.txt

				// netscape 4 and earlier tests, put last so spiders don't get caught
				array( 'mozilla/4.', false, 'ns', 'bbro' ),
				array( 'mozilla/3.', false, 'ns', 'bbro' ),
				array( 'mozilla/2.', false, 'ns', 'bbro' )
			);

			//array( '', false ); // browser array template

			/*
			moz types array
			note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
			rv comes last in case it is plain old mozilla. firefox/netscape/seamonkey need to be later
			*/
			$a_moz_types = array( 'camino', 'epiphany', 'firebird', 'flock', 'galeon', 'k-meleon', 'minimo', 'multizilla', 'phoenix', 'swiftfox', 'iceape', 'seamonkey', 'iceweasel', 'firefox', 'netscape6', 'netscape', 'rv' );

			/*
			webkit types, this is going to expand over time as webkit browsers spread
			konqueror is probably going to move to webkit, so this is preparing for that
			It will now default to khtml. gtklauncher is the temp id for epiphany, might
			change. Defaults to applewebkit, and will all show the webkit number.
			*/
			$a_webkit_types = array( 'arora', 'chrome', 'epiphany', 'gtklauncher', 'konqueror', 'midori', 'omniweb', 'safari', 'uzbl', 'applewebkit', 'webkit' );

			/*
			run through the browser_types array, break if you hit a match, if no match, assume old browser
			or non dom browser, assigns false value to $b_success.
			*/
			$i_count = count( $a_browser_types );
			for ( $i = 0; $i < $i_count; $i ++ ) {
				//unpacks browser array, assigns to variables
				$s_browser = $a_browser_types[ $i ][0]; // text string to id browser from array

				if ( strstr( $browser_user_agent, $s_browser ) ) {
					/*
					it defaults to true, will become false below if needed
					this keeps it easier to keep track of what is safe, only
					explicit false assignment will make it false.
					*/
					$safe_browser = true;

					// assign values based on match of user agent string
					$dom_browser  = $a_browser_types[ $i ][1]; // hardcoded dom support from array
					$browser_name = $a_browser_types[ $i ][2]; // working name for browser
					$ua_type      = $a_browser_types[ $i ][3]; // sets whether bot or browser

					switch ( $browser_name ) {
						// this is modified quite a bit, now will return proper netscape version number
						// check your implementation to make sure it works
						case 'ns':
							$safe_browser           = false;
							$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, 'mozilla' );
							break;
						case 'moz':
							/*
							note: The 'rv' test is not absolute since the rv number is very different on
							different versions, for example Galean doesn't use the same rv version as Mozilla,
							neither do later Netscapes, like 7.x. For more on this, read the full mozilla
							numbering conventions here: http://www.mozilla.org/releases/cvstags.html
							*/
							// this will return alpha and beta version numbers, if present
							$moz_rv_full = HMTrackerFN::get_item_version( $browser_user_agent, 'rv' );
							// this slices them back off for math comparisons
							$moz_rv = substr( $moz_rv_full, 0, 3 );

							// this is to pull out specific mozilla versions, firebird, netscape etc..
							$j_count = count( $a_moz_types );
							for ( $j = 0; $j < $j_count; $j ++ ) {
								if ( strstr( $browser_user_agent, $a_moz_types[ $j ] ) ) {
									$moz_type           = $a_moz_types[ $j ];
									$moz_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $moz_type );
									break;
								}
							}
							/*
							this is necesary to protect against false id'ed moz'es and new moz'es.
							this corrects for galeon, or any other moz browser without an rv number
							*/
							if ( ! $moz_rv ) {
								// you can use this if you are running php >= 4.2
								if ( function_exists( 'floatval' ) ) {
									$moz_rv = floatval( $moz_version_number );
								} else {
									$moz_rv = substr( $moz_version_number, 0, 3 );
								}
								$moz_rv_full = $moz_version_number;
							}
							// this corrects the version name in case it went to the default 'rv' for the test
							if ( $moz_type == 'rv' ) {
								$moz_type = 'mozilla';
							}

							//the moz version will be taken from the rv number, see notes above for rv problems
							$browser_version_number = $moz_rv;
							// gets the actual release date, necessary if you need to do functionality tests
							$moz_release_date = HMTrackerFN::get_item_version( $browser_user_agent, 'gecko/' );
							/*
							Test for mozilla 0.9.x / netscape 6.x
							test your javascript/CSS to see if it works in these mozilla releases, if it does, just default it to:
							$safe_browser = true;
							*/
							if ( ( $moz_release_date < 20020400 ) || ( $moz_rv < 1 ) ) {
								$safe_browser = false;
							}
							break;
						case 'ie':
							/*
							note we're adding in the trident/ search to return only first instance in case
							of msie 8, and we're triggering the  break last condition in the test, as well
							as the test for a second search string, trident/
							*/
							$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $s_browser, true, 'trident/' );
							// construct the proper real number if it's in compat mode and msie 8.0
							if ( strstr( $browser_version_number, '7.' ) && strstr( $browser_user_agent, 'trident/4' ) ) {
								// note that 7.0 becomes 8 when adding 1, but if it's 7.1 it will be 8.1
								$true_msie_version = $browser_version_number + 1;
							}
							// test for most modern msie instances
							if ( $browser_version_number >= 7 ) {
								$ie_version = 'ie7x';
							} // then test for IE 5x mac, that's the most problematic IE out there
							elseif ( strstr( $browser_user_agent, 'mac' ) ) {
								$ie_version = 'ieMac';
							} // this assigns a general ie id to the $ie_version variable
							elseif ( $browser_version_number >= 5 ) {
								$ie_version = 'ie5x';
							} elseif ( ( $browser_version_number > 3 ) && ( $browser_version_number < 5 ) ) {
								$dom_browser = false;
								$ie_version  = 'ie4';
								// this depends on what you're using the script for, make sure this fits your needs
								$safe_browser = true;
							} else {
								$ie_version   = 'old';
								$dom_browser  = false;
								$safe_browser = false;
							}
							break;
						case 'op':
							$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $s_browser );
							if ( $browser_version_number < 5 ) // opera 4 wasn't very useable.
							{
								$safe_browser = false;
							}
							break;
						/*
						note: webkit returns always the webkit version number, not the specific user
						agent version, ie, webkit 583, not chrome 0.3
						*/
						case 'webkit':
							// note that this is the Webkit version number
							$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $s_browser );
							// this is to pull out specific webkit versions, safari, google-chrome etc..
							$j_count = count( $a_webkit_types );
							for ( $j = 0; $j < $j_count; $j ++ ) {
								if ( strstr( $browser_user_agent, $a_webkit_types[ $j ] ) ) {
									$webkit_type = $a_webkit_types[ $j ];
									// and this is the webkit type version number, like: chrome 1.2
									$webkit_type_number = HMTrackerFN::get_item_version( $browser_user_agent, $webkit_type );
									// epiphany hack
									if ( $a_webkit_types[ $j ] == 'gtklauncher' ) {
										$s_browser = 'Epiphany';
									} else {
										$s_browser = $a_webkit_types[ $j ];
									}
									break;
								}
							}
							break;
						default:
							$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $s_browser );
							break;
					}
					// the browser was id'ed
					$b_success = true;
					break;
				}
			}

			//assigns defaults if the browser was not found in the loop test
			if ( ! $b_success ) {
				/*
				this will return the first part of the browser string if the above id's failed
				usually the first part of the browser string has the navigator useragent name/version in it.
				This will usually correctly id the browser and the browser number if it didn't get
				caught by the above routine.
				If you want a '' to do a if browser == '' type test, just comment out all lines below
				except for the last line, and uncomment the last line. If you want undefined values,
				the browser_name is '', you can always test for that
				*/
				// delete this part if you want an unknown browser returned
				$s_browser = substr( $browser_user_agent, 0, strcspn( $browser_user_agent, '();' ) );
				// this extracts just the browser name from the string, if something usable was found
				if ( $s_browser && preg_match( '/[^0-9][a-z]*-*\ *[a-z]*\ *[a-z]*/', $s_browser, $a_unhandled_browser ) ) {
					$s_browser              = $a_unhandled_browser[0];
					$browser_version_number = HMTrackerFN::get_item_version( $browser_user_agent, $s_browser );
				} else {
					$s_browser              = 'NA';
					$browser_version_number = 'NA';
				}

				// then uncomment this part
				//$s_browser = '';//deletes the last array item in case the browser was not a match
			}
			// get os data, mac os x test requires browser/version information, this is a change from older scripts
			if ( $b_os_test ) {
				$a_os_data = HMTrackerFN::get_os_data( $browser_user_agent, $browser_name, $browser_version_number );
				$os_type   = $a_os_data[0]; // os name, abbreviated
				$os_number = $a_os_data[1]; // os number or version if available
			}
			/*
			this ends the run through once if clause, set the boolean
			to true so the function won't retest everything
			*/
			$b_repeat = true;
			/*
			pulls out primary version number from more complex string, like 7.5a,
			use this for numeric version comparison
			*/
			if ( $browser_version_number && preg_match( '/[0-9]*\.*[0-9]*/', $browser_version_number, $a_math_version_number ) ) {
				$math_version_number = $a_math_version_number[0];
				//print_r($a_math_version_number);
			}
			if ( $b_mobile_test ) {
				$mobile_test = HMTrackerFN::check_is_mobile( $browser_user_agent );
				if ( $mobile_test ) {
					$a_mobile_data = HMTrackerFN::get_mobile_data( $browser_user_agent );
					$ua_type       = 'mobile';
				}
			}
		}
		//$browser_version_number = $_SERVER["REMOTE_ADDR"];
		/*
		This is where you return values based on what parameter you used to call the function
		$which_test is the passed parameter in the initial browser_detection('os') for example call
		*/
		// assemble these first so they can be included in full return data
		$a_moz_data    = array( $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date );
		$a_webkit_data = array( $webkit_type, $webkit_type_number, $browser_version_number );

		switch ( $which_test ) {
			case 'safe': // returns true/false if your tests determine it's a safe browser
				/*
				you can change the tests to determine what is a safeBrowser for your scripts
				in this case sub rv 1 Mozillas and Netscape 4x's trigger the unsafe condition
				*/
				return $safe_browser;
				break;
			case 'ie_version': // returns ieMac or ie5x
				return $ie_version;
				break;
			case 'moz_version': // returns array of all relevant moz information
				return $a_moz_data;
				break;
			case 'webkit_version': // returns array of all relevant webkit information
				return $a_webkit_data;
				break;
			case 'dom': // returns true/fale if a DOM capable browser
				return $dom_browser;
				break;
			case 'os': // returns os name
				return $os_type;
				break;
			case 'os_number': // returns os number if windows
				return $os_number;
				break;
			case 'browser': // returns browser name
				return $browser_name;
				break;
			case 'number': // returns browser number
				return $browser_version_number;
				break;
			case 'full': // returns all relevant browser information in an array
				$a_full_data = array(
					$browser_name,
					$browser_version_number,
					$ie_version,
					$dom_browser,
					$safe_browser,
					$os_type,
					$os_number,
					$s_browser,
					$ua_type,
					$math_version_number,
					$a_moz_data,
					$a_webkit_data,
					$mobile_test,
					$a_mobile_data,
					$true_msie_version
				);

				// print_r( $a_full_data );
				return $a_full_data;
				break;
			case 'type': // returns what type, bot, browser, maybe downloader in future
				return $ua_type;
				break;
			case 'math_number': // returns numerical version number, for number comparisons
				return $math_version_number;
				break;
			case 'mobile_test':
				return $mobile_test;
				break;
			case 'mobile_data':
				return $a_mobile_data;
				break;
			case 'true_msie_version':
				return $true_msie_version;
				break;
			default:
				break;
		}
	}

// gets which os from the browser string

	public static function get_os_data( $pv_browser_string, $pv_browser_name, $pv_version_number ) {
		// initialize variables
		$os_working_type   = '';
		$os_working_number = '';
		/*
		packs the os array. Use this order since some navigator user agents will put 'macintosh'
		in the navigator user agent string which would make the nt test register true
		*/
		$a_mac = array( 'intel mac', 'ppc mac', 'mac68k' ); // this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix_types = array(
			'freebsd',
			'openbsd',
			'netbsd',
			'bsd',
			'unixware',
			'solaris',
			'sunos',
			'sun4',
			'sun5',
			'suni86',
			'sun',
			'irix5',
			'irix6',
			'irix',
			'hpux9',
			'hpux10',
			'hpux11',
			'hpux',
			'hp-ux',
			'aix1',
			'aix2',
			'aix3',
			'aix4',
			'aix5',
			'aix',
			'sco',
			'unixware',
			'mpras',
			'reliant',
			'dec',
			'sinix',
			'unix'
		);
		// only sometimes will you get a linux distro to id itself...
		$a_linux_distros = array(
			'ubuntu',
			'kubuntu',
			'xubuntu',
			'mepis',
			'xandros',
			'linspire',
			'winspire',
			'sidux',
			'kanotix',
			'debian',
			'opensuse',
			'suse',
			'fedora',
			'redhat',
			'slackware',
			'slax',
			'mandrake',
			'mandriva',
			'gentoo',
			'sabayon',
			'linux'
		);
		$a_linux_process = array( 'i386', 'i586', 'i686' ); // not use currently
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os_types = array( 'android', 'blackberry', 'iphone', 'ipad', 'ipod', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros );

		//os tester
		$i_count = count( $a_os_types );
		for ( $i = 0; $i < $i_count; $i ++ ) {
			// unpacks os array, assigns to variable $a_os_working
			$os_working_data = $a_os_types[ $i ];
			/*
			assign os to global os variable, os flag true on success
			!strstr($pv_browser_string, "linux" ) corrects a linux detection bug
			*/
			if ( ! is_array( $os_working_data ) && strstr( $pv_browser_string, $os_working_data ) && ! strstr( $pv_browser_string, "linux" ) ) {
				$os_working_type = $os_working_data;
				switch ( $os_working_type ) {
					// most windows now uses: NT X.Y syntax
					case 'nt':
						if ( strstr( $pv_browser_string, 'nt 6.2' ) ) // windows 7
						{
							/*$os_working_number = 6.1;
							$os_working_type = 'nt';*/
							$os_working_number = 8;
							$os_working_type   = 'WIN';

						} elseif ( strstr( $pv_browser_string, 'nt 6.1' ) ) // windows 7
						{
							/*$os_working_number = 6.1;
							$os_working_type = 'nt';*/
							$os_working_number = 7;
							$os_working_type   = 'WIN';

						} elseif ( strstr( $pv_browser_string, 'nt 6.0' ) ) // windows vista/server 2008
						{
							/*$os_working_number = 6.0;
							$os_working_type = 'nt';*/
							$os_working_type = 'WIN VISTA';
						} elseif ( strstr( $pv_browser_string, 'nt 5.2' ) ) // windows server 2003
						{
							$os_working_number = 2003;
							$os_working_type   = 'WIN SERV';
						} elseif ( strstr( $pv_browser_string, 'nt 5.1' ) || strstr( $pv_browser_string, 'xp' ) ) // windows xp
						{
							$os_working_type = 'WIN XP';
						} elseif ( strstr( $pv_browser_string, 'nt 5' ) || strstr( $pv_browser_string, '2000' ) ) // windows 2000
						{
							$os_working_number = 5.0;
						} elseif ( strstr( $pv_browser_string, 'nt 4' ) ) // nt 4
						{
							$os_working_number = 4;
						} elseif ( strstr( $pv_browser_string, 'nt 3' ) ) // nt 4
						{
							$os_working_number = 3;
						}
						break;
					case 'win':
						if ( strstr( $pv_browser_string, 'vista' ) ) // windows vista, for opera ID
						{
							$os_working_number = 6.0;
							$os_working_type   = 'nt';
						} elseif ( strstr( $pv_browser_string, 'xp' ) ) // windows xp, for opera ID
						{
							$os_working_number = 5.1;
							$os_working_type   = 'nt';
						} elseif ( strstr( $pv_browser_string, '2003' ) ) // windows server 2003, for opera ID
						{
							$os_working_number = 5.2;
							$os_working_type   = 'nt';
						} elseif ( strstr( $pv_browser_string, 'windows ce' ) ) // windows CE
						{
							$os_working_number = 'ce';
							$os_working_type   = 'nt';
						} elseif ( strstr( $pv_browser_string, '95' ) ) {
							$os_working_number = '95';
						} elseif ( ( strstr( $pv_browser_string, '9x 4.9' ) ) || ( strstr( $pv_browser_string, 'me' ) ) ) {
							$os_working_number = 'me';
						} elseif ( strstr( $pv_browser_string, '98' ) ) {
							$os_working_number = '98';
						} elseif ( strstr( $pv_browser_string, '2000' ) ) // windows 2000, for opera ID
						{
							$os_working_number = 5.0;
							$os_working_type   = 'nt';
						}
						break;
					case 'mac':
						if ( strstr( $pv_browser_string, 'os x' ) ) {
							$os_working_number = 'os x';
						} /*
					this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
					are only made for os x
					*/
						elseif ( ( $pv_browser_name == 'saf' ) || ( $pv_browser_name == 'cam' ) ||
						         ( ( $pv_browser_name == 'moz' ) && ( $pv_version_number >= 1.3 ) ) ||
						         ( ( $pv_browser_name == 'ie' ) && ( $pv_version_number >= 5.2 ) )
						) {
							$os_working_number = 10;
						}
						break;
					case 'iphone':
					case 'ipad':
						$os_working_number = "OS " . preg_replace( "/(.*) os ([0-9]*)_([0-9]*)(.*)/", "$2_$3", $pv_browser_string );
						break;
					default:
						break;
				}
				break;
			} /*
		check that it's an array, check it's the second to last item
		in the main os array, the unix one that is
		*/
			elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 2 ) ) ) {
				$j_count = count( $os_working_data );
				for ( $j = 0; $j < $j_count; $j ++ ) {
					if ( strstr( $pv_browser_string, $os_working_data[ $j ] ) ) {
						$os_working_type   = 'unix'; //if the os is in the unix array, it's unix, obviously...
						$os_working_number = ( $os_working_data[ $j ] != 'unix' ) ? $os_working_data[ $j ] : ''; // assign sub unix version from the unix array
						break;
					}
				}
			} /*
		check that it's an array, check it's the last item
		in the main os array, the linux one that is
		*/
			elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 1 ) ) ) {
				$j_count = count( $os_working_data );
				for ( $j = 0; $j < $j_count; $j ++ ) {
					if ( strstr( $pv_browser_string, $os_working_data[ $j ] ) ) {
						$os_working_type = 'lin';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$os_working_number = ( $os_working_data[ $j ] != 'linux' ) ? $os_working_data[ $j ] : '';
						break;
					}
				}
			}
		}

		// pack the os data array for return to main function
		$a_os_data = array( $os_working_type, $os_working_number );

		return $a_os_data;
	}

	/*
	Function Info:
	function returns browser number, gecko rv number, or gecko release date
	function get_item_version( $browser_user_agent, $search_string, $substring_length )
	$pv_extra_search='' allows us to set an additional search/exit loop parameter, but we
	only want this running when needed
	*/
	public static function get_item_version( $pv_browser_user_agent, $pv_search_string, $pv_b_break_last = '', $pv_extra_search = '' ) {
		// 12 is the longest that will be required, handles release dates: 20020323; 0.8.0+
		$substring_length = 12;
		$start_pos        = 0; // set $start_pos to 0 for first iteration
		//initialize browser number, will return '' if not found
		$string_working_number = '';

		/*
		use the passed parameter for $pv_search_string
		start the substring slice right after these moz search strings
		there are some cases of double msie id's, first in string and then with then number
		$start_pos = 0;
		this test covers you for multiple occurrences of string, only with ie though
		with for example google bot you want the first occurance returned, since that's where the
		numbering happens
		*/
		for ( $i = 0; $i < 4; $i ++ ) {
			//start the search after the first string occurrence
			if ( strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) !== false ) {
				// update start position if position found
				$start_pos = strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) + strlen( $pv_search_string );
				/*
				msie (and maybe other userAgents requires special handling because some apps inject
				a second msie, usually at the beginning, custom modes allow breaking at first instance
				if $pv_b_break_last $pv_extra_search conditions exist. Since we only want this test
				to run if and only if we need it, it's triggered by caller passing these values.
				*/
				if ( ! $pv_b_break_last || ( $pv_extra_search && strstr( $pv_browser_user_agent, $pv_extra_search ) ) ) {
					break;
				}
			} else {
				break;
			}
		}
		/*
		this is just to get the release date, not other moz information
		also corrects for the omniweb 'v'
		*/
		if ( $pv_search_string != 'gecko/' ) {
			if ( $pv_search_string == 'omniweb' ) {
				$start_pos += 2; // handles the v in 'omniweb/v532.xx
			} else {
				$start_pos ++;
			}
		}

		// Initial trimming
		$string_working_number = substr( $pv_browser_user_agent, $start_pos, $substring_length );

		// Find the space, ;, or parentheses that ends the number
		$string_working_number = substr( $string_working_number, 0, strcspn( $string_working_number, ' );' ) );

		//make sure the returned value is actually the id number and not a string
		// otherwise return ''
		if ( ! is_numeric( substr( $string_working_number, 0, 1 ) ) ) {
			$string_working_number = '';
		}

		//$browser_number = strrpos( $pv_browser_user_agent, $pv_search_string );
		return $string_working_number;
	}

	/*
	Special ID notes:
	Novarra-Vision is a Content Transformation Server (CTS)
	*/
	public static function check_is_mobile( $pv_browser_user_agent ) {
		$mobile_working_test = '';
		/*
		these will search for basic mobile hints, this should catch most of them, first check
		known hand held device os, then check device names, then mobile browser names
		This list is almost the same but not exactly as the 4 arrays in function below
		*/
		$a_mobile_search = array(
			// os
			'android',
			'epoc',
			'linux armv',
			'palmos',
			'palmsource',
			'windows ce',
			'symbianos',
			'symbian os',
			'symbian',
			// devices
			'benq',
			'blackberry',
			'danger hiptop',
			'ddipocket',
			'iphone',
			'kindle',
			'lge-cx',
			'lge-lx',
			'lge-mx',
			'lge vx',
			'lge ',
			'lge-',
			'lg;lx',
			'nintendo wii',
			'nokia',
			'palm',
			'pdxgw',
			'playstation',
			'sagem',
			'samsung',
			'sec-sgh',
			'sharp',
			'sonyericsson',
			'sprint',
			'vodaphone',
			'j-phone',
			'n410',
			'mot 24',
			'mot-',
			'htc-',
			'htc_',
			'sec-',
			'sie-m',
			'sie-s',
			'spv ',
			'smartphone',
			'armv',
			'midp',
			'mobilephone',
			// browsers
			'avantgo',
			'blazer',
			'elaine',
			'eudoraweb',
			'iemobile',
			'minimo',
			'opera mobi',
			'opera mini',
			'netfront',
			'opwv',
			'polaris',
			'semc-browser',
			'up.browser',
			'webpro',
			'wms pie',
			'xiino',
			// services
			'astel',
			'docomo',
			'novarra-vision',
			'portalmmm',
			'reqwirelessweb'
		);

		// then do basic mobile type search, this uses data from: get_mobile_data()
		$j_count = count( $a_mobile_search );
		for ( $j = 0; $j < $j_count; $j ++ ) {
			if ( strstr( $pv_browser_user_agent, $a_mobile_search[ $j ] ) ) {
				$mobile_working_test = $a_mobile_search[ $j ];
				break;
			}
		}

		return $mobile_working_test;
	}


	/*
	thanks to this page: http://www.zytrax.com/tech/web/mobile_ids.html
	for data used here
	*/
	public static function get_mobile_data( $pv_browser_user_agent ) {
		$mobile_browser        = '';
		$mobile_browser_number = '';
		$mobile_device         = '';
		$mobile_os             = ''; // will usually be null, sorry
		$mobile_os_number      = '';
		$mobile_server         = '';
		$mobile_server_number  = '';

		// browsers, show it as a handheld, but is not the os
		$a_mobile_browser = array( 'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile', 'minimo', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino' );
		/*
		This goes from easiest to detect to hardest, so don't use this for output unless you
		clean it up more is my advice.
		*/
		$a_mobile_device = array(
			'benq',
			'blackberry',
			'danger hiptop',
			'ddipocket',
			'iphone',
			'kindle',
			'lge-cx',
			'lge-lx',
			'lge-mx',
			'lge vx',
			'lg;lx',
			'nintendo wii',
			'nokia',
			'palm',
			'pdxgw',
			'playstation',
			'sagem',
			'samsung',
			'sec-sgh',
			'sharp',
			'sonyericsson',
			'sprint',
			'vodaphone',
			'j-phone',
			'n410',
			'mot 24',
			'mot-',
			'htc-',
			'htc_',
			'lge ',
			'lge-',
			'sec-',
			'sie-m',
			'sie-s',
			'spv ',
			'smartphone',
			'armv',
			'midp',
			'mobilephone'
		);
		// note: linux alone can't be searched for, and almost all linux devices are armv types
		$a_mobile_os = array( 'android', 'epoc', 'palmos', 'palmsource', 'windows ce', 'symbianos', 'symbian os', 'symbian', 'linux armv' );

		// sometimes there is just no other id for the unit that the CTS type service/server
		$a_mobile_server = array( 'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb' );

		$k_count = count( $a_mobile_browser );
		for ( $k = 0; $k < $k_count; $k ++ ) {
			if ( strstr( $pv_browser_user_agent, $a_mobile_browser[ $k ] ) ) {
				$mobile_browser = $a_mobile_browser[ $k ];
				// this may or may not work, highly unreliable
				$mobile_browser_number = HMTrackerFN::get_item_version( $pv_browser_user_agent, $mobile_browser );
				break;
			}
		}
		$k_count = count( $a_mobile_device );
		for ( $k = 0; $k < $k_count; $k ++ ) {
			if ( strstr( $pv_browser_user_agent, $a_mobile_device[ $k ] ) ) {
				$mobile_device = $a_mobile_device[ $k ];
				break;
			}
		}
		$k_count = count( $a_mobile_os );
		for ( $k = 0; $k < $k_count; $k ++ ) {
			if ( strstr( $pv_browser_user_agent, $a_mobile_os[ $k ] ) ) {
				$mobile_os = $a_mobile_os[ $k ];
				// this may or may not work, highly unreliable
				$mobile_os_number = HMTrackerFN::get_item_version( $pv_browser_user_agent, $mobile_os );
				break;
			}
		}
		$k_count = count( $a_mobile_server );
		for ( $k = 0; $k < $k_count; $k ++ ) {
			if ( strstr( $pv_browser_user_agent, $a_mobile_server[ $k ] ) ) {
				$mobile_server = $a_mobile_server[ $k ];
				// this may or may not work, highly unreliable
				$mobile_server_number = HMTrackerFN::get_item_version( $pv_browser_user_agent, $a_mobile_server );
				break;
			}
		}
		// just for cases where we know it's a mobile device already
		if ( ! $mobile_os && ( $mobile_browser || $mobile_device || $mobile_server ) && strstr( $pv_browser_user_agent, 'linux' ) ) {
			$mobile_os        = 'linux';
			$mobile_os_number = HMTrackerFN::get_item_version( $pv_browser_user_agent, 'linux' );
		}

		$a_mobile_data = array( $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number );

		return $a_mobile_data;
	}
}

function showMessages( $messages, $type = "success" ) {
	if ( ! is_array( $messages ) ) {
		$messages = (array) $messages;
	}
	if ( ! empty( $messages ) ) {
		switch ( $type ) {
			case "success":
				$colour = "#00FF00";
				break;
			case "error":
				$colour = "#F00";
				break;
		}
		?>
		<div style="margin-bottom: 20px;">
			<br/>
			<strong style="color: <?php echo $colour; ?>">
				<?php foreach ( $messages as $message ) {
					echo "$message<br />";
				}
				?>
			</strong>
		</div>
	<?php
	}
}

function pnp_pagination( $total, $per_page, $num_links, $start_row, $url = '' ) {
	$num_pages = ceil( $total / $per_page );

	if ( $num_pages == 1 ) {
		return '';
	}

	$cur_page = $start_row;

	if ( $cur_page > $total ) {
		$cur_page = ( $num_pages - 1 ) * $per_page;
	}

	$cur_page = floor( ( $cur_page / $per_page ) + 1 );

	$start = ( ( $cur_page - $num_links ) > 0 ) ? $cur_page - $num_links : 0;
	$end   = ( ( $cur_page + $num_links ) < $num_pages ) ? $cur_page + $num_links : $num_pages;

	$output = '';
	if ( $cur_page != 1 ) {
		$i = $start_row - $per_page;
		if ( $i <= 0 ) {
			$i = 0;
		}
		$output .= '<li><a href="' . $url . '&paged=' . $i . '"><i>←</i>Prev</a></li>';
	} else {
		$output .= '<li><span><i>←</i>Prev</span></li>';
	}


	if ( $cur_page > ( $num_links + 1 ) ) {
		$output .= ' <li><a href="' . $url . '" title="First Page">first</a></li>';
	}

	for ( $loop = $start; $loop <= $end; $loop ++ ) {
		$i = ( $loop * $per_page ) - $per_page;

		if ( $i >= 0 ) {
			if ( $cur_page == $loop ) {
				$output .= '<li class="active"><span>' . $loop . '</span></li>';
			} else {

				$n = ( $i == 0 ) ? '' : $i;

				$output .= '<li><a href="' . $url . '&paged=' . $n . '">' . $loop . '</a></li>';
			}
		}
	}

	if ( ( $cur_page + $num_links ) < $num_pages ) {
		$i = ( ( $num_pages * $per_page ) - $per_page );
		$output .= '<li><a href="' . $url . '&paged=' . $i . '" title="Last Page">Last</li>';

	}

	if ( $cur_page < $num_pages ) {
		$output .= '<li><a href="' . $url . '&paged=' . ( $cur_page * $per_page ) . '">Next<i>→</i></a></li>';
	} else {
		$output .= '<li><span>Next<i>→</i></span></li>';
	}

	return '<div class="pagination pagination-small" style="text-align:right" ><ul style="padding:0; display: inline-block">' . $output . '</ul></div>';
}


function pack_paypal_custom( &$user, &$plan, $new_plan = null ) {
	$data = array( 'user_key' => $user->user_key, 'plan_id' => $plan['id'] );
	if ( isset( $new_plan ) ) {
		$data['new_plan_id'] = $new_plan['id'];
	}

	return base64_encode( serialize( $data ) );
}

function unpack_paypal_custom( $custom ) {
	$data  = @base64_decode( $custom );
	$parts = @unserialize( $data );

	return $parts;
}

function create_user_key( $email ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option( $HMTrackerPro_OPTION_NAME );
	$key    = sha1( md5( $email ) . $option['key'] );

	return $key;
}

function user_exists( $user_key ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option      = get_option( $HMTrackerPro_OPTION_NAME );
	$table_users = T_PREFIX . $option['dbtable_name_users'];
	$users_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_users WHERE `user_key` = '" . $user_key . "'" );

	return $users_count > 0;
}

function create_user( $user_obj_or_arr ) {
	global $HMTrackerPro_OPTION_NAME,
	       $HMTrackerPro_USERSTATUS_NAME,
	       $HMTrackerPro_PROJECTS_NAME,
	       $HMTrackerPro_USER_DOMAINS_NAME,
	       $wpdb;
	$option = get_option( $HMTrackerPro_OPTION_NAME );

	if ( is_object( $user_obj_or_arr ) ) {
		$user = array();
		foreach ( $user_obj_or_arr as $k => $v ) {
			$user[ $k ] = $v;
		}
	} else if ( is_array( $user_obj_or_arr ) ) {
		$user = &$user_obj_or_arr;
	} else {
		return 0;
	}

	$table_users = T_PREFIX . $option['dbtable_name_users'];
	$q           = "INSERT INTO `" . $table_users . "` (
						`email`,
						`password`,
						`business_name`,
						`website`,
						`user_key`,
						`plans`,
						`status`,
						`last_status_check`) 
		 VALUES ('" . $user["email"] . "',
		 '" . md5( sha1( $user["password"] ) ) . "',
		 '" . $user["business_name"] . "',
		 '" . $user["website"] . "',
		 '" . $user['user_key'] . "',
		 '" . ( is_array( $user['plans'] ) ? serialize( $user['plans'] ) : $user['plans'] ) . "',
		 '" . $user['status'] . "',
		 " . time() . ")";

	//print_r(compact('user', 'q'));
	$res     = $wpdb->query( $q );
	$user_id = $res ? $wpdb->lastInsertedId() : 0;


	add_option( $HMTrackerPro_PROJECTS_NAME . $user['user_key'], array() );
	$statuses = array( 1 => array( 0 ), 2 => array( 0 ) );
	add_option( $HMTrackerPro_USERSTATUS_NAME . $user_id, $statuses );

	ensure_plans_unserialized( $user );
	$plan                                = get_active_plan( $user );
	$domains                             = array();
	$domains['opt_max_tracking_domains'] = $plan['pack_domains'] + $plan['extradomains_count'];
	$domains['opt_tracking_domains']     = array();
	$domains['opt_tracking_autofill']    = true;
	add_option( $HMTrackerPro_USER_DOMAINS_NAME . $user['user_key'], $domains );

	return $user_id;
}

function hmt_isset( &$value, $default = null ) {
	if ( isset( $value ) ) {
		$temp = $value;
	} else {
		$temp = $default;
	}

	return $temp;
}

function add_payment( $payment, $now = null ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option( $HMTrackerPro_OPTION_NAME );

	$table_payments = T_PREFIX . $option['dbtable_name_payments'];
	$q              = "INSERT INTO `" . $table_payments . "` (
					`txnid`,
					`payment_amount`,
					`payment_status`,
					`createdtime`,
					`txn_type`,
					`user`,
					`user_id`,
					`plan_id`) 
				VALUES ('" . $payment['txnid'] . "',
				" . $payment['payment_amount'] . ",
				'" . $payment['payment_status'] . "',
				'" . ( empty( $now ) ? date( "Y-m-d H:i:s" ) : date( "Y-m-d H:i:s", $now ) ) . "',
				'" . $payment['txn_type'] . "',
				'" . $payment['user'] . "',
				" . $payment['user_id'] . ",
				'" . $payment['plan_id'] . "');";
	$res            = $wpdb->query( $q );

	return $wpdb->lastInsertedId();
}

function refund_payment( $payment ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option( $HMTrackerPro_OPTION_NAME );

	//find all payments for current plan...
	$table_payments = T_PREFIX . $option['dbtable_name_payments'];
	$q              = "SELECT SUM(payment_amount)
			FROM `$table_payments`
			WHERE `plan_id` = '${payment['plan_id']}' AND
					payment_status = 'Completed' AND
					txn_type = 'payment'
			;";
	//echo $q;
	$amount = $wpdb->get_var( $q );
	//print_r(compact('amount', 'q'));
	if ( $amount ) { //user has positive credits
		$payment['payment_amount'] = $amount;

		return add_payment( $payment );
	}

	return 0;
}


function get_payment_by( $field, $value ) {
	if ( ! in_array( $field, array( 'txnid', 'id' ) ) ) {
		return false;
	}

	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option = get_option( $HMTrackerPro_OPTION_NAME );

	$value          = mysql_escape_string( $value );
	$table_payments = T_PREFIX . $option['dbtable_name_payments'];
	$q              = "SELECT * FROM `" . $table_payments . "` WHERE $field = '$value';";
	$res            = $wpdb->get_row( $q );

	return $res;
}

/**
 * Gets user current plan according to the date specified.
 * For new user returns first and the only plan
 */
function get_active_plan( &$user, $now = null ) {
	//check entity type
	if ( is_array( $user ) ) {
		$plans  =  &$user['plans'];
		$status = $user['status'];
	} else {
		$plans  = &$user->plans;
		$status = $user->status;
	}

	if ( ! is_array( $plans ) ) {
		$plans = unserialize( $plans );
	}


	if ( $status == 0 ) //new user - only one plan
	{
		return $plans[0];
	}

	$now     = empty( $now ) ? time() : $now;
	$lastInd = count( $plans ) - 1;
	for ( $i = $lastInd; $i >= 0; $i -- ) { //it should be last as well
		$plan = &$plans[ $i ];
		if ( $plan['start_date'] <= $now && empty( $plan['end_date'] ) ) {
			return $plans[ $i ];
		}
	}

	return false;
}

function get_plan_by_id( &$user, $plan_id ) {
	ensure_plans_unserialized( $user );

	//check entity type
	if ( is_array( $user ) ) {
		$plans =  &$user['plans'];
	} else {
		$plans = &$user->plans;
	}

	foreach ( $plans as $plan ) {
		if ( $plan['id'] == $plan_id ) {
			return $plan;
		}
	}

	return false;
}

function ensure_plans_unserialized( &$user ) {
	//check entity type
	if ( is_array( $user ) ) {
		$plans =  &$user['plans'];
	} else {
		$plans = &$user->plans;
	}

	//unserialize if needed
	if ( ! is_array( $plans ) ) {
		$plans = unserialize( $plans );
	}
}

/**
 * Creates default plan stub based on specified package
 */
function get_plan_for( $pack, $pack_id, $prev_plan = null ) {
	$plan                         = array();
	$plan['id']                   = create_guid( 'plan' );
	$plan['pack_id']              = $pack_id; //package id
	$plan['title']                = $pack['title'];
	$plan['pack_domains']         = $pack['domains'];
	$plan['free_trial']           = $pack['free_trial'];
	$plan['currency_code']        = $pack['currency_code'];
	$plan['cost_weekly']          = $pack['weekly'];
	$plan['cost_biweekly']        = $pack['biweekly'];
	$plan['cost_monthly']         = $pack['monthly'];
	$plan['cost_annually']        = $pack['annually'];
	$plan['extradomain_weekly']   = $pack['extradomain_weekly'];
	$plan['extradomain_biweekly'] = $pack['extradomain_biweekly'];
	$plan['extradomain_monthly']  = $pack['extradomain_monthly'];
	$plan['extradomain_annually'] = $pack['extradomain_annually'];
	$plan['suspensions']          = array();
	//will be set up on plan customization
	$plan['end_date'] = 0; //timestamp, not  "Y-m-d H:i:s"
	if ( $prev_plan ) {
		$prev_plan['end_date']      = empty( $prev_plan['end_date'] ) ? time() : $prev_plan['end_date'];
		$plan['start_date']         = $prev_plan['end_date'];
		$plan['extradomains_count'] = $prev_plan['extradomains_count'];
		$plan['pay_interval']       = $prev_plan['pay_interval'];
		$plan['fixed']              = $prev_plan['fixed'];
	} else {
		$plan['extradomains_count'] = 0;
		$plan['pay_interval']       = 'weekly';
		$plan['fixed']              = 1;
		$plan['start_date']         = time(); //timestamp, not  "Y-m-d H:i:s"
	}

	return $plan;
}

function create_guid( $namespace = '' ) {
	static $guid = '';
	$uid  = uniqid( "", true );
	$data = $namespace;
	$data .= $_SERVER['REQUEST_TIME'];
	$data .= $_SERVER['HTTP_USER_AGENT'];
	$data .= isset( $_SERVER['LOCAL_ADDR'] ) ? $_SERVER['LOCAL_ADDR'] : "";
	$data .= isset( $_SERVER['LOCAL_PORT'] ) ? $_SERVER['LOCAL_PORT'] : "";
	$data .= $_SERVER['REMOTE_ADDR'];
	$data .= isset( $_SERVER['REMOTE_PORT'] ) ? $_SERVER['REMOTE_PORT'] : "";
	$hash_orig = hash( 'ripemd128', $uid . $guid . md5( $data ) );
	$hash      = strtoupper( $hash_orig );
	$guid      = '{' .
	             substr( $hash, 0, 8 ) .
	             '-' .
	             substr( $hash, 8, 4 ) .
	             '-' .
	             substr( $hash, 12, 4 ) .
	             '-' .
	             substr( $hash, 16, 4 ) .
	             '-' .
	             substr( $hash, 20, 12 ) .
	             '}';

	//return $guid;
	return $hash_orig;
}

function init_balance_calculator( $user ) {
	require_once( 'balance.php' );
	ensure_plans_unserialized( $user );

	$calc = new BalanceCalculator();
	$calc->SetTrans( user_payments( $user->id ) );
	$calc->SetPlans( $user->plans );
	$calc->SetPacks( get_packages() );

	return $calc;
}

/**
 * Detects status of the user
 * possible statuses:
 * 0 = created - waiting first payment
 * 1 = active  - balance > 0
 * 2 = pending - waiting recurrent payment within 1 day,
 * 3 = suspended - balance < 0 more then 1 day; account is not charged.
 * 4 = refunded  - about to delete account, will be done manually by admin.
 * 5 || -1 = deleted
 * 6 = free user without payment and plan
 */
function detect_user_status( $user = null, $force = false ) {
	$user = empty( $user ) ? current_user() : $user;
	if ( ! $user ) {
		return - 1;
	}

	//print_r($user)

	//$force = true;
	$now = time();
	if ( $force || ( $user->last_status_check < $now - 600 && $user->status != 6 && $user->status != 7 ) ) {
		require_once( "balance.php" );

		//calculate balance and other stuff
		$calc                    = init_balance_calculator( $user );
		$user->status            = $calc->DetectStatus( $user->status );
		$user->last_status_check = $now;

		$balance = $calc->Balance();
		$plan    = $calc->GetActivePlan();

		$daily_cost        = $calc->DailyCost( $plan );
		$plan_cost         = $calc->CalcPlanPayment( $plan );
		$pay_in_seconds    = $calc->PayIntervalInSeconds( $plan );
		$active_expires_in = 0;
		if ( $pay_in_seconds && $plan_cost > 0 ) {
			$active_expires_in = (int) ( ( ( $balance / $plan_cost ) * $pay_in_seconds ) / 3600 );
		}
		$pending_expires_in = 0;
		if ( $daily_cost > 0 ) {
			$pending_expires_in = (int) ( 24 * ( $daily_cost + $balance ) / $daily_cost );
		}

		//save status parameters
		global $HMTrackerPro_USERSTATUS_NAME;
		$statuses       = array( 1 => array( $active_expires_in ), 2 => array( $pending_expires_in ) );
		$statuses_saved = get_option( $HMTrackerPro_USERSTATUS_NAME . $user->id );
		if ( $statuses_saved ) {
			update_option( $HMTrackerPro_USERSTATUS_NAME . $user->id, $statuses );
		} else {
			add_option( $HMTrackerPro_USERSTATUS_NAME . $user->id, $statuses );
		}

//		print_r(compact('plan', 'user', 'balance', 'daily_cost', 'plan_cost', 'pay_in_seconds', 'statuses', 'statuses_saved'));
//		print_r(compact('balance', 'daily_cost', 'plan_cost', 'pay_in_seconds', 'active_expires_in', 'pending_expires_in'));
//		var_dump(validate_user_status( $user->status, array( 3, 4, 99 ) ));
//		var_dump(validate_user_status( $user->status, array( 0, 1, 2, ) ));
//		die();

		//update user status
		if ( validate_user_status( $user->status, array( 3, 4, 99 ) ) ) { //suspended or refunded
			$need_update = ensure_plan_suspended( $plan );
			if ( $need_update ) {
				replace_plan( $user->plans, $plan );
				update_user( $user );
			} else {
				update_user_status( $user->id, $user->status );
			}
		} else if ( validate_user_status( $user->status, array( 0, 1, 2, 7 ) ) ) {
			ensure_plan_resumed( $plan );
			replace_plan( $user->plans, $plan );
			update_user( $user );
		}
	}


	return $user->status;
}

function is_plan_temporary( $plan ) {
	return ! empty( $lastPlan['start_date'] ) &&
	       ( empty( $lastPlan['end_date'] ) ||
	         $lastPlan['start_date'] < $lastPlan['end_date'] ) ? false : true;
}

function is_plan_closed( $user, $plan_id ) {
	$calc = init_balance_calculator( $user );
	$plan = get_plan_by_id( $user, $plan_id );
	if ( ! empty( $plan ) ) {
		$eots = $calc->SearchTrans( 'cancel', $plan );

		//print_r(compact('user','eots', 'plan'));
		return count( $eots ) > 0;
	}

	return false;
}

function replace_plan( &$plans, $plan ) {
	foreach ( $plans as $i => $pl ) {
		if ( $plan['id'] == $pl['id'] ) {
			$plans[ $i ] = $plan;
			break;
		}
	}
}

function get_projects( $user_key ) {
	global $HMTrackerPro_PROJECTS_NAME;
	$packages = get_option( $HMTrackerPro_PROJECTS_NAME . $user_key );

	return $packages;
}


function get_packages() {
	global $HMTrackerPro_PACKAGES_NAME;
	$packages = get_option( $HMTrackerPro_PACKAGES_NAME );

	return $packages;
}

/**
 * Detects status of the user
 * possible statuses:
 * 0 = new - waiting first payment
 * 1 = active  - balance > 0
 * 2 = pending - waiting recurrent payment within 1 day, balance < 0,
 * 3 = suspended - balance < 0 more then 1 day; account is not charged.
 * 4 = refunded  - about to delete account, will be done manually by admin.
 * 5 = deleted
 * 6 = free
 * 7 = active - free package
 * 8 = overridden - admin overrides package
 * 9 = suspended - suspended by admin
 */
function user_status_name( $status_code ) {
	static $codes = array( 'new', 'active', 'pending', 'suspended', 'refunded', 'deleted', 'free', 'active', 'active (override)', 'suspended (override)' );

	return isset( $codes[ $status_code ] ) ? $codes[ $status_code ] : 'deleted';
}

function get_user_by( $field, $value ) {
	if ( ! in_array( $field, array( 'user_key', 'id' ) ) ) {
		return false;
	}

	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option      = get_option( $HMTrackerPro_OPTION_NAME );
	$table_users = T_PREFIX . $option["dbtable_name_users"];
	$q           = "SELECT *
			FROM " . $table_users . "
			WHERE `$field` = \"" . $value . "\"";
	$user        = $wpdb->get_row( $q );

	return ! empty( $user ) ? $user : false;
}

function update_user( $user ) {
	global $HMTrackerPro_OPTION_NAME, $HMTrackerPro_USER_DOMAINS_NAME, $wpdb;
	$option  = get_option( $HMTrackerPro_OPTION_NAME );
	$domains = get_option( $HMTrackerPro_USER_DOMAINS_NAME . $user->user_key );
	if ( ! is_array( $domains ) ) {
		add_option( $HMTrackerPro_USER_DOMAINS_NAME . $user->user_key, array() );
	}
	$table_users = T_PREFIX . $option["dbtable_name_users"];
	$q           = "UPDATE " . $table_users . "
			SET `status` = {$user->status},
				`last_status_check` = {$user->last_status_check},
				`email` = '{$user->email}',
				`business_name` = '{$user->business_name}',
				`website` = '{$user->website}',
				`plans` = '" . ( ! is_array( $user->plans ) ? $user->plans : serialize( $user->plans ) ) . "'
			WHERE `id` = {$user->id}
			;";
	$wpdb->query( $q );

	//update domain limit
	ensure_plans_unserialized( $user );
	$plan = get_active_plan( $user );

	$domains['opt_max_tracking_domains'] = $plan['pack_domains'] + $plan['extradomains_count'];

	if ( ! isset( $domains['opt_tracking_domains'] ) || ! is_array( $domains['opt_tracking_domains'] ) ) {
		$domains['opt_tracking_domains'] = array();
	}
	if ( ! isset( $domains['opt_tracking_autofill'] ) ) {
		$domains['opt_tracking_autofill'] = true;
	}


	$i = count( $domains['opt_max_tracking_domains'] );

	while ( $i -- > $domains['opt_max_tracking_domains'] ) {
		array_pop( $domains['opt_tracking_domains'] );
	}

	update_option( $HMTrackerPro_USER_DOMAINS_NAME . $user->user_key, $domains );
}

/**
 * Light version to set status flag only
 */
function update_user_status( $user_id, $status ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option      = get_option( $HMTrackerPro_OPTION_NAME );
	$table_users = T_PREFIX . $option["dbtable_name_users"];
	$q           = "UPDATE " . $table_users . "
			SET `status` = {$status}
			WHERE `id` = {$user_id}
			;";
	$wpdb->query( $q );

}

/**
 * Shows if plan suspensions array was updated
 */
function ensure_plan_resumed( &$plan, $now = null ) {
	if ( empty( $plan ) ) {
		return false;
	}

	$now   = empty( $now ) ? time() : $now;
	$susps = &$plan['suspensions'];
	if ( ! is_array( $susps ) ) {
		$susps = array();
	}

	$found   = false;
	$lastInd = count( $susps ) - 1;
	for ( $i = $lastInd; $i >= 0; $i -- ) {
		$susp = $susps[ $i ];
		if ( empty( $susp['end'] ) ) {
			$found       = true;
			$susp['end'] = $now;
		}
	}

	return $found;
}


/**
 * Shows if plan suspensions array was updated
 */
function ensure_plan_suspended( &$plan, $now = null ) {
	if ( empty( $plan ) ) {
		return false;
	}

	$now   = empty( $now ) ? time() : $now;
	$found = false;
	$susps = &$plan['suspensions'];
	if ( ! is_array( $susps ) ) {
		$susps = array();
	}

	$lastInd = count( $susps ) - 1;
	for ( $i = $lastInd; $i >= 0; $i -- ) {
		$susp = $susps[ $i ];
		if ( empty( $susp['end'] ) ) {
			$found = true;
			break;
		}
	}

	if ( ! $found ) {
		$susps[] = array( 'start' => $now, 'end' => 0 );
	}

	return ! $found;
}

function current_user() {
	global $loggedin_user;
//	return get_user_by( 'user_key', $_SESSION["login_user"][2] );
	return get_user_by( 'user_key', $loggedin_user[2] );
}

function user_payments( $user_id, $offset = 0, $limit = 10000 ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option         = get_option( $HMTrackerPro_OPTION_NAME );
	$table_payments = T_PREFIX . $option["dbtable_name_payments"];
	$q              = "SELECT *
			FROM " . $table_payments . "
			WHERE `user_id` = $user_id 
			ORDER BY `createdtime`";
	$payments       = $wpdb->get_results( $q, - 1, false );

	return ! empty( $payments ) ? $payments : array();
}

function plan_has_payments( $plan_id ) {
	global $HMTrackerPro_OPTION_NAME, $wpdb;
	$option         = get_option( $HMTrackerPro_OPTION_NAME );
	$table_payments = T_PREFIX . $option["dbtable_name_payments"];
	$q              = "SELECT id
			FROM " . $table_payments . "
			WHERE `plan_id` = '$plan_id' AND `txn_type` = 'payment'
			";
	$pid            = $wpdb->queryUniqueValue( $q );

	return ! empty( $pid );
}

function b2a2( $bin ) {
	$result = '';
	$len    = strlen( $bin );
	for ( $i = 0; $i < $len; $i += 8 ) {
		$result .= chr( bindec( substr( $bin, $i, 8 ) ) );
	}

	return $result;
}

$this->VIBER_INIT = b2a2( "0010010001101000011011010111010001110010011000010110001101101011011001010111001001011111011110000010000000111101001000000110001101110010011001010110000101110100011001010101111101100110011101010110111001100011011101000110100101101111011011100010100000100000001000100010001000101100001000000010011101100110011101010110111001100011011101000110100101101111011011100010000001110011011101010111000001100101011100100101111101101000011011010111010001110010011000010110001101101011011001010111001000101000001010010111101101100111011011000110111101100010011000010110110000100000001001000100100001001101010101000111001001100001011000110110101101100101011100100101000001110010011011110101111101010000010011000101010101000111010010010100111001011111010100000100000101010100010010000011101100100000011010010111001101011111011001100110100101101100011001010010100000100100010010000100110101010100011100100110000101100011011010110110010101110010010100000111001001101111010111110101000001001100010101010100011101001001010011100101111101010000010000010101010001001000001011100010001000101111011011000110100101100010001011110110100001101101011101000111001001100001011000110110101101100101011100100111001101110000011110010010110101110011011101000110000101101110011001000110000101101100011011110110111001100101001011010110010001100101011101100010111001100010011010010110111000100010001010010010000001101111011100100010000001100100011010010110010100101000001000100100001101100001011011100110000001110100001000000110011001101001011011100110010000100000011010000110110101110100011100100110000101100011011010110110010101110010011100110111000001111001001011010111001101110100011000010110111001100100011000010110110001101111011011100110010100101101011001000110010101110110001011100110001001101001011011100010001000101001001110110110011001110101011011100110001101110100011010010110111101101110001000000110001000110010011000010010100000100100011100010111101000110000001010010111101100100100011110100110011000110001001111010110100101101101011100000110110001101111011001000110010100101000011101010110111001110000011000010110001101101011001010000010001001100011001010100010001000101100001001000111000101111010001100000010100100101100001000100010001000101001001110110010010001111001011011010011001000111101001000100010001000111011001001000110000101111001001100110011110101110011011101000111001001101100011001010110111000101000001001000111101001100110001100010010100100111011011001100110111101110010001010000010010001110011011010100011010000111101001100000011101100100100011100110110101000110100001111000010010001100001011110010011001100111011001001000111001101101010001101000010101100111101001110000010100101111011001001000111100101101101001100100010111000111101011000110110100001110010001010000110001001101001011011100110010001100101011000110010100001110011011101010110001001110011011101000111001000101000001001000111101001100110001100010010110000100100011100110110101000110100001011000011100000101001001010010010100100111011011111010111001001100101011101000111010101110010011011100010000000100100011110010110110100110010001110110111110100100100011000100111101000110101001111010110011101111010011010010110111001100110011011000110000101110100011001010010100001100110011010010110110001100101010111110110011101100101011101000101111101100011011011110110111001110100011001010110111001110100011100110010100000100100010010000100110101010100011100100110000101100011011010110110010101110010010100000111001001101111010111110101000001001100010101010100011101001001010011100101111101010000010000010101010001001000001011100010001000101111011011000110100101100010001011110110100001101101011101000111001001100001011000110110101101100101011100100111001101110000011110010010110101110011011101000110000101101110011001000110000101101100011011110110111001100101001011010110010001100101011101100010111001100010011010010110111000100010001010010010100100111011001001000110001101101111001101100011110101110000011100100110010101100111010111110111001001100101011100000110110001100001011000110110010100101000001000100010111100101000001011100010101000101001001011110110010100100010001011000010000001100010001100100110000100101000001001000110001001111010001101010010100100101100001000000010001000100010001010010011101101110010011001010111010001110101011100100110111000100000001001000110100001101101011101000111001001100001011000110110101101100101011100100101111101111000001100100010100000101001001110110111110101110010011001010111010001110101011100100110111000100000011100110111010101110000011001010111001001011111011010000110110101110100011100100110000101100011011010110110010101110010001010000010100100111011001001110010100100111011" );
$this->MAIN_STR   = null;


function calculate_initial_credit( $user, $plan, $hours_credit ) {
	$calc             = init_balance_calculator( $user );
	$pay_interval_sec = $calc->PayIntervalInSeconds( $plan );
	$plan_cost        = $calc->CalcPlanPayment( $plan );

	return $plan_cost * $hours_credit * 3600 / $pay_interval_sec;
}

function create_payment( $user, $initial_balance, $plan ) {
	$payment = array
	(
		'txnid'          => '',
		'payment_amount' => number_format( $initial_balance, 2 ),
		'payment_status' => 'Completed',
		'txn_type'       => 'manual',
		'user'           => $user->email,
		'user_id'        => $user->id,
		'plan_id'        => $plan['id']
	);

	add_payment( $payment, $plan['start_date'] );

}

function validate_user_status( $cur_status_id, $statuses = array( 1, 2, 6, 7, 8 ) ) {
	return in_array( $cur_status_id, $statuses );
}

function is_plan_free( $package ) {
	return $package['cost_weekly'] == 0 && $package['cost_biweekly'] == 0 && $package['cost_monthly'] == 0 && $package['cost_annually'] == 0;
}

function has_available_packages( $packages, $projects ) {
	$project_count = count( $projects );

	foreach ( $packages as $package_id => $package_data ) {
		if ( $project_count <= $package_data['domains'] ) {
			return true;
		}
	}

	return false;

}