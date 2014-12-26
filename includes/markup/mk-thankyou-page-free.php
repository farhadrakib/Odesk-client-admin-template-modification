<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}
session_start();
?>
<?php
if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<div id="wrapper">
	<div id="header">
		<strong class="logo add"><a href="<?php echo $this->PLUGIN_URL ?>"><img
					src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
	</div>
	<div id="main">
		<div class="login-box">
			<div class="holder">

				<?php
				$user = get_user_by( 'user_key', $user_key );
				if ( $user ) {
					?>
					<h3>Thank You For Subscribing!</h3>

					<p></p>

					<a class="btn btn-primary" href="<?php echo admin_url() ?>" class="btn">Sign In</a>

				<?php } ?>
				<!-- END FORGOT PASSWORD FORM -->
			</div>
		</div>
	</div>
</div>

</body>
<!-- END BODY -->
</html>