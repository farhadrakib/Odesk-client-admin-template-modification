<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 18-11-2014
 * Time: 10:49
 */
if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}
?>

<!-- BEGIN LOGIN FORM -->
<div id="help-modal" class="modal hide">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">×</button>
		<h3>Help</h3>
	</div>
	<div class="modal-body">
		<h4>Create Database</h4>
		<iframe width="525" height="350" src="//www.youtube.com/embed/AV5cJ1mz_4g" frameborder="0" allowfullscreen></iframe>
		<h4>Installing Heat Map Stand Alone</h4>
		<iframe width="525" height="350" src="//www.youtube.com/embed/Lt4CvmgtLnM" frameborder="0" allowfullscreen></iframe>
	</div>
</div>
<a href="<?php echo $this->OPTIONS['brandsupport'] ?>/" target="_blank" class="btn btn-block btn-inverse pull-left" style="margin:0; width:100px">Support</a>
<a href="#help-modal" data-toggle="modal" class="btn btn-block btn-inverse pull-left" style="margin-left: 10px; width:100px">Help</a>
<?php showErrors( $errors ); ?>
<form id="regform" class="form-vertical no-padding no-margin" method="post" action="" style="overflow: hidden; clear: both">
	<input type="hidden" id="fldTask" name="fldTask" value="<?php echo $reregister ? "reregister" : "register"; ?>"/>

	<div style="width: <?php echo $reregister ? "350px" : "250px" ?>; float: left">

		<p class="center" style="margin-bottom: 46px;">Enter your registration data</p>

		<div class="row">
			<label for="name">License Key:</label>

			<div class="text">
				<input id="license" name="license" value="<?php echo ( isset( $_POST["license"] ) ) ? $_POST["license"] : "" ?>" type="text" autocomplete="off"/>
			</div>
		</div>
		<div class="row">
			<label for="name">E-mail:</label>

			<div class="text">
				<input id="email" name="email" value="<?php echo ( isset( $_POST["email"] ) ) ? $_POST["email"] : "" ?>" type="text"  autocomplete="off"/>
			</div>
		</div>
		<div class="row">
			<label for="name">Password:</label>

			<div class="text"><input id="pass1" name="pass1" type="password" autocomplete="off"/></div>
		</div>
		<div class="row">
			<label for="name">Password Again:</label>

			<div class="text"><input id="pass2" name="pass2" type="password" autocomplete="off"/></div>
		</div>
	</div>

	<div style="width: 250px; float: left; margin-left: 20px;<?php echo $reregister ? "display:none;" : "" ?>">
		<p class="center">Enter database config</p>

		<div style="margin-bottom: 15px;"><input type="checkbox" id="use_rds" name="use_rds" value="use_rds" <?php echo isset( $_POST['use_rds'] ) ? 'checked="checked"' : ''; ?>/> Use Amazon RDS</div>

		<div id="mysql_server_config">
			<div class="row">
				<label for="name">Database Name:</label>

				<div class="text">
					<input id="db_name" name="db_name" value="<?php echo ( isset( $_POST["db_name"] ) ) ? $_POST["db_name"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">Database User:</label>

				<div class="text">
					<input id="db_user" name="db_user" value="<?php echo ( isset( $_POST["db_user"] ) ) ? $_POST["db_user"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">Database Password:</label>

				<div class="text">
					<input id="db_password" name="db_password" value="<?php echo ( isset( $_POST["db_password"] ) ) ? $_POST["db_password"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">Database Host</label>

				<div class="text">
					<input id="db_host" name="db_host" value="<?php echo ( isset( $_POST["db_host"] ) ) ? $_POST["db_host"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">Table Prefix</label>

				<div class="text">
					<input id="db_prefix" name="db_prefix" value="<?php echo ( isset( $_POST["db_prefix"] ) ) ? $_POST["db_prefix"] : "hmt_" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
		</div>

		<div id="iam_config" style="display: none;">
			<div class="row">
				<label for="name">IAM Key:</label>

				<div class="text">
					<input id="iam_key" name="iam_key" value="<?php echo ( isset( $_POST["iam_key"] ) ) ? $_POST["iam_key"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">IAM Secret:</label>

				<div class="text">
					<input id="iam_secret" name="iam_secret" value="<?php echo ( isset( $_POST["iam_secret"] ) ) ? $_POST["iam_secret"] : "" ?>" type="text" autocomplete="off"/>
				</div>
			</div>
			<div class="row">
				<label for="name">IAM Region:</label>

				<div class="text">
					<select id="iam_region" name="iam_region">
						<option value="us-east-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "us-east-1" ) ? 'selected="selected"' : "" ?>>US East (N. Virginia)</option>
						<option value="us-west-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "us-west-1" ) ? 'selected="selected"' : "" ?>>US West (N. California)</option>
						<option value="us-west-2" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "us-west-2" ) ? 'selected="selected"' : "" ?>>US West (Oregon)</option>
						<option value="eu-west-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "eu-west-1" ) ? 'selected="selected"' : "" ?>>EU (Ireland)</option>
						<option value="eu-central-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "eu-central-1" ) ? 'selected="selected"' : "" ?>>EU (Frankfurt)</option>
						<option value="ap-northeast-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "ap-northeast-1" ) ? 'selected="selected"' : "" ?>>Asia Pacific (Tokyo)</option>
						<option value="ap-southeast-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "ap-southeast-1" ) ? 'selected="selected"' : "" ?>>Asia Pacific (Singapore)</option>
						<option value="ap-southeast-2" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "ap-southeast-2" ) ? 'selected="selected"' : "" ?>>Asia Pacific (Sydney)</option>
						<option value="sa-east-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "sa-east-1" ) ? 'selected="selected"' : "" ?>>South America (Sao Paulo)</option>
						<option value="us-gov-west-1" <?php echo ( isset( $_POST["iam_region"] ) && $_POST['iam_region'] == "us-gov-west-1" ) ? 'selected="selected"' : "" ?>>AWS GovCloud (US)</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<input type="submit" id="login-btn" class="btn btn-block btn-inverse fldsubmitLicense" value="Install"/>

	<div style="text-align: right; font-size: 10px; margin-top: 15px;">v<?php echo CURRENT_VERSION; ?></div>

</form>
<script>
	function enableFields(parent) {
		parent.find("input, select").each(function () {
			$(this).removeAttr("disabled");
		});
	}

	function disableFields(parent) {
		parent.find("input, select").each(function () {
			$(this).attr("disabled", "disabled");
		});
	}

	function showError(obj, error) {
		var parent = obj.parent();

		if(parent.find("span:contains(" + error + ")").html() != error ) {
			parent.append('<span style="color: red; display: block;">' + error + '</span>');
		}
	}

	function removeError(obj) {
		obj.parent().find("span").remove();
	}

	function checkRequired(obj, focused) {
		if (obj.val() == "") {
			obj.addClass("alert");
			if (focused) {
				obj.focus();
			}
			showError(obj, "This field is required");
			return false;
		}
		if(!focused) {
			return focused;
		}
		return true;
	}

	function checkRds(elem) {
		if (elem.is(":checked")) {
			$("#mysql_server_config").hide();
			$("#iam_config").show();
			disableFields($("#mysql_server_config"));
			enableFields($("#iam_config"));
			$("#login-btn").val("Continue");
		} else {
			$("#mysql_server_config").show();
			$("#iam_config").hide();
			disableFields($("#iam_config"));
			enableFields($("#mysql_server_config"));
			if( $("#fldTask").val() == "register") {
				$("#login-btn").val("Install");
			} else {
				$("#login-btn").val("Register");
			}
		}
	}

	function IsEmail(email) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(!regex.test(email)) {
			return false;
		}else{
			return true;
		}
	}

	jQuery(document).ready(function () {

		checkRds($("#use_rds"));

		$("#use_rds").click(function () {
			checkRds($(this));
		});

		$("#regform input, #regform select").change(function () {
			$(this).removeClass("alert");
			removeError($(this));
		});

		$("#regform input, #regform select").keydown(function () {
			$(this).removeClass("alert");
			removeError($(this));
		});

		$("#regform").submit(function () {
			$("#rdsform input, #rdsform select").removeClass("alert");

			// Validate
			$result = true;
			$result = checkRequired($("#license"), $result);
			$result = checkRequired($("#email"), $result);
			$result = checkRequired($("#pass1"), $result);
			$result = checkRequired($("#pass2"), $result);

			if ($("#pass1").val() != $("#pass2").val()) {
				$("#pass1").addClass("alert");
				$("#pass2").addClass("alert");
				showError($("#pass1"), "Password mismatch");
				showError($("#pass2"), "Password mismatch");
				$result = false;
			}

			if ($("#fldTask").val() != "reregister") {
				if (!$("#use_rds").is(":checked")) {
					$result = checkRequired($("#db_name"), $result);
					$result = checkRequired($("#db_user"), $result);
					$result = checkRequired($("#db_password"), $result);
					$result = checkRequired($("#db_host"), $result);
					$result = checkRequired($("#db_prefix"), $result);
				} else {
					$result = checkRequired($("#iam_key"), $result);
					$result = checkRequired($("#iam_secret"), $result);
					$result = checkRequired($("#iam_region"), $result);
				}
			}
			return $result;
		});
	});
</script>
<!-- END JAVASCRIPTS -->