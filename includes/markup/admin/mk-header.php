<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 22-09-2014
 * Time: 11:24
 */
if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
} ?>
<?php
global $loggedin_user;
if ( ! is_user_logged_in( $loggedin_user ) && is_admin() && IS_KEY_VALID ) {
	header( 'location: ' . admin_url() . '?login' );
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"/>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>

	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
	<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<strong class="logo"><a href="<?php echo $this->PLUGIN_URL ?>"><img
					src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
		<ul id="nav">
			<li><a href="<?php echo $this->PLUGIN_URL ?>?adminsettings">Admin Dashboard</a></li>
			<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank">Support</a></li>
			<li><a href="<?php echo $this->PLUGIN_URL ?>?logout">Log Out</a></li>
		</ul>
	</div>
	<div id="main">
		<div class="container">
			<div class="headbar">

				<?php if ( version_compare( PHP_VERSION, "5.3", "<" ) ): ?>
					<div class="alert alert-danger pull-left" style="margin-bottom: 0">
						You are using PHP v.<?php echo PHP_VERSION ?> but for the stable work of Agency license PHP
						v5.3+ requered. Please upgrade your PHP
					</div>
				<?php endif; ?>

				<em class="date"><?php echo @date( "F d, Y" ) ?></em>
			</div>
