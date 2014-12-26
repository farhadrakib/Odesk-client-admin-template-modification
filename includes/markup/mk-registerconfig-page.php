<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
global $HMTrackerPro_OPTION_NAME;

if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}

function showErrors( $errors ) {
	if ( ! empty( $errors ) ) {
		showMessages( $errors, "error" );
	}
	if ( ! empty( $_SESSION['error_msg']['registration'] ) ) {
		showMessages( $_SESSION['error_msg']['registration'], "error" );
	}
}

function writeConfigFile( $file, $config ) {
	$errors = array();
	if ( ! ( $f = fopen( $file, 'wb' ) ) ) {
		$errors[] = "Can't create config file. <br />Please make sure you have write permission in the heatmap tracker folder";
	} else {
		$res = @fwrite( $f, $config );
		if ( $res == - 1 ) {
			$errors[] = "Can't write to file";
		}
		fclose( $f );
	}
	return $errors;
}

function writeMailConfig( $file ) {
	// Write the new config file for mail settings
	$config = "<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://HeatMapTracker.com
 */
/*
 * Email Notifications Config
 */
# Specify email, that will be used as sender for the admin emails for you and your clients
# ! NOTE add this email in white list in order to prevent appearing emails in spam
define('PHP_MAILER_SENDER_EMAIL', 'no-reply@" . $_SERVER["SERVER_NAME"] . "');
define('PHP_MAILER_SENDER_NAME', 'HeatMapTracker Notifications');

# Specify Mailer Transport type:
# 1 - SMTP
# 2 - Sendmail
# 3 - Mail
define('PHP_MAILER_TRANSPORT', 3);

# Specify Mailer SMTP Settings
define('PHP_MAILER_SERVER', '');
define('PHP_MAILER_PORT', 25);
define('PHP_MAILER_SSL', false);
define('PHP_MAILER_USERNAME', '');
define('PHP_MAILER_PASSWORD', '');
";

	return writeConfigFile( $file, $config );
}

function writeDatabaseConfig( $file, $config ) {
	$config = "<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://HeatMapTracker.com
 */
/*
 * Database config
 */
# MySQL database name
define('DB_NAME', '" . $config["db_name"] . "');

# MySQL database username
define('DB_USER', '" . $config["db_user"] . "');

# MySQL database user password
define('DB_PASSWORD', '" . $config["db_password"] . "');

# MySQL database host
define('DB_HOST', '" . $config["db_host"] . "');

#unique MYSQL table prefix.
# ! NOTE Only numbers, letters, and underscores please!
define('T_PREFIX', '" . $config["db_prefix"] . "');";

	return writeConfigFile( $file, $config );
}

$errors = array();
if ( isset( $_POST['fldTask'] ) ) {
	$_POST['db_port'] = 3306;
	if ( $_POST['fldTask'] == 'register' ) {
		$errors = $this->hmtRegisterPlugin( $_POST, $this->PLUGIN_PATH );
		if ( empty( $errors ) ) {
			$_POST['fldTask'] = "continue";
		}
	}

	if ( $_POST['fldTask'] == "reregister" ) {
		$errors = $this->hmtRegisterPlugin( $_POST, $this->PLUGIN_PATH, true );
	}

	// Load RDS Class if we're installing RDS
	if ( ! in_array( $_POST['fldTask'], array( 'register', 'reregister' ) ) ) {
		require_once( 'includes/aws_sdk/rds_class.php' );

		/** @var awsRds $rds */
		$rds = new awsRds( $_POST['iam_key'], $_POST['iam_secret'], $_POST['iam_region'] );
	}

	if ( $_POST['fldTask'] == "install" ) {

		if ( $_POST['options']['DBInstanceIdentifier'] == "New Instance" ) {
			// Create new DB instance
			if ( isset( $_POST['DBInstanceIdentifier'] ) ) {
				$_POST['options']['DBInstanceIdentifier'] = $_POST['DBInstanceIdentifier'];
			}
			if ( isset( $_POST['options']['StorageType'] ) && $_POST['options']['StorageType'] == 'io1' && ! isset( $_POST['options']['Iops'] ) ) {
				$_POST['options']['Iops'] = 1000;
			}
			$_POST['options']['MultiAZ'] = (bool) $_POST['options']['MultiAZ'];

			// TODO - Be more specific with security on inbound security group. NB! Need to deal with this on new registration if moving server!
//			$myip = gethostbyname( gethostname() ) . "/32";
//			if ( $myip == "127.0.0.1/32" || $myip == "127.0.1.1/32" ) {
			$myip = "0.0.0.0/0";
//			}

			$security_group_name = "HeatmapTracker";

			$security_group_id = $rds->createSecurityGroup( array(
				"GroupName"   => $security_group_name,
				"Description" => "Allow {$myip} access to {$_POST['DBInstanceIdentifier']}"
			) );

			$found = true;
			if ( isset( $security_group_id['error'] ) && isset( $security_group_id['error']['Caught exception'] ) ) {
				$error    = $security_group_id['error']['Caught exception'];
				$response = $rds->describeSecurityGroups();
				if ( isset( $response['error'] ) && isset( $response['error']['Caught exception'] ) ) {
					$found = false;
					$error = $response['error']['Caught exception'];
				} else {
					$found = false;
					foreach ( $response->get( "SecurityGroups" ) as $r ) {
						if ( $r["GroupName"] == $security_group_name ) {
							$found             = true;
							$security_group_id = $r["GroupId"];
							break;
						}
					}
				}
			}

			if ( ! $found ) {
				$errors[] = $error;
			} else {
				$security_group_name = $response;
				$response            = $rds->authorizeSecurityGroupIngress( array(
					"GroupId"       => $security_group_id,
					"CIDRIP"        => "{$myip}",
					"IpPermissions" => array(
						array(
							"IpProtocol"       => "-1",
							"FromPort"         => 0,
							"ToPort"           => 65535,
							"UserIdGroupPairs" => array(),
							"IpRanges"         => array(
								array(
									"CidrIp" => "{$myip}"
								)
							)
						)
					)
				) );

				if ( isset( $response['error'] ) && isset( $response['error']['Caught exception'] ) && strpos( "already exists", $response['error']['Caught exception'] ) ) {
					$errors[] = $response['error']['Caught exception'];
				} else {
					$_POST['options']["VpcSecurityGroupIds"] = array( $security_group_id );
					$instance                                = $rds->createInstance( $_POST['options'] );
					if ( isset( $instance['error'] ) ) {
						$errors[] = $instance['error']['Caught exception'];
						$_POST['options']['DBInstanceIdentifier'] = "";
					}
				}
			}
		} else {
			$instance = $rds->getInstance( $_POST['options']['DBInstanceIdentifier'] );

			if ( ! isset( $instance['error'] ) ) {
				$config['db_host']     = "{$instance['Endpoint']['Address']}:{$instance['Endpoint']['Port']}";
				$config['db_port']     = $instance['Endpoint']['Port'];
				$config['db_name']     = $instance['DBName'];
				$config['db_user']     = $instance['MasterUsername'];
				$config['db_password'] = $_POST['options']['MasterUserPassword'];
				//TODO - Ask for this during setup
				$config['db_prefix'] = "hmt_";
				$config['license']   = $_POST['license'];
				$errors              = $this->checkDB( $config );
				if ( empty( $errors ) ) {
					updateUserDetails( $_POST['license'] );
					header( "Location: " . home_url() );
					die();
				}
			} else {
				$errors[] = $instance['error']['Caught exception'];
			}
		}

		if ( empty( $errors ) ) {
			include( $this->PLUGIN_PATH . "includes/markup/mk-rdsinstall-poll.php" );
			die();
		}
	}

	if ( $_POST['fldTask'] == "complete" ) {
		$instance = $rds->getInstance( $_POST['DBInstanceIdentifier'] );
		if ( ! isset( $instance['error'] ) ) {
			$config['db_host']     = "{$instance['Endpoint']['Address']}:{$instance['Endpoint']['Port']}";
			$config['db_port']     = $instance['Endpoint']['Port'];
			$config['db_user']     = $_POST['db_user'];
			$config['db_password'] = $_POST['db_password'];
			$config['db_name']     = $_POST['db_name'];
			$config['db_prefix']   = $_POST['db_prefix'];
			$config['license']     = $_POST['license'];
			$errors                = $this->checkDB( $config );
			if ( empty( $errors ) ) {
				updateUserDetails( $_POST['license'] );
				header( "Location: " . home_url() );
				die();
			}
		} else {
			$errors[] = $instance['error']['Caught exception'];
		}
	}
}
$reregister = false;
if ( isset( $backendregister ) && $backendregister ) {
	$reregister = true;
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<!--[if lt IE 9]>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/excanvas.js"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/respond.js"></script>
	<![endif]-->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/login.js"></script>
	<!-- END PAGE LEVEL SCRIPTS -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<div id="wrapper">
	<div id="header">
		<strong class="logo add"><a href="#"><img src="images/hmtracker-logo.png" alt="logo"/></a></strong></div>
</div>
<div id="main">
	<div class="login-box" style="max-width: <?php echo $reregister ? "385px" : "550px" ?>;">
		<div class="holder">
			<?php
			if ( version_compare( PHP_VERSION, "5.3", "<" ) ) {
				showErrors( "You are using PHP v " . PHP_VERSION . " but for the stable work of Agency license PHP v5.3+ is required. Please upgrade your PHP" );
			} elseif ( isset( $_POST['fldTask'] ) && ( $_POST['fldTask'] == 'continue' || $_POST['fldTask'] == 'install' ) ) {
				require_once( "includes/markup/views/mk-registerconfig-rds.php" );
			} else {
				require_once( "includes/markup/views/mk-registerconfig-view.php" );
			}
			?>
			<!-- END LOGIN FORM -->
			<!-- BEGIN FORGOT PASSWORD FORM -->
			<form id="forgotform" class="form-vertical no-padding no-margin hide" action="index.html">
				<p class="center">Enter your e-mail address below to reset your password.</p>

				<div class="control-group">
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><i class="icon-envelope"></i></span><input id="input-email" type="text" placeholder="Email"/>
						</div>
					</div>
					<div class="space10"></div>
				</div>
				<input type="button" id="forget-btn" class="btn btn-block btn-inverse" value="Submit"/>
			</form>
			<!-- END FORGOT PASSWORD FORM -->
		</div>
	</div>
</div>
</body>
<!-- END BODY -->
</html>