<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
	<h2><span><a href="<?php echo $this->PLUGIN_URL ?>">Users</a> &gt; Edit</span> Edit User</h2>

	<div class="table-holder">
		<br/><br/>
		<?php
		if ( isset( $_POST['save'] ) ) {
			global $wpdb;
			$table_users = T_PREFIX . $this->OPTIONS["dbtable_name_users"];

			$q = "UPDATE " . $table_users . "
					SET `email` = '" . $_POST["email"] . "',
						`business_name` = '" . $_POST["bname"] . "',
						`website` = '" . $_POST["site"] . "'";

			if(!empty($_POST['status'])) {
				switch($_POST['status']) {
					case "none":
						$user = get_user_by( 'id', $_GET['id'] );
						$package = get_active_plan($user);
//						var_dump($package);
//						var_dump(is_free($package));
						if($package && is_plan_free($package)) {
							$status = 7;
						} elseif(!$package) {
							$status = 6;
						} else {
							$status = 1;
						}
						break;
					case "active":
						$status = 8;
						break;
					case "suspend":
						$status = 9;
						break;
				}
				$q .= ", `status` = '" . $status . "'";
			}
			if ( !empty( $_POST['pass1'] ) ) {
				$q .= ", `password` = '" . md5( sha1( $_POST["pass1"] ) ) . "'";
			}
			$q .= " WHERE `id` = " . $_POST["uid"] . ";";

//			print_r($_POST);
//			echo $q;
//			die();
			$res = $wpdb->query( $q );

			if ( ! $res ) {
				?>
				<div class="form-horizontal">
					<div class="control-group">
						<label class="control-label"></label>

						<div class="controls">
							<strong style="color:#f00">Database Error</strong>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="form-horizontal">
					<div class="control-group">
						<label class="control-label"></label>

						<div class="controls">
							<strong style="color:#0f0">User Info has been saved</strong><br/>
						</div>
					</div>
				</div>
			<?php
			}
		}
		$user = get_user_by( 'id', $_GET['id'] );
		$user->status = detect_user_status($user);
		?>
		<form id="regform" class="form-horizontal no-padding no-margin" method="post"
		      action="<?php echo admin_url() ?>?edituser&id=<?php echo $user->id ?>"
		      style="overflow: hidden; clear: both">
			<input type="hidden" name="save" value="info"/>
			<input type="hidden" name="uid" value="<?php echo $user->id ?>"/>

			<div class="control-group">
				<label class="control-label">Business Name</label>

				<div class="controls">
					<input id="bname" name="bname" value="<?php echo $user->business_name ?>" type="text" required/>
				</div>
			</div>

			<div class="control-group">

				<label class="control-label">Status Override</label>

				<div class="controls">
					<div class="btn-group all-special btn-heatmap" data-toggle="buttons-radio">
						<button type="button" class="btn btn-success btn-mini btn-primary btn-h-click<?php echo ($user->status != 8 && $user->status !=9 ? ' active' : ''); ?>" data-value="none">
							None
						</button>
						<button type="button" class="btn btn-primary btn-mini btn-h-move<?php echo ($user->status == 8 ? ' active' : ''); ?>" data-value="active">
							Active
						</button>
						<button type="button" class="btn btn-primary btn-mini btn-h-scroll<?php echo ($user->status == 9 ? ' active' : ''); ?>" data-value="suspend">
							Suspend
						</button>
					</div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Website</label>

				<div class="controls">
					<input id="site" name="site" value="<?php echo $user->website ?>" type="text" required/>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">E-mail</label>

				<div class="controls">
					<input id="email" name="email" value="<?php echo $user->email ?>" type="text" required email/>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">New Password</label>

				<div class="controls">
					<input id="pass1" name="pass1" type="password"/>
				</div>
			</div>

			<div class="form-actions">
				<button type="button" class="btn btn-primary width-auto save-button ">
					Save User
				</button>
			</div>
		</form>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$(".save-button").click(function () {
				var form =$(this).closest("form");
				var layout = $(".btn-heatmap").find(".active").attr("data-value");
				$("<input />").attr("type", "hidden").attr("name", "status").attr("value", layout).appendTo(form);
				form.submit();
			});
		});
	</script>
<?php include( 'mk-footer.php' ); ?>