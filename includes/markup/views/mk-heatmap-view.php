<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 2014/06/30
 * Time: 10:37 AM
 */
$u = parse_url($url);
$h = parse_url($home_url);
$h = "{$u['scheme']}://{$h['host']}{$h['path']}";
?>
<div class="navbar navbar-fixed-top" style="z-index: 20">
	<div class="navbar-inner">
		<div class="container" style="margin-left: 10px">
			<a class="brand" href="javascript: window.close()" style="padding-top: 0; padding-bottom: 0">
				<img src="<?php echo $brandLogo; ?>" alt="logo" style="max-height: 40px;" class="center"/>
			</a>
			<ul class="nav">
				<?php if ($count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;" id="ie_message"><?php echo $count; ?> points analyzed</li>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">From: <?php echo $from ?> To: <?php echo $to ?></li>
					<?php if ($map == 'scroll') { ?>
						<li class="divider-vertical"></li>
						<li style="margin: 10px 0 0;">Scroll grid step: &nbsp;&nbsp;</li>
						<li style="margin: 10px 0 0;">
							<input id="grd_step" style=" height: 23px; margin: 0; padding: 0;"
							       class="span1 opt_record_interval" min="50" max="500"
							       step="10" value="<?php echo $grid_step; ?>" type="number">
						</li>
					<?php } ?>
				<?php elseif ($count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;"><?php echo $count ?> points analysys</li>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">Please, wait..</li>
				<?php
				elseif (!$count > 0): ?>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;"><strong style="color:#f00">No tracking data for the selected time period</strong></li>
					<li class="divider-vertical"></li>
					<li style="margin: 10px 0 0;">For the period From: <?php echo $from; ?>
						To: <?php echo $to; ?></li>
				<?php endif; ?>
			</ul>
			<?php if ($count > 0) { ?>
				<div id="loader" style="margin-top: 10px;"><span id="loader-text">Loading webpage:</span>&nbsp;&nbsp;<img src="<?php echo $home_url; ?>/images/loader.gif"/></div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="heat-holder spy-frame" style="margin-top: 41px; position: relative; z-index: 10">
	<?php if ($count > 0): ?>
		<div id="heatmapArea" class="spy-frame"
		     style="position: absolute !important; z-index:9999 !important; top: 0px; left: 0; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"></div>
		<iframe id="spy-iframe" class="spy-frame"
		        src="<?php echo $h . '?heatmap_frame=&url=' . urlencode($url) . '&height=' . $height; ?>"
		        name="spy-frame" frameborder="0" noresize="noresize" scrolling="no"
		        style="width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;"></iframe>
	<?php endif; ?>
</div>