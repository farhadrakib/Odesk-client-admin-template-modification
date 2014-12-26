<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 18-11-2014
 * Time: 11:22
 */

if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}

$db_instances = array( "New Instance" => array( "DBInstanceStatus" => "available" ) );
$instances    = $rds->ListInstances();
if ( isset( $instances['error'] ) ) {
	$errors[] = $instances['error']['Caught exception'];
} else {
	foreach ( $instances as $instance ) {
//	print_r($instance);
		if ( ! isset( $db_instances[ $instance['DBInstanceIdentifier'] ] ) ) {
			$db_instances[ $instance['DBInstanceIdentifier'] ] = array();
		}
		$db_instances[ $instance['DBInstanceIdentifier'] ]['DBInstanceStatus'] = $instance['DBInstanceStatus'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['AvailabilityZone'] = $instance['AvailabilityZone'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['Endpoint']         = $instance['Endpoint'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['DBName']           = $instance['DBName'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['AllocatedStorage'] = $instance['AllocatedStorage'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['DBInstanceClass']  = $instance['DBInstanceClass'];
		$db_instances[ $instance['DBInstanceIdentifier'] ]['MasterUsername']   = $instance['MasterUsername'];
	}
}
if ( isset( $response->success ) ) {
	showMessages( $response->success );
} elseif ( ! empty( $errors ) ) {
	showErrors( $errors );
	die();
}
?>

<form id="rdsform" class="form-vertical no-padding no-margin" method="post" action="" style="overflow: hidden; clear: both">
<input type="hidden" id="fldTask" name="fldTask" value="install"/>

<input type="hidden" name="email" value="<?php echo $_POST['email'] ?>"/>
<input type="hidden" name="pass1" value="<?php echo $_POST['pass1'] ?>"/>

<input type="hidden" name="iam_key" value="<?php echo $_POST['iam_key'] ?>"/>
<input type="hidden" name="iam_secret" value="<?php echo $_POST['iam_secret'] ?>"/>
<input type="hidden" name="iam_region" value="<?php echo $_POST['iam_region'] ?>"/>

<input type="hidden" name="license" value="<?php echo $_POST['license'] ?>"/>

<input type="hidden" name="options[Engine]" value="MySQL"/>

<div class="row">
<div class="row">
	<div id="db_instance_div" style="width: 250px; float: left">
		<label for="name">Database Instance Identifier:</label>

		<div class="text">
			<select id="DBInstanceIdentifier" name="options[DBInstanceIdentifier]">
				<?php
				foreach ( $db_instances as $db_instance => $db_instance_values ) {
					if ( isset( $db_instance_values['DBInstanceStatus'] ) && $db_instance_values['DBInstanceStatus'] != 'available' ) {
						continue;
					}
					?>
					<option value="<?php echo $db_instance ?>" <?php echo isset( $_POST['options']['DBInstanceIdentifier'] ) && $_POST['options']['DBInstanceIdentifier'] == $db_instance ? 'selected="selected"' : ""; ?>>
						<?php echo $db_instance; ?>
					</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div id="db_instance_name_div" style="width: 250px; float: left; margin-left: 20px;">
		<label for="name">Database Instance Identifier:</label>

		<div class="text">
			<input type="text" name="DBInstanceIdentifier" value="<?php echo isset( $_POST['DBInstanceIdentifier'] ) ? $_POST['DBInstanceIdentifier'] : ""; ?>"/>
		</div>
	</div>
</div>
<div id="endpoint" class="row" style="display: none;">
	<label for="name">DB Endpoint:</label>

	<div class="text">
	</div>

</div>
<div id="rds_server_config">
	<div class="row">
		<div style="width: 250px; float: left;">
			<label for="name">Instance Class:</label>

			<div class="text">
				<select name="options[DBInstanceClass]" class="gwt-ListBox" style="width: 230px;">
					<option value="db.t1.micro"
					        title="db.t1.micro &mdash; 1 vCPU, 0.613 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.t1.micro" ? 'selected="selected"' : ""; ?>>
						db.t1.micro &mdash; 1 vCPU, 0.613 GiB RAM
					</option>
					<option value="db.t2.micro"
					        title="db.t2.micro &mdash; 1 vCPU, 1 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.t2.micro" ? 'selected="selected"' : ""; ?>>
						db.t2.micro &mdash; 1 vCPU, 1 GiB RAM
					</option>
					<option value="db.t2.small"
					        title="db.t2.small &mdash; 1 vCPU, 2 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.t2.small" ? 'selected="selected"' : ""; ?>>
						db.t2.small &mdash; 1 vCPU, 2 GiB RAM
					</option>
					<option value="db.t2.medium"
					        title="db.t2.medium &mdash; 2 vCPU, 4 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.t2.medium" ? 'selected="selected"' : ""; ?>>
						db.t2.medium &mdash; 2 vCPU, 4 GiB RAM
					</option>
					<option value="db.m3.medium"
					        title="db.m3.medium &mdash; 1 vCPU, 3.75 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m3.medium" ? 'selected="selected"' : ""; ?>>
						db.m3.medium &mdash; 1 vCPU, 3.75 GiB RAM
					</option>
					<option value="db.m3.large"
					        title="db.m3.large &mdash; 2 vCPU, 7.5 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m3.large" ? 'selected="selected"' : ""; ?>>
						db.m3.large &mdash; 2 vCPU, 7.5 GiB RAM
					</option>
					<option value="db.m3.xlarge"
					        title="db.m3.xlarge &mdash; 4 vCPU, 15 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m3.xlarge" ? 'selected="selected"' : ""; ?>>
						db.m3.xlarge &mdash; 4 vCPU, 15 GiB RAM
					</option>
					<option value="db.m3.2xlarge"
					        title="db.m3.2xlarge &mdash; 8 vCPU, 30 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m3.2xlarge" ? 'selected="selected"' : ""; ?>>
						db.m3.2xlarge &mdash; 8 vCPU, 30 GiB RAM
					</option>
					<option value="db.r3.large"
					        title="db.r3.large &mdash; 2 vCPU, 15 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.r3.large" ? 'selected="selected"' : ""; ?>>
						db.r3.large &mdash; 2 vCPU, 15 GiB RAM
					</option>
					<option value="db.r3.xlarge"
					        title="db.r3.xlarge &mdash; 4 vCPU, 30.5 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.r3.large" ? 'selected="selected"' : ""; ?>>
						db.r3.xlarge &mdash; 4 vCPU, 30.5 GiB RAM
					</option>
					<option value="db.r3.2xlarge"
					        title="db.r3.2xlarge &mdash; 8 vCPU, 61 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.r3.2xlarge" ? 'selected="selected"' : ""; ?>>
						db.r3.2xlarge &mdash; 8 vCPU, 61 GiB RAM
					</option>
					<option value="db.r3.4xlarge"
					        title="db.r3.4xlarge &mdash; 16 vCPU, 122 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.r3.4xlarge" ? 'selected="selected"' : ""; ?>>
						db.r3.4xlarge &mdash; 16 vCPU, 122 GiB RAM
					</option>
					<option value="db.r3.8xlarge"
					        title="db.r3.8xlarge &mdash; 32 vCPU, 244 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.r3.8xlarge" ? 'selected="selected"' : ""; ?>>
						db.r3.8xlarge &mdash; 32 vCPU, 244 GiB RAM
					</option>
					<option value="db.m2.xlarge"
					        title="db.m2.xlarge &mdash; 2 vCPU, 17.1 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m2.xlarge" ? 'selected="selected"' : ""; ?>>
						db.m2.xlarge &mdash; 2 vCPU, 17.1 GiB RAM
					</option>
					<option value="db.m2.2xlarge"
					        title="db.m2.2xlarge &mdash; 4 vCPU, 34 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m2.2xlarge" ? 'selected="selected"' : ""; ?>>
						db.m2.2xlarge &mdash; 4 vCPU, 34 GiB RAM
					</option>
					<option value="db.m2.4xlarge"
					        title="db.m2.4xlarge &mdash; 8 vCPU, 68 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m2.4xlarge" ? 'selected="selected"' : ""; ?>>
						db.m2.4xlarge &mdash; 8 vCPU, 68 GiB RAM
					</option>
					<option value="db.cr1.8xlarge"
					        title="db.cr1.8xlarge &mdash; 32 vCPU, 244 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.cr1.8xlarge" ? 'selected="selected"' : ""; ?>>
						db.cr1.8xlarge &mdash; 32 vCPU, 244 GiB RAM
					</option>
					<option value="db.m1.small"
					        title="db.m1.small &mdash; 1 vCPU, 1.7 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m1.small" ? 'selected="selected"' : ""; ?>>
						db.m1.small &mdash; 1 vCPU, 1.7 GiB RAM
					</option>
					<option value="db.m1.medium"
					        title="db.m1.medium &mdash; 1 vCPU, 3.75 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m1.medium" ? 'selected="selected"' : ""; ?>>
						db.m1.medium &mdash; 1 vCPU, 3.75 GiB RAM
					</option>
					<option value="db.m1.large"
					        title="db.m1.large &mdash; 2 vCPU, 7.5 GiB RAM <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m1.large" ? 'selected="selected"' : ""; ?>">
						db.m1.large &mdash; 2 vCPU, 7.5 GiB RAM
					</option>
					<option value="db.m1.xlarge"
					        title="db.m1.xlarge &mdash; 4 vCPU, 15 GiB RAM" <?php echo isset( $_POST['options']['DBInstanceClass'] ) && $_POST['options']['DBInstanceClass'] == "db.m1.xlarge" ? 'selected="selected"' : ""; ?>>
						db.m1.xlarge &mdash; 4 vCPU, 15 GiB RAM
					</option>
				</select>
			</div>
		</div>
		<div style="width: 250px; float: left; margin-left: 20px;">
			<label for="name">Use Multi-AZ Development:</label>

			<div class="text">
				<select name="options[MultiAZ]" style="width: 230px;">
					<option value="1" title="Enabled" <?php echo isset( $_POST['options']['MultiAZ'] ) && $_POST['options']['MultiAZ'] === true ? 'selected="selected"' : ""; ?>>Enabled</option>
					<option value="0" title="Disabled" <?php echo isset( $_POST['options']['MultiAZ'] ) && $_POST['options']['MultiAZ'] === false ? 'selected="selected"' : ""; ?>>Disabled</option>
				</select>
			</div>
		</div>

	</div>
	<div class="row">
		<div style="width: 250px; float: left;">
			<label for="name">Database Storage Type</label>

			<div class="text">
				<select name="options[StorageType]" style="width: 230px;">
					<option value="gp2" selected="selected">General Purpose (SSD)</option>
					<option value="io1">Provisioned IOPS (SSD)</option>
					<option value="standard">Magnetic</option>
				</select>
			</div>
		</div>
		<div style="width: 250px; float: left; margin-left: 20px;">
			<label for="name">Allocated Storage (GB)</label>

			<div class="text">
				<input name="options[AllocatedStorage]" type="text" value="<?php echo isset( $_POST['options']['AllocatedStorage'] ) ? $_POST['options']['AllocatedStorage'] : "5"; ?>"/>
			</div>
		</div>
		General Storage: Min 5GB, Max 3072GB<br/>
		Provisioned IOPS: Min 100GB, Max 3072GB<br/>
		Please note that while you can always increase storage size, there's no easy way to decrease storage size!
	</div>

	<div class="row">
		<div style="width: 250px; float: left;">
			<label for="name">Database Name:</label>

			<div class="text">
				<input id="db_name" name="options[DBName]" type="text" value="<?php echo isset( $_POST['options']['DBName'] ) ? $_POST['options']['DBName'] : ""; ?>"/>
			</div>
		</div>
		<div style="width: 250px; float: left; margin-left: 20px;">
			<label for="name">Table Prefix:</label>

			<div class="text">
				<input id="db_user" name="db_prefix" type="text" value="<?php echo isset( $_POST['db_prefix'] ) ? $_POST['db_prefix'] : "hmt_"; ?>"/>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<label for="name">Master User Name:</label>

	<div class="text">
		<input id="db_user" name="options[MasterUsername]" type="text" value="<?php echo isset( $_POST['options']['MasterUsername'] ) ? $_POST['options']['MasterUsername'] : ""; ?>"/>
	</div>
</div>
<div class="row">
	<div style="width: 250px; float: left;">
		<label for="name">Master Password:</label>

		<div class="text">
			<input id="db_password" name="options[MasterUserPassword]" value="" type="password" autocomplete="off"/>
		</div>
	</div>
	<div style="width: 250px; float: left; margin-left: 20px;">
		<label for="name">Confirm Master Password:</label>

		<div class="text">
			<input id="db_password" name="db_password_confirm" value="" type="password" autocomplete="off"/>
		</div>
	</div>
	Password must include at least 8 characters
</div>
</div>


<input type="submit" id="login-btn" class="btn btn-block btn-inverse fldsubmitLicense" value="Install"/>
</form>
<script>

	var instances = jQuery.parseJSON('<?php echo json_encode($db_instances); ?>');

	function enableFields(parent, val) {
		parent.find("input, select").each(function () {
			$(this).removeAttr("disabled");
			$(this).removeAttr("readonly");
		});
	}

	function disableFields(parent, val) {
		parent.find("input, select").each(function () {
			$(this).attr("disabled", "disabled");
			$(this).attr("readonly", "");
		});
	}


	function checkRequired(obj, focused) {
		if (obj.val() == "") {
			obj.addClass("alert");
			if (focused) {
				obj.focus();
			}
			return false;
		}
		return true;
	}

	jQuery(document).ready(function ($) {

		function checkDBInstance(val) {
			if (val == "New Instance") {
				$("#db_instance_div").css("width", "250px");
				$("#db_instance_name_div").css("display", "block");
				$("#DBInstanceIdentifier").css("width", "230px");
				enableFields($("#rds_server_config"), val);
				$("[name='DBInstanceIdentifier']").val("<?php echo isset( $_POST['DBInstanceIdentifier'] ) ? $_POST['DBInstanceIdentifier'] : "New Instance"; ?>");
				$("[name='options[DBInstanceClass]']").val("<?php echo isset( $_POST['options']['DBInstanceClass'] ) ? $_POST['options']['DBInstanceClass'] : "db.t1.micro"; ?>");
				$("[name='options[MultiAZ]']").val(<?php echo isset( $_POST['options']['MultiAZ'] ) && $_POST['options']['MultiAZ'] ? 1 : 0; ?>);
				$("[name='options[StorageType]']").val("<?php echo isset( $_POST['options']['StorageType'] ) ? $_POST['options']['StorageType'] : "gp2"; ?>");
				$("[name='options[AllocatedStorage]']").val("<?php echo isset( $_POST['options']['AllocatedStorage'] ) ? $_POST['options']['AllocatedStorage'] : "5"; ?>");
				$("[name='options[DBName]']").val("<?php echo isset( $_POST['options']['DBName'] ) ? $_POST['options']['DBName'] : ""; ?>");
				$("[name='options[MasterUsername]']").val("<?php echo isset( $_POST['options']['MasterUsername'] ) ? $_POST['options']['MasterUsername'] : ""; ?>");
				$("#endpoint").css("display", "none");
				$("#endpoint div.text").html("");
			} else {
				$("#db_instance_div").css("width", "100%");
				$("#db_instance_name_div").css("display", "none");
				$("#DBInstanceIdentifier").css("width", "500px");
				$("[name='options[DBInstanceIdentifier]']").val(val);
				$("[name='options[DBInstanceClass]']").val(instances[val]['DBInstanceClass']);
				$("[name='options[MultiAZ]']").val(instances[val]['MultiAZ']);
				$("[name='options[StorageType]']").val(instances[val]['StorageType']);
				$("[name='options[AllocatedStorage]']").val(instances[val]['AllocatedStorage']);
				$("[name='options[DBName]']").val(instances[val]['DBName']);
				$("[name='options[MasterUsername]']").val(instances[val]['MasterUsername']);
				$("#endpoint div.text").html(instances[val]['Endpoint']['Address'] + ":" + instances[val]['Endpoint']['Port']);//.append("<input type='hidden' name='options[DBEndpoint]' value='" + instances[val]['Endpoint']['Address'] + ":" + instances[val]['Endpoint']['Port'] + "' />");
				$("#endpoint").css("display", "block");
				disableFields($("#rds_server_config"), val);
			}
		}

		$("#DBInstanceIdentifier").change(function () {
			checkDBInstance($(this).val());
			$("#rdsform input, #rdsform select").removeClass("alert");
		});


		checkDBInstance($("#DBInstanceIdentifier").val());

		$("#rdsform input, #rdsform select").change(function () {
			$(this).removeClass("alert");
		});

		$("#rdsform input, #rdsform select").keydown(function () {
			$(this).removeClass("alert");
		});

		$("#rdsform").submit(function () {
			$("#rdsform input, #rdsform select").removeClass("alert");

			// Validate
			$result = true;
			if ($("#DBInstanceIdentifier").val() == "") {
				$result = checkRequired($("[name='DBInstanceIdentifier']"), $result);
			}
			$result = checkRequired($("[name='options[AllocatedStorage]']"), $result);
			$result = checkRequired($("[name='options[DBName]']"), $result);
			$result = checkRequired($("[name='options[MasterUsername]']"), $result);
			$result = checkRequired($("[name='options[MasterUserPassword]']"), $result);
			$result = checkRequired($("[name='db_password_confirm']"), $result);

			if ($("[name='options[MasterUserPassword]']").val() != $("[name='db_password_confirm']").val()) {
				$("[name='options[MasterUserPassword]']").addClass("alert");
				$("[name='db_password_confirm']").addClass("alert");
				$result = false;
			}

			if ($("[name='options[MasterUserPassword]']").val().length < 8) {
				$("[name='options[MasterUserPassword]']").addClass("alert");
				$("[name='db_password_confirm']").addClass("alert");
				$result = false;
			}

			return $result;
		});
	});
</script>