<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19-11-2014
 * Time: 14:40
 */

require_once( "includes/functions/fn-functions.php" );
require_once( "includes/functions/fn-rds-process.php" );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		<strong class="logo add"><a href="#"><img src="<?php echo $this->PLUGIN_URL ?>images/hmtracker-logo.png" alt="logo"/></a></strong>
	</div>
</div>
<div id="main">
	<div class="login-box" style="max-width: 550px">
		<div class="holder">
			<?php showErrors( $errors ); ?>
			<h2>RDS Setup Status: <?php echo showStatus( "Waiting for status..." ); ?> <img id="loading" src="<?php echo home_url() ?>/assets/img/loading.gif" style="display: none;"/></h2>
			<div id="warning">
				<h2 style="text-align: center; line-height: 24px;">Waiting for Amazon RDS to create the DB Instance. This can take a long time - please be patient!</h2>
				<h1 style="text-align: center; color: #c00000;">Please do not leave this page!</h1>
				<p style="text-align: center; font-size: 14px; ">Average waiting time is 5-30min or more. As soon as the RDS setup is complete the installation will continue automatically.</p>
			</div>
			<div id="available" style="display: none;">
				<h1 style="margin-top: 35px; text-align: center; color: #093;">RDS Setup Complete!</h1>
				<h2 style="text-align: center; line-height: 24px;">Heatmap Tracker setup is continuing<br />please wait for the login screen</h2>
				<p>If the page does not reload please automatically please click <a href="<?php echo $this->PLUGIN_URL; ?>">Reload Page</a></p>
			</div>
			<form id="rds_install_form" action="" method="post">
				<input name="fldTask" type="hidden" value="complete" />

				<input name="iam_key" type="hidden" value="<?php echo $_POST['iam_key']; ?>" />
				<input name="iam_secret" type="hidden" value="<?php echo $_POST['iam_secret']; ?>" />
				<input name="iam_region" type="hidden" value="<?php echo $_POST['iam_region']; ?>" />
				<input name="DBInstanceIdentifier" type="hidden" value="<?php echo $_POST['DBInstanceIdentifier']; ?>" />

				<input name="license" type="hidden" value="<?php echo $_POST['license'] ?>" />

				<input name="email" type="hidden" value="<?php echo $_POST['email']; ?>" />
				<input name="pass1" type="hidden" value="<?php echo $_POST['pass1']; ?>" />

				<input name="db_name" type="hidden" value="<?php echo $_POST['options']['DBName']; ?>" />
				<input name="db_user" type="hidden" value="<?php echo $_POST['options']['MasterUsername']; ?>" />
				<input name="db_password" type="hidden" value="<?php echo $_POST['options']['MasterUserPassword']; ?>" />
				<input name="db_prefix" type="hidden" value="<?php echo $_POST['db_prefix']; ?>" />
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

	jQuery(document).ready(function () {
		(function poll() {
			setTimeout(function () {
				$("#loading").css("display", "inline-block");
				$.ajax("<?php echo admin_url() ?>?rds_poll", {
					type: "post",
					dataType: "json",
					data: {
						iam_key: '<?php echo $_POST['iam_key'] ?>',
						iam_secret: '<?php echo $_POST['iam_secret'] ?>',
						iam_region: '<?php echo $_POST['iam_region'] ?>',
						instance: '<?php echo $_POST['options']['DBInstanceIdentifier']; ?>'
					}
				}).success(function (data) {
					if (data.status) {
						$("#status").html(data.status_formatted);
						if (data.status == "available") {
							$("#warning").hide();
							$("#available").show();
							$("#rds_install_form").submit();
						}
					}
					$("#loading").css("display", "none");
					poll();
				});
			}, 10000);
		})();

	});
</script>
</body>
</html>