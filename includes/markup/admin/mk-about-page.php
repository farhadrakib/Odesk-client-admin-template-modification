<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
include( "mk-header.php" );
include( "mk-sidebar.php" );
?>
<h2>About Software</h2>

<div class="table-holder">

	<?php

	$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->PLUGIN_PATH ), RecursiveIteratorIterator::SELF_FIRST );
	$flag  = true;
	foreach ( $files as $file ) {
		$file = str_replace( '\\', '/', $file );
		// Ignore "." and ".." folders
		if ( in_array( substr( $file, strrpos( $file, '/' ) + 1 ), array(
				'.',
				'..'
			) ) || strpos( $file, "_backup" ) !== false || ! $flag
		) {
			continue;
		}
		if ( ! is_writable( $file ) ) {
			$flag = false;
		}
	}

	?>
	<?php
	$latest = false;
	if ( isset( $this->OPTIONS['last_info'] ) && is_array( $this->OPTIONS['last_info'] ) && version_compare( $this->OPTIONS['version'], $this->OPTIONS['last_info']['version'], '<' ) ) { ?>
		<div class="space20"></div>
		<div class="stats-overview block clearfix">
			<h3>New Version Available</h3>
			<h4>Version <?php echo $this->OPTIONS['last_info']['version'] ?></h4>
			<ul class="todo-list">
				<?php foreach ( $this->OPTIONS['last_info']['changelog'] as $key => $value ) { ?>
					<li>
						<div class="col1">
							<div class="cont">
													<span class="label label-success"><?php echo $key ?>
														:</span> <?php echo $value ?>
							</div>
						</div>
					</li>
				<?php } ?>

			</ul>

			<?php foreach ( $this->OPTIONS['last_info']['messages'] as $title => $message ) {
				if ( $message != "" ) {
					?>
					<div class="alert alert-success">
						<button class="close" data-dismiss="alert">×</button>
						<strong><?php echo $title ?></strong> <?php echo $message ?>
					</div>
				<?php
				}
			} ?>

			<div class="space10"></div>
			<?php if ( $flag ): ?>
				<a href="<?php echo home_url() ?>?update_start" class="btn btn-primary btn-mini"
				   type="button">Update Automatically</a>
			<?php else: ?>
				<a href="javascript:;" class="btn btn-primary btn-mini" type="button"> Update
					Automatically [Permission Denied]</a>
			<?php endif; ?>
			<a href="<?php echo $this->OPTIONS['last_info']['link'] ?>" target="_blank"
			   class="btn btn-primary btn-mini" role="button"> Download ZIP</a>

			<div class="space20"></div>
		</div>
	<?php } elseif ( isset($_GET['check_for_update']) && isset( $this->OPTIONS['last_info'] ) && is_array( $this->OPTIONS['last_info'] ) && version_compare( $this->OPTIONS['version'], $this->OPTIONS['last_info']['version'], '=' ) ) {
		$latest = true;
		echo "<h4 style='color: #00bd00;'>You have the latest version of Heatmap Tracker installed.</h4>";
	} ?>
	<h3><?php echo $this->OPTIONS['software'] ?></h3>
	<h4>Version <?php echo $this->OPTIONS['version'] ?>
		<?php if(!$latest) { ?><a href="<?php echo home_url() ?>?about=&check_for_update" class="btn btn-primary btn-mini" role="button"> <i class="icon-refresh"></i> Check for Updates</a><?php } ?>
	</h4>


	<!-- BEGIN CHANGELOG-->
	<div class="widget">
		<div class="widget-title">
			<h4><i class="icon-reorder"></i>Changelog</h4>
		</div>
		<div class="widget-body">

			<div class="accordion in collapse" id="accordion1" style="height: auto;">
				<?php $htmlid = 0;
				foreach ( $this->OPTIONS['changelog'] as $version => $changelog ) {
					$htmlid ++; ?>
					<div class="accordion-group">
						<div class="accordion-heading">
							<a class="accordion-toggle collapsed" data-toggle="collapse"
							   data-parent="#accordion<?php echo $htmlid ?>"
							   href="#collapse_<?php echo $htmlid ?>">
								<i class=" icon-ok"></i>
								Version <?php echo $version ?>
							</a>
						</div>
						<div id="collapse_<?php echo $htmlid ?>" class="accordion-body collapse"
						     style="height: 0px;">
							<div class="accordion-inner">

								<ul class="todo-list">
									<?php foreach ( $changelog as $which => $log ) { ?>
										<li>
											<div class="col1">
												<div class="cont">
													<span class="label label-success"><?php echo $which ?>:</span>
													<?php echo $log ?>
												</div>
											</div>
										</li>
									<?php } ?>
								</ul>

							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
<?php include('mk-footer.php'); ?>