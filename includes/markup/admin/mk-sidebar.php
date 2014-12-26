<div id="sidebar">
	<ul class="sidenav">
		<li><a<?php echo( empty( $_GET ) || isset( $_GET['edituser'] ) || isset( $_GET['extrauser'] ) ? ' class="active"' : '' ); ?> href="<?php echo $this->PLUGIN_URL ?>"><i class="icon-group"></i> Users</a></li>
		<li><a<?php echo( isset( $_GET['payments'] ) ? ' class="active"' : '' ); ?> href="<?php echo $this->PLUGIN_URL ?>?payments"><i class="icon-money"></i> Payments</a></li>
		<li><a<?php echo( isset( $_GET['packages'] ) || isset( $_GET['editpackage'] ) || isset( $_GET['newpackage'] ) ? ' class="active"' : '' ); ?> href="<?php echo $this->PLUGIN_URL ?>?packages"><i class="icon-briefcase"></i> Package
				Manager</a></li>
		<li><a<?php echo( isset( $_GET['devhelpvideos'] ) ? ' class="active"' : '' ); ?> href="<?php echo admin_url() ?>?devhelpvideos"><i class="icon-facetime-video"></i> Help Videos</a></li>
		<li><a<?php echo( isset( $_GET['brandsupport'] ) ? ' class="active"' : '' ); ?> href="<?php echo $this->OPTIONS['brandsupport'] ?>"><i class=" icon-comments-alt"></i> Support</a></li>
		<?php if ( isset( $this->OPTIONS['iam_key'] ) && $this->OPTIONS['iam_key'] != "" ) { ?>
			<li><a<?php echo( isset( $_GET['rds'] ) ? ' class="active"' : '' ); ?> href="<?php echo admin_url() ?>?rds"><i class="icon-fire"></i> AWS RDS Settings</a></li>
		<?php } ?>
		<li><a<?php echo( isset( $_GET['adminsettings'] ) ? ' class="active"' : '' ); ?> href="<?php echo admin_url() ?>?adminsettings"><i class=" icon-wrench"></i> Admin Settings</a></li>
		<li><a<?php echo( isset( $_GET['about'] ) ? ' class="active"' : '' ); ?> href="<?php echo admin_url() ?>?about"><i class=" icon-info-sign"></i> About This
				Software</a></li>
	</ul>
</div>
<div id="content">
	<div class="analytics-block">
