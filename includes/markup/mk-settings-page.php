<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly'); ?>
<?php
global $loggedin_user;
if (!is_user_logged_in( $loggedin_user ) && IS_KEY_VALID) header('location: ' . admin_url() . '?login'); ?>
<?php $_GET["settings"] = rawurlencode($_GET["settings"]);
$user = current_user();
$option = $this->PROJECTS[$_GET["settings"]]['settings'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
	<title><?php echo $this->OPTIONS['brandname'] ?></title>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->PLUGIN_URL ?>css/datepicker.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->PLUGIN_URL ?>css/style.css"/>
	<link href="<?php echo $this->PLUGIN_URL ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"/>
</head>
<body>
<div id="wrapper">
<div id="header">
	<strong class="logo"><a href="<?php echo $this->PLUGIN_URL ?>"><img
				src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/></a></strong>
	<ul id="nav">
		<?php if ( isset( $_SESSION['return_to_admin'] ) && $_SESSION['return_to_admin'] ) { ?>
			<li><a href="<?php echo $this->PLUGIN_URL ?>?return_admin">Return to Admin</a></li>
		<?php } ?>
		<li><a href="<?php echo $this->PLUGIN_URL ?>">Projects</a></li>
		<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank">Support</a></li>
		<li><a href="<?php echo $this->PLUGIN_URL ?>?logout">Log Out</a></li>
	</ul>
</div>
<div id="main">
<div class="container">
<div class="headbar">
	<em class="date"><?php echo date("F d, Y") ?></em>
</div>
<div id="sidebar">
	<ul class="sidenav">
		<li><a href="<?php echo $this->PLUGIN_URL ?>?project=<?php echo $_GET['settings'] ?>"><i
					class="icon-th-list"></i> Dashboard</a></li>
		<li><a href="<?php echo $this->PLUGIN_URL ?>?analytics=<?php echo $_GET['settings'] ?>"><i
					class="icon-bar-chart"></i> User Sessions</a></li>
		<li><a href="<?php echo $this->PLUGIN_URL ?>?hmaps=<?php echo $_GET['settings'] ?>"><i
					class="icon-dashboard"></i> Heat Maps</a></li>
		<li><a href="<?php echo $this->PLUGIN_URL ?>?ppages=<?php echo $_GET['settings'] ?>"><i
					class="icon-star"></i> Popular Pages</a></li>
		<li><a href="<?php echo $this->PLUGIN_URL ?>?mdata=<?php echo $_GET['settings'] ?>"><i
					class="icon-hdd"></i> Manage Data</a></li>
		<li><a class="active"
		       href="<?php echo $this->PLUGIN_URL ?>?settings=<?php echo $_GET['settings'] ?>"><i
					class="icon-cogs"></i> Settings</a></li>
		<li><a href="<?php echo admin_url() ?>?helpvideos"><i class="icon-facetime-video"></i> Help
				Videos</a></li>
		<li><a href="<?php echo $this->OPTIONS['brandsupport'] ?>" target="_blank"><i
					class=" icon-comments-alt"></i> Support</a></li>
	</ul>
</div>
<div id="content">
<div class="analytics-block">
<h2><span>Adjust Your Project</span> Settings</h2>

<div class="table-holder">
<form action="#" method="POST" class="form-horizontal" id="settings_form">
<div class="control-group">
	<label class="control-label">Enable record</label>

	<div class="controls">
		<div class="btn-group onoff opt_record_status" data-toggle="buttons-radio">
			<button type="button"
			        class="btn btn-mini  btn-on <?php print(($option["opt_record_status"]) ? 'active btn-success' : ''); ?> btn-small"
			        data-value="1">
				Enabled
			</button>
			<button type="button"
			        class="btn btn-mini btn-off <?php print((!$option["opt_record_status"]) ? 'active btn-danger' : ''); ?> btn-small"
			        data-value="0">
				Disabled
			</button>
		</div>
		<a class="help-ico" data-trigger="hover" rel="popover"
		   data-original-title="Enable record"
		   data-content="Enable HeatMapTracker to track info from your pages">lnk</a>
	</div>
</div>
<div class="control-group">
	<label class="control-label">Record actions</label>

	<div class="controls">
		<div class="btn-group all-special" data-toggle="buttons-radio">
			<button type="button"
			        class="opt_record_all btn-mini btn btn-small <?php print(($option["opt_record_all"] == "true") ? 'active btn-success' : ''); ?> btn-all"
			        data-value="1">
				On all pages
			</button>
			<button type="button"
			        class="btn btn-mini btn-small <?php print(($option["opt_record_all"] == "false") ? 'active btn-success' : ''); ?> btn-special"
			        data-value="0">
				On special page
			</button>
		</div>
		<a class="help-ico" data-trigger="hover" rel="popover"
		   data-original-title="Record actions"
		   data-content="Specify page, where you want to track actions. Use CTRL or SHIFT to select multiple pages">lnk</a>
	</div>
	<br/>

	<div class="controls">
		<select <?php print(($option["opt_record_all"] == "true") ? 'disabled' : ''); ?>
			class="input-xlarge pagesposts opt_record_special" multiple="multiple" size="10"
			style="width:600px !important">
			<?php
			global $wpdb, $loggedin_user;
			$table3 = T_PREFIX . 'mmove_' . $loggedin_user[2];
			$urls = $wpdb->get_results(
				"
										SELECT DISTINCT `page_url`
										FROM $table3  where `project` = '" . $_GET["settings"] . "'
										"
			);
			$urlArray2 = array();
			foreach ($urls as $key2 => $value2) {
				$urlArray2[] = $value2->page_url;
			}
			$pages = $urlArray2;
			foreach ($pages as $page => $page_url) {
				$checked = (in_array($page_url, $option['opt_record_special'])) ? 'selected="selected"' : '';
				$optn    = '<option value="' . $page_url . '"  ' . $checked . ' >';
				$optn .= $page_url;
				$optn .= '</option>';
				echo $optn;
			}
			?>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="control-label">Ignore Query String</label>

	<div class="controls">
		<div class="btn-group onoff opt_ignore_query" data-toggle="buttons-radio">
			<button type="button"
			        class="btn btn-on btn-mini <?php print(($option["opt_ignore_query"]) ? 'active btn-success' : ''); ?>  btn-small"
			        data-value="1">
				Yes
			</button>
			<button type="button"
			        class="btn btn-off btn-mini <?php print((!$option["opt_ignore_query"]) ? 'active btn-danger' : ''); ?> btn-small"
			        data-value="0">
				No
			</button>
		</div>
		<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Ignore Query String"
		   data-content="Ignores the query string from the url sent by the tracking code">lnk</a>
	</div>
</div>

<div class="control-group">
	<label class="control-label">Stop Track IP</label>

	<div class="controls">
		<input id="black_ips" type="text" class="input-medium" value=""/> <a type="button"
		                                                                     class="btn btn-primary btn-mini add_ip">Add</a>

		<div id="ips_cont" style="width:180px;">
			<?php foreach ($option["opt_black_ips"] as $key => $value) if ($key != "0") { ?>
				<div class="label label-warning"
				     style="font-size: 14px; margin-top: 3px; padding: 4px;"><?php echo $key; ?>
					<button class="close del_ip" type="button" style="float: none;"
					        to-del="<?php echo $key; ?>">×
					</button>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<div class="control-group">
	<label class="control-label"> Timezone </label>

	<div class="controls">
		<select class="input-large pagesposts opt_record_tz">
			<option value="-1">Choose Timezone</option>
			<?php
			$zonelist = array('Kwajalein' => -12.00, 'Pacific/Midway' => -11.00, 'Pacific/Honolulu' => -10.00, 'America/Anchorage' => -9.00, 'America/Los_Angeles' => -8.00, 'America/Denver' => -7.00, 'America/Tegucigalpa' => -6.00, 'America/New_York' => -5.00, 'America/Caracas' => -4.30, 'America/Halifax' => -4.00, 'America/St_Johns' => -3.30, 'America/Argentina/Buenos_Aires' => -3.00, 'America/Sao_Paulo' => -3.00, 'Atlantic/South_Georgia' => -2.00, 'Atlantic/Azores' => -1.00, 'Europe/Dublin' => 0, 'Europe/Belgrade' => 1.00, 'Europe/Minsk' => 2.00, 'Asia/Kuwait' => 3.00, 'Asia/Tehran' => 3.30, 'Asia/Muscat' => 4.00, 'Asia/Yekaterinburg' => 5.00, 'Asia/Kolkata' => 5.30, 'Asia/Katmandu' => 5.45, 'Asia/Dhaka' => 6.00, 'Asia/Rangoon' => 6.30, 'Asia/Krasnoyarsk' => 7.00, 'Asia/Brunei' => 8.00, 'Asia/Seoul' => 9.00, 'Australia/Darwin' => 9.30, 'Australia/Canberra' => 10.00, 'Asia/Magadan' => 11.00, 'Pacific/Fiji' => 12.00, 'Pacific/Tongatapu' => 13.00);
			foreach ($zonelist as $zkey => $zvalue) {
				$checked = ($option['opt_record_tz'] == $zkey) ? 'selected="selected"' : '';
				$optn    = '<option value="' . $zkey . '"  ' . $checked . ' >';
				$optn .= $zkey . " (" . $zvalue . ")";
				$optn .= '</option>';
				echo $optn;
			}
			?>
		</select>
	</div>
</div>
<div class="control-group">
	<label class="control-label"> Record mouse movement</label>

	<div class="controls">
		<div class="btn-group onoff opt_record_mousemove" data-toggle="buttons-radio">
			<button type="button"
			        class="btn btn-on btn-mini <?php print(($option["opt_record_mousemove"]) ? 'active btn-success' : ''); ?>  btn-small"
			        data-value="1">
				On
			</button>
			<button type="button"
			        class="btn btn-off btn-mini <?php print((!$option["opt_record_mousemove"]) ? 'active btn-danger' : ''); ?> btn-small"
			        data-value="0">
				Off
			</button>
		</div>
		<a class="help-ico" data-trigger="hover" rel="popover"
		   data-original-title="Record mouse movement"
		   data-content="record all mouse coordinates by mousemove event">lnk</a>
	</div>
</div>

<div class="control-group">
	<label class="control-label"> Record page scroll</label>

	<div class="controls">
		<div class="btn-group onoff opt_record_pagescroll" data-toggle="buttons-radio">
			<button type="button"
			        class="btn btn-on btn-mini <?php print(($option["opt_record_pagescroll"]) ? 'active btn-success' : ''); ?> btn-small"
			        data-value="1">
				On
			</button>
			<button type="button"
			        class="btn btn-off btn-mini <?php print((!$option["opt_record_pagescroll"]) ? 'active btn-danger' : ''); ?> btn-small"
			        data-value="0">
				Off
			</button>
		</div>
		<a class="help-ico" data-trigger="hover" rel="popover"
		   data-original-title="Record page scroll"
		   data-content="Record page scroll changes">lnk</a>
	</div>
</div>

<div class="control-group">
	<label class="control-label">Send interval</label>

	<div class="controls">
		<div class="input-append input-mini">
			<input style="width: 50px" class="opt_record_interval" min="1" max="10" step="1"
			       value="<?php print($option["opt_record_interval"]); ?>" type="number">
			<span class="add-on">sec</span>
		</div>
		<a class="help-ico more-to-right" data-trigger="hover" rel="popover"
		   data-original-title="Send interval"
		   data-content="Send messages with recorded data to the database in the specified interval">lnk</a>
	</div>
</div>

<div class="control-group">
	<label class="control-label">Session delay</label>

	<div class="controls">
		<div class="input-append input-mini">
			<input style="width: 50px" class="opt_record_kill_session" min="5" max="1000"
			       step="5" value="<?php print($option["opt_record_kill_session"]); ?>"
			       type="number">
			<span class="add-on">sec</span>
		</div>
		<a class="help-ico more-to-right" data-trigger="hover" rel="popover"
		   data-original-title="Session delay"
		   data-content="If the user will be inactive in the next XX seconds you may say that his previous session expired and the next time new session will be created">lnk</a>
	</div>
</div>

<div class="form-actions">
	<button type="button" class="btn btn-primary save-button width-auto"
	        data-loading-text="Saving...">
		Save changes
	</button>
</div>
</form>
</div>
</div>
</div>
</div>
<br/>
<?php echo date("Y") ?> &copy; <?php echo $this->OPTIONS['brandname'] ?>
v. <?php echo $this->OPTIONS['version']; ?>
</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"
        type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/excanvas.js"></script>
<script src="<?php echo $this -> PLUGIN_URL ?>assets/plugins/respond.js"></script>
<![endif]-->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
<!-- IMPORTANT! jquery.slimscroll.min.js depends on jquery-ui-1.10.1.custom.min.js -->

<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>
<script type="text/javascript"
        src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.input-ip-address-control-1.0.min.js"></script>


<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/alerter.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js" type="text/javascript"></script>
<script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/index.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	jQuery(document).ready(function () {
		App.init(); // initlayout and core plugins
		Index.init();

		$('#black_ips').ipAddress();

		jQuery('.help-ico').popover({'placement': 'right'});


		//all pages/special page buttons
		jQuery('.all-special button').click(function () {
			if (!jQuery(this).hasClass("active")) {
				jQuery(this).parent().find("button").removeClass("btn-success");
				jQuery(this).addClass("btn-success")

				if (jQuery(this).hasClass("btn-special")) {
					jQuery(".pagesposts").removeAttr("disabled");
				} else {
					jQuery(".pagesposts").attr("disabled", "");
				}
			}
		})

		//save settings
		jQuery('.save-button').click(function () {
			//validating

			//post var
			var post = {};
			post.opt_record_status = jQuery('.opt_record_status .active').attr("data-value");
			post.opt_record_mousemove = jQuery('.opt_record_mousemove .active').attr("data-value");
			post.opt_record_pagescroll = jQuery('.opt_record_pagescroll .active').attr("data-value");
			post.opt_record_all = jQuery('.opt_record_all').hasClass("active");
			post.opt_record_special = (jQuery('.opt_record_all').hasClass("active")) ? "" : jQuery('.opt_record_special').val();
			post.opt_black_ips = jQuery('#black_ips_tagsinput .tag span').text().replace(/\s{2,}/g, ' ').split(' ')
			post.opt_record_interval = jQuery('.opt_record_interval').val();
			post.opt_record_kill_session = jQuery('.opt_record_kill_session').val();
			post.opt_record_tz = jQuery('.opt_record_tz').val();
			post.opt_ignore_query = jQuery('.opt_ignore_query .active').attr("data-value");
			post.opt_record_to = '<?php echo $_GET["settings"] ?>';
			post.hmtracker_action = "save";

			//sending
			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>/?hmtrackersettings', post, function (data) {
				jQuery("#settings_form").prepend(alerter(data, 3));
				jQuery('.save-button').button('reset');
			});

		})

		//onoff buttons
		jQuery('.onoff button').click(function () {
			if (!jQuery(this).hasClass("active")) {
				if (jQuery(this).hasClass("btn-on")) {
					jQuery(this).addClass("btn-success")
					jQuery(this).parent().find(".btn-off").removeClass("btn-danger");
				}
				if (jQuery(this).hasClass("btn-off")) {
					jQuery(this).addClass("btn-danger")
					jQuery(this).parent().find(".btn-on").removeClass("btn-success");
				}
			}
		})


		//ip
		jQuery('.add_ip').click(function () {

			//validate
			if (jQuery('#black_ips').val() == "___.___.___.___") {
				jQuery('#black_ips').focus();
				return false;
			}

			//post var
			var post = {};
			post.ip = jQuery('#black_ips').val();
			post.action = 'add_ip';
			post.opt_record_to = '<?php echo $_GET["settings"] ?>';

			//sending
			jQuery(this).button('loading')
			jQuery.post('<?php echo admin_url() ?>/?hmtrackeractions', post, function (data) {
				jQuery('.add_ip').button('reset');
				jQuery('#ips_cont').append('<div class="label label-warning" style="font-size: 14px; margin-top: 3px; padding: 4px;">' + data + '<button class="close del_ip" type="button" style="float: none;" to-del="' + data + '">×</button></div><br/>')
				to_det_init();
			});
		})
		to_det_init();

	});
	function to_det_init() {
		jQuery('.del_ip').click(function () {
			//post var
			var post = {};
			post.ip = jQuery(this).attr('to-del');
			post.action = 'del_ip';
			post.opt_record_to = '<?php echo $_GET["settings"] ?>';
			var latest_el = jQuery(this);
			//sending
			jQuery.post('<?php echo admin_url() ?>/?hmtrackeractions', post, function (data) {
				if (data == "ok") {
					latest_el.parent().remove();
				}
			});
		})
	}
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
