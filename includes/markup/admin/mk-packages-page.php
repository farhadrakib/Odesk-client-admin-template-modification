<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
	<h2><span>VIEW AND MANAGE PACKAGE LIST</span> Packages</h2>

	<div class="table-holder">
		<a href="<?php echo $this->PLUGIN_URL ?>?newpackage" role="button"
		   class="btn btn-primary btn-mini"> + Create</a>

		<br/><br/>

		<form id="to_del_form" action="" method="post">

			<table border="0" width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped bs-table" id="gcheck">
				<tr>
					<th rowspan="2"><span>Package</span></th>
					<th colspan="5"><span>Domains</span></th>
					<th colspan="5"><span>Extra Domains</span></th>
					<th rowspan="2"><span>Subscribe URL</span></th>
					<th rowspan="2"><span>Free Trial (Days)</span></th>
					<th rowspan="2" class="table-header-options "><span>Options</span></th>
				</tr>
				<tr>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>#</span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Weekly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Biweekly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Monthly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Annually </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span></span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Weekly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Biweekly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Monthly </span></th>
					<th class=" minwidth-1" style="background-color: #f9f9f9;"><span>Annually </span></th>
				</tr>
				<?php
				$i = 0;
				if($this->PACKAGES) {
					foreach ( $this->PACKAGES as $package_id => $package_data ) {

						//build table row
						$bg_style = 'style="';
						$bg_style .= $i ++ % 2 != 0 ? 'background-color: #f9f9f9;' : 'background-color: transparent;';
						$bg_style .= 'text-align:center;"';
						?>
						<tr class="rows <?php echo $package_id; ?>">
							<td <?php echo $bg_style; ?>> <?php echo $package_data['title']; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo $package_data['domains']; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo "{$package_data['weekly']} {$package_data['currency_code']}"; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo "{$package_data['biweekly']} {$package_data['currency_code']}"; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo "{$package_data['monthly']} {$package_data['currency_code']}"; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo "{$package_data['annually']} {$package_data['currency_code']}"; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo $package_data['extradomains_enabled'] ? '<i class="icon-ok"></i>' : ''; ?></td>
							<td <?php echo $bg_style; ?>> <?php echo( $package_data['extradomains_enabled'] ? "{$package_data['extradomain_weekly']} {$package_data['currency_code']}" : '' ); ?></td>
							<td <?php echo $bg_style; ?>> <?php echo( $package_data['extradomains_enabled'] ? "{$package_data['extradomain_biweekly']} {$package_data['currency_code']}" : '' ); ?></td>
							<td <?php echo $bg_style; ?>> <?php echo( $package_data['extradomains_enabled'] ? "{$package_data['extradomain_monthly']} {$package_data['currency_code']}" : '' ); ?></td>
							<td <?php echo $bg_style; ?>> <?php echo( $package_data['extradomains_enabled'] ? "{$package_data['extradomain_annually']} {$package_data['currency_code']}" : '' ); ?></td>
							<td <?php echo $bg_style; ?>>
								<input type="text" value="<?php echo $this->PLUGIN_URL ?>?package=<?php echo $package_id; ?>" style="width: 120px;"/>
							</td>
							<td <?php echo $bg_style; ?>><?php echo isset( $package_data['free_trial'] ) && $package_data['free_trial'] > 0 ? $package_data['free_trial'] : "NO"; ?></td>
							<td  <?php echo $bg_style; ?> class="options-width">
								<a href="<?php echo $this->PLUGIN_URL ?>?editpackage=<?php echo $package_id; ?>">
									<i class="icon-edit"></i> edit
								</a> |
								<a href="#delpackage" data-toggle="modal" class="delpackage" to-del="<?php echo $package_id; ?>">
									<i class="icon-remove"></i> delete
								</a>
							</td>
						</tr> <?php
					}
				}
				?>

			</table>
		</form>

	</div>
	<!-- START MODALS -->
	<div id="delpackage" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
	     aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel1">Delete <span id="delprojtitle"></span></h3>
		</div>
		<div class="modal-body">
			<p>Please confirm deleting this package</p>
		</div>
		<div class="modal-footer">
			<a class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
			<a class="btn btn-danger del-package" data-dismiss="modal" id="delpackageaction" to-del="">Delete</a>
		</div>
	</div>
	<!-- END MODALS -->
	<script>
		jQuery(document).ready(function () {
			//App.init(); // initlayout and core plugins


			//del package
			jQuery('.delpackage').click(function () {
				jQuery('#delpackageaction').attr('to-del', jQuery(this).attr('to-del'));
			})

			//delete package
			jQuery('.del-package').click(function () {

				//post var
				var post = {};
				post.to_del = jQuery(this).attr("to-del");
				post.action = "delpackage";


				//sending
				jQuery.post('<?php echo admin_url() ?>/?hmtrackeractions', post, function (data) {
					jQuery('.save-button').button('reset');
					if (data != "ok") {
						jQuery("#page").prepend(alerter(data, 1));
					} else {
						jQuery('.' + post.to_del).remove();
					}

				});

			})


		});
	</script>
<?php include( 'mk-footer.php' ); ?>