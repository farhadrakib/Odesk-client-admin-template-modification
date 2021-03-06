<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://HeatMapTracker.com
 */
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
		<strong class="logo add"><a href="<?php echo $this->PLUGIN_URL ?>"><img src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
	</div>
	<div id="main">
		<div class="login-box">
			<div class="holder">
				<!-- BEGIN LOGIN FORM -->
				<?php
				global $error_msg;
				if ( isset($_SESSION['error_msg']['login']) && $_SESSION['error_msg']['login'] != "" ) {
					?>
					<strong style="color: #F00"><?php echo $_SESSION['error_msg']['login'] ?></strong><br/><br/>
					<?php
					$_SESSION['error_msg']['login'] = "";
				} ?>

				<form id="loginform" class="form-vertical no-padding no-margin" method="post" action="<?php echo admin_url() ?>">
					<div class="row">
						<label for="name">Email Address:</label>

						<div class="text"><input id="input-username" type="text" placeholder="Email Address" name="username"/></div>
					</div>
					<div class="row">
						<label for="pass">Password:</label>

						<div class="text"><input id="input-password" type="password" placeholder="Password" name="password"/></div>
					</div>
					<div class="row">
						<a href="javascript:;" class="forget" id="forget-password">Forgot Password?</a>
					</div>

					<input type="submit" id="login-btn" class="btn btn-block btn-inverse" value="Login"/>

					<div style="text-align: right; font-size: 10px; margin-top: 15px;">v<?php echo CURRENT_VERSION; ?></div>

				</form>
				<!-- END LOGIN FORM -->
				<!-- BEGIN FORGOT PASSWORD FORM -->
				<form id="forgotform" class="form-vertical no-padding no-margin hide" method="post"
				      action="<?php echo admin_url() ?>?hmtrackerrestore">
					<p class="center"></p>

					<div class="row">
						<label for="name">Enter your e-mail address below to reset your password:</label>

						<div class="text"><input id="input-email" type="text" placeholder="Email" name="uemail"/></div>
					</div>

					<input type="submit" id="forget-btn" class="btn btn-block btn-inverse" value="Submit"/>
				</form>
				<!-- END FORGOT PASSWORD FORM -->
			</div>
		</div>
	</div>
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/excanvas.js"></script>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/respond.js"></script>
<![endif]-->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/login.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		// initiate layout and plugins
		App.init();
		Login.init();
	});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>