<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
	<h2><span>VIEW AND MANAGE PAYMENTS LIST</span> Payments</h2>

	<div class="table-holder">

		<label class="pull-left">
			<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="perpage">
				<input type="hidden" name="payments" value=""/>
				<input type="hidden" name="order_by"
				       value="<?php echo ( isset( $_GET["order_by"] ) ) ? $_GET["order_by"] : "session_start" ?>"/>
				<input type="hidden" name="s"
				       value="<?php echo ( isset( $_GET["s"] ) ) ? $_GET["s"] : "" ?>"/>
				per page <select size="1" name="perpage" class="input-small perpage"
				                 style="margin-bottom: 0">
					<?php
					for ( $i = 10; $i < 510; $i += 10 ) {
						?>
						<option
							value="<?php echo $i ?>" <?php if ( ( isset( $_GET['perpage'] ) && $i == $_GET['perpage'] ) || ( ! isset( $_GET['perpage'] ) && $i == 30 ) ): ?> selected="selected"<?php endif; ?>><?php echo $i ?></option>
					<?php } ?>
				</select>
			</form>
		</label>
		<label class="pull-left">&nbsp;&rsaquo;&nbsp;</label>
		<label class="pull-left">
			<form method="get" action="<?php echo $this->PLUGIN_URL ?>" id="orderby">
				<input type="hidden" name="payments" value=""/>
				<input type="hidden" name="perpage"
				       value="<?php echo ( isset( $_GET["perpage"] ) ) ? $_GET["perpage"] : "10" ?>"/>
				<input type='text' value="<?php echo ( isset( $_GET["s"] ) ) ? $_GET["s"] : "" ?>"
				       style="margin: 0; height: 12px;" name="s">
				<button type="submit" class="btn btn-mini btn-primary width-auto "> search</button>
			</form>
		</label>


		<form id="to_del_form"
		      action="<?php echo admin_url() . '?payments=' . ( isset( $_GET['perpage'] ) ? '&perpage=' . $_GET['perpage'] : '' ) . ( isset( $_GET['paged'] ) ? '&paged=' . $_GET['paged'] : '' ) ?>"
		      method="post">

			<table border="0" width="100%" cellpadding="0" cellspacing="0"
			       class="table table-bordered table-striped bs-table" id="gcheck">
				<tr>
					<th class=" minwidth-1"><span>Id</span></th>
					<th class=" minwidth-1"><span>Amount</span></th>
					<th class=" minwidth-1"><span>Status</span></th>
					<th class=" minwidth-1"><span>Type</span></th>
					<th class=" minwidth-1"><span>Date</span></th>
					<th class=" minwidth-1"><span>User</span></th>
				</tr>
				<?php

				global $wpdb;
				//delete
				if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) {

					global $wpdb;
					$table    = T_PREFIX . $this->OPTIONS['dbtable_name_payments'];
					$entry_id = ( is_array( $_REQUEST['user'] ) ) ? $_REQUEST['user'] : array( $_REQUEST['user'] );
					foreach ( $entry_id as $id ) {

						$wpdb->query( "DELETE FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_payments'] . "` WHERE id = $id" );
					}
				}

				$search = ( isset( $_GET['s'] ) && $_GET['s'] != "" ) ? " WHERE `user` like '%" . $_GET['s'] . "%' OR `payment_amount` like '%" . $_GET['s'] . "%'" : "";

				$q = "SELECT * FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_payments'] . "` " . $search . " ORDER BY createdtime DESC ";


				//pagination
				$perpage = ( isset( $_GET['perpage'] ) ) ? $_GET['perpage'] : 30;

				$totalitems = $wpdb->get_var( "SELECT COUNT(*) FROM `" . T_PREFIX . $this->OPTIONS['dbtable_name_payments'] . "` " . $search );
				$paged      = ! empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';


				if ( ! empty( $paged ) && ! empty( $perpage ) ) {
					$offset = $paged;
					$q .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
				} else {
					$q .= ' LIMIT ' . (int) $perpage;
				}

				$r = $wpdb->query( $q );


				$nr = $wpdb->numRows( $r );


				$counter = 0;
				if ( $nr > 0 ) {
					while ( $a = mysql_fetch_assoc( $r ) ) {


						$table_users = T_PREFIX . $this->OPTIONS['dbtable_name_users'];
						$user        = $wpdb->get_row( "SELECT `email` FROM $table_users WHERE `id` = '" . $a["user_id"] . "'" );

						//build table row
						?>
						<tr class="rows <?php echo $a["id"]; ?>">
							<td> <?php echo $a["txnid"]; ?></td>
							<td> <?php echo $a["payment_amount"]; ?></td>
							<td> <?php echo $a["payment_status"]; ?></td>
							<td> <?php echo $a["txn_type"]; ?></td>
							<td> <?php echo $a["createdtime"]; ?></td>
							<td> <?php echo $user->email; ?></td>
						</tr>
						<?php $counter ++;
					}
				}
				?>

			</table>

			<?php
			echo pnp_pagination( $totalitems, $perpage, 5, $paged, admin_url() . '?payments=' . ( isset( $_GET['perpage'] ) ? '&perpage=' . $_GET['perpage'] : '' ) . ( isset( $_GET['order_by'] ) ? '&order_by=' . $_GET['order_by'] : '' ) . ( isset( $_GET['s'] ) ? '&s=' . $_GET['s'] : '' ) );
			?>
		</form>


	</div>
	<!-- START MODALS -->
	<div id="deluser" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
	     aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel1">Delete <span id="delprojtitle"></span></h3>
		</div>
		<div class="modal-body">
			<p>Please confirm deleting this payment.</p>
		</div>
		<div class="modal-footer">
			<a class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
			<a class="btn btn-danger del-user" id="deluseraction" to-del="" href="">Delete</a>
		</div>
	</div>
	<script>
		jQuery(document).ready(function () {

			jQuery('.perpage').change(function () {
				jQuery(this).parent().submit();
			});

			//del package
			jQuery('.deluser').click(function () {
				jQuery('#deluseraction').attr('href', jQuery(this).attr('to-del'));
			})


		});
	</script>
<?php include( 'mk-footer.php' ); ?>