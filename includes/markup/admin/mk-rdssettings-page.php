<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13-11-2014
 * Time: 17:09
 */

if ( $rds ) {
	?>

	<div>
		<div style="float: right;">
			<h2><img id="loading" src="<?php echo home_url() ?>/assets/img/loading.gif" style="display: none;"/> Status: <?php echo showStatus( $instance['DBInstanceStatus'] ); ?></h2>
		</div>
		<div>
			<h2>AWS RDS Settings</h2>
		</div>
	</div>

	<?php echo showError( "rds_error", $errors, true ); ?>

	<?php if ( $instance['DBInstanceStatus'] != 'available' ) { ?>
		<h4 style="text-align: center;">Your request is being processed by the Amazon AWS service. This can take several minutes. <br/>No further changes are permitted during this process. <br/>This page will automatically reload when the
			process is complete.</h4>

	<?php } else { ?>
		<?php if ( empty( $errors ) && isset( $_POST['form'] ) && $_POST['form'] != 'aim' ) {
			echo renderStatusbar( "Your request has been queued with the Amazon AWS service. Please stand by while your request is being processed. This can take several minutes.", "success" );
		}?>
		<form action="<?php echo admin_url() ?>?rds" method="POST" class="form-horizontal" id="rdsform" autocomplete="off">
		<input type="hidden" name="DBInstanceIdentifier" value="<?php echo $instance['DBInstanceIdentifier']; ?>"/>
		<input type="hidden" name="modify" value="1"/>

		<div class="control-group">
			<label class="control-label">RDS Instance</label>

			<div class="controls">
				<input style="height: 30px;" type="text" disabled class="license_key" value="<?php echo $instance['DBInstanceIdentifier']; ?>"/>
				<a id="reboot" class="btn btn-primary btn-mini fldsubmitLicenseU" type="button" href="javascript:void(0);">Reboot</a>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label">RDS End Point</label>

			<div class="controls">
				<input style="height: 30px; width: 550px;" class="license_key" type="text" disabled
				       value="<?php echo isset( $instance['Endpoint']['Address'] ) ? $instance['Endpoint']['Address'] . ":" . $instance['Endpoint']['Port'] : ""; ?>"/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Instance Class</label>

			<div class="controls">
				<select name="options[DBInstanceClass]" class="gwt-ListBox" style="width: 265px;">
					<option value="db.t1.micro" <?php echo( $instance['DBInstanceClass'] == 'db.t1.micro' ? 'selected="selected"' : '' ); ?> title="db.t1.micro &mdash; 1 vCPU, 0.613 GiB RAM">db.t1.micro &mdash; 1 vCPU, 0.613 GiB RAM
					</option>
					<option value="db.t2.micro" <?php echo( $instance['DBInstanceClass'] == 'db.t2.micro' ? 'selected="selected"' : '' ); ?> title="db.t2.micro &mdash; 1 vCPU, 1 GiB RAM">db.t2.micro &mdash; 1 vCPU, 1 GiB RAM</option>
					<option value="db.t2.small" <?php echo( $instance['DBInstanceClass'] == 'db.t2.small' ? 'selected="selected"' : '' ); ?> title="db.t2.small &mdash; 1 vCPU, 2 GiB RAM">db.t2.small &mdash; 1 vCPU, 2 GiB RAM</option>
					<option value="db.t2.medium" <?php echo( $instance['DBInstanceClass'] == 'db.t2.medium' ? 'selected="selected"' : '' ); ?> title="db.t2.medium &mdash; 2 vCPU, 4 GiB RAM">db.t2.medium &mdash; 2 vCPU, 4 GiB RAM</option>
					<option value="db.m3.medium" <?php echo( $instance['DBInstanceClass'] == 'db.m3.medium' ? 'selected="selected"' : '' ); ?> title="db.m3.medium &mdash; 1 vCPU, 3.75 GiB RAM">db.m3.medium &mdash; 1 vCPU, 3.75 GiB RAM
					</option>
					<option value="db.m3.large" <?php echo( $instance['DBInstanceClass'] == 'db.m3.large' ? 'selected="selected"' : '' ); ?> title="db.m3.large &mdash; 2 vCPU, 7.5 GiB RAM">db.m3.large &mdash; 2 vCPU, 7.5 GiB RAM</option>
					<option value="db.m3.xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m3.xlarge' ? 'selected="selected"' : '' ); ?> title="db.m3.xlarge &mdash; 4 vCPU, 15 GiB RAM">db.m3.xlarge &mdash; 4 vCPU, 15 GiB RAM</option>
					<option value="db.m3.2xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m3.2xlarge' ? 'selected="selected"' : '' ); ?> title="db.m3.2xlarge &mdash; 8 vCPU, 30 GiB RAM">db.m3.2xlarge &mdash; 8 vCPU, 30 GiB RAM
					</option>
					<option value="db.r3.large" <?php echo( $instance['DBInstanceClass'] == 'db.r3.large' ? 'selected="selected"' : '' ); ?> title="db.r3.large &mdash; 2 vCPU, 15 GiB RAM">db.r3.large &mdash; 2 vCPU, 15 GiB RAM</option>
					<option value="db.r3.xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.r3.xlarge' ? 'selected="selected"' : '' ); ?> title="db.r3.xlarge &mdash; 4 vCPU, 30.5 GiB RAM">db.r3.xlarge &mdash; 4 vCPU, 30.5 GiB RAM
					</option>
					<option value="db.r3.2xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.r3.2xlarge' ? 'selected="selected"' : '' ); ?> title="db.r3.2xlarge &mdash; 8 vCPU, 61 GiB RAM">db.r3.2xlarge &mdash; 8 vCPU, 61 GiB RAM
					</option>
					<option value="db.r3.4xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.r3.4xlarge' ? 'selected="selected"' : '' ); ?> title="db.r3.4xlarge &mdash; 16 vCPU, 122 GiB RAM">db.r3.4xlarge &mdash; 16 vCPU, 122 GiB RAM
					</option>
					<option value="db.r3.8xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.r3.8xlarge' ? 'selected="selected"' : '' ); ?> title="db.r3.8xlarge &mdash; 32 vCPU, 244 GiB RAM">db.r3.8xlarge &mdash; 32 vCPU, 244 GiB RAM
					</option>
					<option value="db.m2.xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m2.xlarge' ? 'selected="selected"' : '' ); ?> title="db.m2.xlarge &mdash; 2 vCPU, 17.1 GiB RAM">db.m2.xlarge &mdash; 2 vCPU, 17.1 GiB RAM
					</option>
					<option value="db.m2.2xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m2.2xlarge' ? 'selected="selected"' : '' ); ?> title="db.m2.2xlarge &mdash; 4 vCPU, 34 GiB RAM">db.m2.2xlarge &mdash; 4 vCPU, 34 GiB RAM
					</option>
					<option value="db.m2.4xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m2.4xlarge' ? 'selected="selected"' : '' ); ?> title="db.m2.4xlarge &mdash; 8 vCPU, 68 GiB RAM">db.m2.4xlarge &mdash; 8 vCPU, 68 GiB RAM
					</option>
					<option value="db.cr1.8xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.cr1.8xlarge' ? 'selected="selected"' : '' ); ?> title="db.cr1.8xlarge &mdash; 32 vCPU, 244 GiB RAM">db.cr1.8xlarge &mdash; 32 vCPU, 244 GiB
						RAM
					</option>
					<option value="db.m1.small" <?php echo( $instance['DBInstanceClass'] == 'db.m1.small' ? 'selected="selected"' : '' ); ?> title="db.m1.small &mdash; 1 vCPU, 1.7 GiB RAM">db.m1.small &mdash; 1 vCPU, 1.7 GiB RAM</option>
					<option value="db.m1.medium" <?php echo( $instance['DBInstanceClass'] == 'db.m1.medium' ? 'selected="selected"' : '' ); ?> title="db.m1.medium &mdash; 1 vCPU, 3.75 GiB RAM">db.m1.medium &mdash; 1 vCPU, 3.75 GiB RAM
					</option>
					<option value="db.m1.large" <?php echo( $instance['DBInstanceClass'] == 'db.m1.large' ? 'selected="selected"' : '' ); ?> title="db.m1.large &mdash; 2 vCPU, 7.5 GiB RAM">db.m1.large &mdash; 2 vCPU, 7.5 GiB RAM</option>
					<option value="db.m1.xlarge" <?php echo( $instance['DBInstanceClass'] == 'db.m1.xlarge' ? 'selected="selected"' : '' ); ?> title="db.m1.xlarge &mdash; 4 vCPU, 15 GiB RAM">db.m1.xlarge &mdash; 4 vCPU, 15 GiB RAM</option>
				</select>

				<div style="margin-top: 10px;">
					<p>Please note that making changes to the instance class causes an outage while the change is being executed. Amazon RDS currently supports the following DB Instance Classes:</p>
					<table cellspacing="0" cellpadding="1" border="0" width="1218" height="293" jcr:primarytype="nt:unstructured">
						<tbody>
						<tr>
							<td><b>Instance Type</b></td>
							<td><b>vCPU</b></td>
							<td><b>Memory (GiB)</b></td>
							<td><b>PIOPS-Optimized</b></td>
							<td><b>Network Performance</b></td>
						</tr>
						<tr>
							<td colspan="5"><b>Standard - current generation</b></td>
						</tr>
						<tr>
							<td>db.m3.medium</td>
							<td>1</td>
							<td>3.75</td>
							<td>-</td>
							<td>Moderate</td>
						</tr>
						<tr>
							<td>db.m3.large</td>
							<td>2</td>
							<td>7.5</td>
							<td>-</td>
							<td>Moderate</td>
						</tr>
						<tr>
							<td valign="top">db.m3.xlarge</td>
							<td valign="top">4</td>
							<td valign="top">15</td>
							<td valign="top">Yes</td>
							<td valign="top">Moderate</td>
						</tr>
						<tr>
							<td valign="top">db.m3.2xlarge</td>
							<td valign="top">8</td>
							<td valign="top">30</td>
							<td valign="top">Yes</td>
							<td valign="top">High</td>
						</tr>
						<tr>
							<td colspan="5"><b>Memory optimized - current generation</b></td>
						</tr>
						<tr>
							<td>db.r3.large</td>
							<td>2</td>
							<td>15</td>
							<td>-</td>
							<td>Moderate</td>
						</tr>
						<tr>
							<td>db.r3.xlarge</td>
							<td>4</td>
							<td>30.5</td>
							<td>Yes</td>
							<td>Moderate</td>
						</tr>
						<tr>
							<td>db.r3.2xlarge</td>
							<td>8</td>
							<td>61</td>
							<td>Yes</td>
							<td>High</td>
						</tr>
						<tr>
							<td>db.r3.4xlarge</td>
							<td>16</td>
							<td>122</td>
							<td>Yes</td>
							<td>High</td>
						</tr>
						<tr>
							<td>db.r3.8xlarge</td>
							<td>32</td>
							<td>244</td>
							<td colspan="1">-</td>
							<td>10 Gigabit</td>
						</tr>
						<tr>
							<td><b>Burstable performance instances</b></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>db.t2.micro</td>
							<td>1</td>
							<td>1</td>
							<td>-</td>
							<td>Low to Moderate</td>
						</tr>
						<tr>
							<td>db.t2.small</td>
							<td>1</td>
							<td>2
							</td>
							<td>-</td>
							<td>Low to Moderate</td>
						</tr>
						<tr>
							<td>db.t2.medium</td>
							<td>2</td>
							<td>4</td>
							<td>-</td>
							<td>Low to Moderate</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Allocated Storage</label>

			<div class="controls">
				<input name="options[AllocatedStorage]" value="<?php echo $instance['AllocatedStorage']; ?>"/> GB
				<div style="margin-top: 10px;">
					<p>General Storage: Min 5GB, Max 3072GB<br/></p>

					<p>Provisioned IOPS: Min 100GB, Max 3072GB</p>

					<p>Please note that while you can always increase storage size, there's no easy way to decrease storage size!</p>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Storage Type</label>

			<div class="controls">
				<select name="options[StorageType]">
					<option value="gp2" <?php echo( $instance['StorageType'] == "gp2" ? 'selected="selected"' : '' ); ?>>General Purpose (SSD)</option>
					<option value="io1" <?php echo( $instance['StorageType'] == "io1" ? 'selected="selected"' : '' ); ?>>Provisioned IOPS (SSD)</option>
					<option value="standard" <?php echo( $instance['StorageType'] == "standard" ? 'selected="selected"' : '' ); ?>>Magnetic</option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Multi-AZ</label>

			<div class="controls">
				<select name="options[MultiAZ]">
					<option value="1" <?php echo( $instance['MultiAZ'] ? 'selected="selected"' : '' ); ?>>Enabled</option>
					<option value="0" <?php echo( ! $instance['MultiAZ'] ? 'selected="selected"' : '' ); ?>>Disabled</option>
				</select>
				<?php if ( $instance['MultiAZ'] ) { ?>
					<a id="rebootAZ" class="btn btn-primary btn-mini fldsubmitLicenseU" type="button" href="javascript:void(0);">Reboot Instance to Failover</a>
				<?php } ?>


				<div style="margin-top: 10px;">
					<p>When Multi-ZA is enabled, Amazon RDS automatically creates a primary DB Instance and synchronously replicates the data to a standby instance in a different Availability Zone (AZ). Each AZ runs on its own physically
						distinct,
						independent infrastructure, and is engineered to be highly reliable. In case of an infrastructure failure (for example, instance hardware failure, storage failure, or network disruption), Amazon RDS performs an
						automatic
						failover to
						the standby, so that you can resume database operations as soon as the failover is complete. Since the endpoint for your DB Instance remains the same after a failover, your application can resume database operation
						without the need
						for manual administrative intervention.</p>
				</div>
			</div>
		</div>
		<div class="form-actions">
			<input type="submit" class="btn btn-primary width-auto save-button fldsubmitLicense" value="Save changes"/>
		</div>
		<input type="hidden" name="form" value="rds"/>
		</form>
	<?php } ?>
	<script type="text/javascript">
		$(document).ready(function () {
			$("#reboot, #rebootAZ").click(function () {
				$('[name="form"]').val($(this).attr("id"));
				$(this).closest("form").submit();
			});

			(function poll() {
				setTimeout(function () {
					$("#loading").css("display", "inline-block");
					$.ajax("<?php echo admin_url() ?>?rds_poll", {
						dataType: "json"
					}).success(function (data) {
						var instance_status = $("#status").html();

						if (data.status && data.status.toLowerCase() != instance_status.toLowerCase()) {
							window.location = "<?php echo admin_url(); ?>?rds";
						}
						$("#loading").css("display", "none");
						poll();
					});
				}, 10000);
			})();
		});

	</script>
<?php } ?>