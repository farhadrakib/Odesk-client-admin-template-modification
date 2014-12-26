<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
	<h2><span><a href="<?php echo $this->PLUGIN_URL ?>">Users</a> &gt; Create</span>

		New User</h2>

	<div class="table-holder">
	<br/><br/>

	<?php
	$error     = false;
	$error_msg = "";
	if ( isset( $_POST['pay'] ) && $_POST['pay'] == "true" ) {
		global $wpdb;

		//print_r($_POST);

		$uid = create_user_key( $_POST['email'] );

		//check if user in DB
		if ( user_exists( $uid ) ) {
			$error     = true;
			$error_msg = "User with such email already exists";
		}

		//create package and user record
		if ( ! $error ) {
			//extract post vars
			$package_id    = $_POST['package_id'];
			$extra_domains = intval( $_POST['extradomains'] );
			$pay_interval  = HMTrackerFN::hmtracker_secure( $_POST['pay_interval'] );
			$email         = HMTrackerFN::hmtracker_secure( $_POST["email"] );
			$balance_type  = $_POST['first_payment'];
			if ( $balance_type == 'initial_balance' ) {
				$initial_balance = (float) $_POST['txt_balance'];
			} else if ( $balance_type == 'hours_credit' ) {
				$hours_credit = intval( $_POST['txt_balance'] );
			}

			//fill user data
			$user = array
			(
				'user_key'      => $uid,
				'email'         => $email,
				'password'      => $_POST["pass1"],
				'business_name' => HMTrackerFN::hmtracker_secure( $_POST["bname"] ),
				'website'       => HMTrackerFN::hmtracker_secure( $_POST["site"] ),
				'plans'         => array(),
				'status'        => 0
			);

			//if free user
			if ( isset( $_POST["is_free"] ) && $_POST["is_free"] == "true" ) {
				$user['status'] = 6;
			}

			//create plan for him
			if ( ! isset( $_POST["is_free"] ) || $_POST["is_free"] != "true" ) {
				$plan                       = get_plan_for( $this->PACKAGES[ $package_id ], $package_id );
				$plan['extradomains_count'] = $extra_domains;
				$plan['pay_interval']       = $pay_interval;
				$plan['title'] .= ' (Custom)';
				$user['plans'] = array( $plan );
			}

			//save user
			$user_id = create_user( $user );
			if ( ! $user_id ) {
				$error     = true;
				$error_msg = 'Database Error';
			}

			//add first payment if specified
			if ( ! $error ) {
				$user['id'] = $user_id;
				create_user_tables( $uid );

				if ( isset( $hours_credit ) && $hours_credit != 0 ) {
					$initial_balance = calculate_initial_credit($user, $plan, $hours_credit);
				}

				if ( isset( $initial_balance ) && $initial_balance != 0 ) {
					create_payment($user, $initial_balance, $plan);
				}

			}
		}
	}


	?>

	<?php if ( ! $error && isset( $_POST['pay'] ) && $_POST['pay'] == "true" ): ?>

		<script type="text/javascript">location.href = "<?php echo $this -> PLUGIN_URL ?>"</script>

	<?php endif; ?>

	<?php if ( $error ): ?>

		<div class="alert alert-error">
			<button class="close" data-dismiss="alert">×</button>
			<strong>Error</strong>. <?php echo $error_msg ?>
		</div>

	<?php endif; ?>

	<form id="regform" class="form-horizontal no-padding no-margin" method="post"
	      action="<?php echo admin_url() ?>?extrauser" style="overflow: hidden; clear: both">
		<input type="hidden" name="pay" value="true"/>
		<input type="hidden" id="first_payment" name="first_payment" value="hours_credit"/>


		<div class="control-group">
			<label class="control-label">Business Name</label>

			<div class="controls">
				<input id="bname" name="bname"
				       value="<?php echo ( isset( $_SESSION["temp"]["bname"] ) ) ? $_SESSION["temp"]["bname"] : "" ?>"
				       type="text" required/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Website</label>

			<div class="controls">
				<input id="site" name="site"
				       value="<?php echo ( isset( $_SESSION["temp"]["site"] ) ) ? $_SESSION["temp"]["site"] : "" ?>"
				       type="text"
				       required/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">E-mail</label>

			<div class="controls">
				<input id="email" name="email"
				       value="<?php echo ( isset( $_SESSION["temp"]["email"] ) ) ? $_SESSION["temp"]["email"] : "" ?>"
				       type="text" required email/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Password</label>

			<div class="controls">
				<input id="pass1" name="pass1" type="password" required/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Password Again</label>

			<div class="controls">
				<input id="pass2" name="pass2" type="password" required/>
			</div>
		</div>

		<input id="is_free" name="is_free" type="hidden" value="true"/>

		<?php if($this->PACKAGES !== false && !empty($this->PACKAGES) ) { ?>
		<div class="accordion" id="accordion1" style="height: auto;">
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle collapse in" id="pextradomains_toggle" data-toggle="collapse"
					   data-parent="#accordion1" href="#collapse_1">
						<i class="icon-ok-circle"></i>
						<span>Free User</span>
					</a>
				</div>
				<div id="collapse_1" class="accordion-body collapse off">
					<div class="accordion-inner collapse in" style="padding-left:0px;">


						<div class="control-group">
							<label class="control-label">Base Package</label>

							<div class="controls">
								<select id="package_id" name="package_id">
									<?php
									$i = 0;
									foreach ( $this->PACKAGES as $package_id => $package_data ) {
										$selected = $i == 0 ? 'selected' : '';
										?>
										<option <?php echo $selected; ?>
											value="<?php echo $package_id; ?>"><?php echo $package_data['title']; ?></option>
									<?php } ?>
								</select>
							</div>

						</div>
						<div class="control-group">
							<label class="control-label">Payments Schedule</label>

							<div class="controls">
								<select id="ppay_interval" name="pay_interval" placeholder="Payments Schedule">
									<option id='ppay_weekly' value="weekly">Weekly</option>
									<option id='ppay_biweekly' value="biweekly">Biweekly</option>
									<option id='ppay_monthly' value="monthly">Monthly</option>
									<option id='ppay_annually' value="annually">Annually</option>
								</select>
							</div>
						</div>
						<div id="extrad_container" class="control-group">
							<label class="control-label">Extra Domains</label>

							<div class="controls">
								<input id="pextradomains" name="extradomains" style="width: 68px" min="0" max="1000"
								       step="1" value="0" type="number">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Initial Credit</label>

							<div class="controls">
								<div class="input-append">
									<input id="pbalance" name="txt_balance" style="width: 68px" value="0" type="number"
									       placeholder="Initial Credit" class="tooltips" data-placement="bottom"
									       data-original-title="Initial credit (non-0 to apply)">

									<div class="btn-group" data-toggle="buttons-radio">
										<button type="button" data-mean="initial_balance" data-toggle="button"
										        class="btn btn-primary btn-mini init-balance active">$
										</button>
										<button type="button" data-mean="hours_credit" data-toggle="button"
										        class="btn  btn-primary btn-mini init-balance">Hours
										</button>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary width-auto save-button ">
				Create User
			</button>
		</div>
	</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function () {

			jQuery('#pextradomains_toggle').click(function () {

				var icon = jQuery(this).children('i');
				var wasOk = icon.hasClass('icon-ok-circle');
				icon.removeClass('icon-ok-circle icon-off');
				icon.addClass(wasOk ? 'icon-off' : 'icon-ok-circle');

				var title = jQuery(this).children('span');
				title.text(wasOk ? 'Trial User' : 'Free User');

				if (wasOk) {
					jQuery('#is_free').val('false');
				} else {
					jQuery('#is_free').val('true');
				}

				return true;
			});
		});
	</script>
<?php include( 'mk-footer.php' ); ?>