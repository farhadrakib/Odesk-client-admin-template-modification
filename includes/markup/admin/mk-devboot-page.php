<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include("mk-header.php");
include( "mk-sidebar.php" );
?>
					<h2><span>VIEW AND MANAGE USER LIST</span> Users</h2>

					<div class="table-holder">
						<a href="<?php echo $this->PLUGIN_URL ?>?extrauser" role="button"
						   class="btn btn-primary btn-mini"> + Create</a>
						<label class="pull-right">
							<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="perpage">
								<input type="hidden" name="order_by"
								       value="<?php echo (isset($_GET["order_by"])) ? $_GET["order_by"] : "session_start" ?>"/>
								<input type="hidden" name="s"
								       value="<?php echo (isset($_GET["s"])) ? $_GET["s"] : "" ?>"/>
								per page <select size="1" name="perpage" class="input-small perpage"
								                 style="margin-bottom: 0">
									<?php
									for ($i = 10; $i < 510; $i += 10) {
										?>
										<option
											value="<?php echo $i ?>" <?php if ((isset($_GET['perpage']) && $i == $_GET['perpage']) || (!isset($_GET['perpage']) && $i == 30)): ?> selected="selected"<?php endif; ?>><?php echo $i ?></option>
									<?php } ?>
								</select>
							</form>
						</label>

						<br/><br/>

						<form id="to_del_form"
						      action="<?php echo admin_url() . '?analytics=' . $_GET["analytics"] . (isset($_GET['perpage']) ? '&perpage=' . $_GET['perpage'] : '') . (isset($_GET['paged']) ? '&paged=' . $_GET['paged'] : '') ?>"
						      method="post">

							<table border="0" width="100%" cellpadding="0" cellspacing="0"
							       class="table table-bordered table-striped bs-table" id="gcheck">
								<tr>
									<th class=" minwidth-1"><span>Email</span></th>
									<th class=" minwidth-1"><span>Business Name</span></th>
									<th class=" minwidth-1"><span>Website</span></th>
									<th class=" minwidth-1"><span>Package</span></th>
									<th class=" minwidth-1"><span>Status</span></th>
									<th class="table-header-options "><span>Options</span></th>
								</tr>
								<?php

								global $wpdb;
								//delete
								if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_REQUEST['custom']) && $_REQUEST['custom'] != "") {

									global $wpdb;
									$table    = T_PREFIX . $this->OPTIONS['dbtable_name_users'];
									$entry_id = (is_array($_REQUEST['user'])) ? $_REQUEST['user'] : array($_REQUEST['user']);
									foreach ($entry_id as $id) {

										$table          = T_PREFIX . 'main_' . $_REQUEST['custom'];
										$table_click    = T_PREFIX . 'clicks_' . $_REQUEST['custom'];
										$table_mmove    = T_PREFIX . 'mmove_' . $_REQUEST['custom'];
										$table_scroll   = T_PREFIX . 'scroll_' . $_REQUEST['custom'];
										$table_ppopular = T_PREFIX . 'popular_' . $_REQUEST['custom'];

										$structure1 = "DROP TABLE IF EXISTS $table";
										$structure2 = "DROP TABLE IF EXISTS $table_click";
										$structure3 = "DROP TABLE IF EXISTS $table_mmove";
										$structure4 = "DROP TABLE IF EXISTS $table_scroll";
										$structure5 = "DROP TABLE IF EXISTS $table_ppopular";

										$wpdb->query($structure1);
										$wpdb->query($structure2);
										$wpdb->query($structure3);
										$wpdb->query($structure4);
										$wpdb->query($structure5);

										global $HMTrackerPro_USERSTATUS_NAME;
										delete_option($this->PROJECTS_NAME . $_REQUEST['custom']);
										delete_option($this->USER_DOMAINS_NAME . $_REQUEST['custom']);
										delete_option($HMTrackerPro_USERSTATUS_NAME . $id);

										$wpdb->query("DELETE FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_users'] . "` WHERE id = $id");
									}
								}

								$search = (isset($_GET['s']) && $_GET['s'] != "") ? " WHERE `email` like '%" . $_GET['s'] . "%' OR `business_name` like '%" . $_GET['s'] . "%'" : "";

								$q = "SELECT * FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_users'] . "` " . $search;


								//pagination
								$perpage = (isset($_GET['perpage'])) ? $_GET['perpage'] : 30;

								$totalitems = $wpdb->get_var("SELECT COUNT(*) FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_users'] . "` " . $search);
								$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';


								if (!empty($paged) && !empty($perpage)) {
									$offset = $paged;
									$q .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
								} else {
									$q .= ' LIMIT ' . (int)$perpage;
								}

								$r = $wpdb->query($q);


								$nr = $wpdb->numRows($r);


								$counter = 0;
								if ($nr > 0) {
									require_once($this->FUNCTIONS_PATH . 'balance.php');
									$calc = new BalanceCalculator();

									while ($a = mysql_fetch_assoc($r)) {
										$plans = unserialize($a["plans"]); //array of plans
										$calc->SetPlans($plans);
										$plan = $calc->GetActivePlan();
										//build table row	
										?>
										<tr class="rows <?php echo $a["id"]; ?>">
											<td> <?php echo $a["email"]; ?></td>
											<td> <?php echo $a["business_name"]; ?></td>
											<td> <?php echo $a["website"]; ?></td>
											<td> <?php echo ($a['status'] == 6) ? "Free" : $plan['title']; ?> </td>
											<td> <?php echo ucwords(user_status_name($a['status'])); ?> </td>

											<td class="options-width">
												<a href="<?php echo admin_url() ?>?edituser&id=<?php echo $a["id"]; ?>" class="edituser">
													<i class="icon-edit"></i> edit
												</a> |
												<a href="#deluser" data-toggle="modal" class="deluser" to-del="<?php echo admin_url() ?>?users=&action=delete&user=<?php echo $a["id"]; ?>&custom=<?php echo $a["user_key"]; ?>">
													<i class="icon-remove"></i> delete
												</a> |
												<a href="<?php echo admin_url() ?>?client_login=<?php echo $a["id"]; ?>" class="edituser">
													<i class="icon-user"></i> login
												</a>
											</td>
										</tr>
										<?php $counter++;
									}
								}
								?>

							</table>

							<?php
							echo pnp_pagination($totalitems, $perpage, 5, $paged, admin_url() . '?users=' . (isset($_GET['perpage']) ? '&perpage=' . $_GET['perpage'] : '') . (isset($_GET['order_by']) ? '&order_by=' . $_GET['order_by'] : '') . (isset($_GET['s']) ? '&s=' . $_GET['s'] : ''));
							?>
						</form>

					</div>
<!-- BEGIN MODALS -->
<div id="deluser" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
     aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel1">Delete <span id="delprojtitle"></span></h3>
	</div>
	<div class="modal-body">
		<p>Please confirm deleting this user. All data will be removed including user options and database tables</p>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
		<a class="btn btn-danger del-user" id="deluseraction" to-del="" href="">Delete</a>
	</div>
</div>
<!-- END MODALS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>

<script>
	jQuery(document).ready(function () {
		<?php if(isset($this->OPTIONS['last_info']) && is_array($this->OPTIONS['last_info']) && version_compare($this->OPTIONS['version'], $this->OPTIONS['last_info']['version'], '<')): ?>
		jQuery.gritter.add({
			title: 'New Version Available!',
			text: '<u><a href="<?php echo $this -> PLUGIN_URL ?>?about" style="color:#ccc">About new version</a></u>'
		});
		<?php endif; ?>

		<?php if (version_compare(PHP_VERSION, "5.3", "<")):  ?>
		jQuery.gritter.add({
			title: 'IMPORTANT',
			text: 'You are using PHP v.<?php echo PHP_VERSION ?> but for the stable work of Agency license PHP v5.3+ requered. Please upgrade your PHP'
		});
		<?php endif; ?>


		jQuery('.perpage').change(function () {
			jQuery(this).parent().submit();
		});

		//del package
		jQuery('.deluser').click(function () {
			jQuery('#deluseraction').attr('href', jQuery(this).attr('to-del'));
		})
	});
</script>
<?php include("mk-footer.php"); ?>