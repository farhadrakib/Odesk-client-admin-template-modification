<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH)) die('Can`t be called directly'); ?>
<?php
//secure check $_POST variables
$_GET['hmtrackerdata'] = rawurlencode($_GET["hmtrackerdata"]);
$_REQUEST["user"]      = HMTrackerFN::hmtracker_secure($_REQUEST["user"]);


//save spy data to db
if (isset($_REQUEST['user'])) {

	global $wpdb;


//fetch user
	$user = get_user_by('user_key', HMTrackerFN::hmtracker_secure($_GET['uid']));

//fetch project settings and check if we can track domain
	$general_opts   = array($this->PROJECTS_NAME . $user->user_key);
	$opts           = get_options($general_opts);
	$this->PROJECTS = $opts[$this->PROJECTS_NAME . $user->user_key];
	$settings       = $this->PROJECTS[$_GET['hmtrackerdata']]['settings'];

	if (isset($settings['opt_record_tz'])) date_default_timezone_set($settings['opt_record_tz']);

//stack the data
	$sessions    = array();
	$click_arr   = array();
	$mmove_arr   = array();
	$scroll_arr  = array();
	$popular_arr = array();
	if (isset($_REQUEST['data'])) {
		$data = json_decode(base64_decode($_REQUEST['data']));

//		echo base64_decode($_REQUEST['data']) . "\n";
//		print_r($data);
		foreach ($data as $key => $value) {

//			echo "SESSION: $key\n";

			if (!isset($sessions[$key]["time"])) $sessions[$key]["time"] = 0;

			foreach ($value as $kk => $vv) { //pages lvl

//				echo "KEY: $kk\n";
				//for special pages
				if (($settings["opt_record_all"] == "false" && !(in_array($kk, $settings['opt_record_special'])))) {
					continue;
				}

				// Parse the url
				$parsed_url = parse_url($kk);
				// If we have a query string
				if (isset($parsed_url['query'])) {
					// Find gclid and remove it
					$q_options = explode("&", $parsed_url['query']);
					foreach ($q_options as $qkey => $q_option) {
						$o = explode("=", $q_option);
						if ($o[0] == "gclid") {
							unset($q_options[$qkey]);
						}
					}
					$parsed_url['query'] = implode("&", $q_options);

					// Rework the url to exclude the gclid query option
					$kk = "{$parsed_url['scheme']}://{$parsed_url['host']}{$parsed_url['path']}";
					// If user wants to include othre query string options then add to url
					if (isset($settings['opt_ignore_query']) && $settings['opt_ignore_query'] == 0) {
						$kk .= "?{$parsed_url['query']}";
					}
				}

				$scroll_arr[$kk]["height"]    = 0;
				$scroll_arr[$kk]["maxscroll"] = 0;
				$popular_arr[$kk]             = $settings["opt_record_interval"];
				foreach ($vv as $kkk => $vvv) { //event lvl
					foreach ($vvv as $kkkk => $vvvv) { //events arr lvl
						if ($kkk != "responsetive")
							if ($sessions[$key]["time"] < (int)($vvvv[0])) $sessions[$key]["time"] = (int)($vvvv[0]);
						if ($kkk == "mouse_click") {
							if (isset($click_arr[$kk]))
								$click_arr[$kk] .= "|" . $vvvv[2] . " " . $vvvv[3] . " " . $vvvv[6];
							else
								$click_arr[$kk] = "|" . $vvvv[2] . " " . $vvvv[3] . " " . $vvvv[6];
						}
						if ($kkk == "mouse_move") {
							if (isset($mmove_arr[$kk]))
								$mmove_arr[$kk] .= "|" . $vvvv[1] . " " . $vvvv[2] . " " . $vvvv[3];
							else
								$mmove_arr[$kk] = "|" . $vvvv[1] . " " . $vvvv[2] . " " . $vvvv[3];
						}
						if ($kkk == "page_scroll") {
							if ($scroll_arr[$kk]["maxscroll"] < $vvvv[1]) $scroll_arr[$kk]["maxscroll"] = $vvvv[1];
						}
						if ($kkk == "window_size") {
							if ($scroll_arr[$kk]["height"] < $vvvv[1]) $scroll_arr[$kk]["height"] = $vvvv[1];
						}
					}
				}
				$sessions[$key]['data'][] = array($kk => $vv);
			}
		}
	}

//	print_r($click_arr);;
//	echo "\n";

	//put clicks to DB
	$table2 = T_PREFIX . 'clicks_' . $_GET['uid'];
	foreach ($click_arr as $key => $value) {

		$clicks = $wpdb->get_row("SELECT * FROM $table2 WHERE `page_url` = '$key' AND `project` = '" . $_GET['hmtrackerdata'] . "' ORDER BY `id` DESC LIMIT 1");

		if (!$clicks) {
			$q = "INSERT INTO `" . $table2 . "` (`page_url`,`click_data`,`date`,`project`) VALUES ('" . $key . "','" . $value . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
			$wpdb->query($q);
		} else {
			if ($clicks->click_data != "") {
				$clickStr = $clicks->click_data;
			}
			if (strlen($clickStr) > 600 || date("m.d.y") != date("m.d.y", strtotime($clicks->date))) {
				$q = "INSERT INTO `" . $table2 . "` (`page_url`,`click_data`,`date`,`project`) VALUES ('" . $key . "','" . $value . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
				$wpdb->query($q);
			} else {
				$clickStrMerged = $clickStr . $value;
				$q              = "UPDATE `" . $table2 . "` SET  `click_data` = '" . $clickStrMerged . "' WHERE `page_url` = '" . $key . "' ORDER BY `id` DESC LIMIT 1 ";
				$wpdb->query($q);
			}
		}
	}

	//put mmove to DB
	$table3 = T_PREFIX . 'mmove_' . $_GET['uid'];
	foreach ($mmove_arr as $key => $value) {

		$clicks = $wpdb->get_row("SELECT * FROM $table3 WHERE `page_url` = '$key' AND `project` = '" . $_GET['hmtrackerdata'] . "' ORDER BY `id` DESC LIMIT 1");
		if (!$clicks) {
			$q = "INSERT INTO `" . $table3 . "` (`page_url`,`mmove_data`,`date`,`project`) VALUES ('" . $key . "','" . $value . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
			$wpdb->query($q);
		} else {
			if ($clicks->mmove_data != "") {
				$clickStr = $clicks->mmove_data;
			}
			if (strlen($clickStr) > 600 || date("m.d.y") != date("m.d.y", strtotime($clicks->date))) {
				$q = "INSERT INTO `" . $table3 . "` (`page_url`,`mmove_data`,`date`,`project`) VALUES ('" . $key . "','" . $value . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
				$wpdb->query($q);
			} else {
				$clickStrMerged = $clickStr . $value;
				$q              = "UPDATE `" . $table3 . "` SET  `mmove_data` = '" . $clickStrMerged . "' WHERE `page_url` = '" . $key . "' ORDER BY `id` DESC LIMIT 1 ";
				$wpdb->query($q);
			}
		}
	}

	//put scroll to DB
	$table4 = T_PREFIX . 'scroll_' . $_GET['uid'];
	foreach ($scroll_arr as $key => $value) {

		$clicks = $wpdb->get_row("SELECT * FROM $table4 WHERE `page_url` = '$key' AND `project` = '" . $_GET['hmtrackerdata'] . "' ORDER BY `id` DESC LIMIT 1");
		if (!$clicks) {
			$q = "INSERT INTO `" . $table4 . "` (`page_url`,`scroll_data`,`date`,`project`) VALUES ('" . $key . "','" . ($value["height"] + $value["maxscroll"]) . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
			$wpdb->query($q);
		} else {
			if ($clicks->scroll_data != "") {
				$clickStr = $clicks->scroll_data;
			}
			if (strlen($clickStr) > 600 || date("m.d.y") != date("m.d.y", strtotime($clicks->date))) {
				$q = "INSERT INTO `" . $table4 . "` (`page_url`,`scroll_data`,`date`,`project`) VALUES ('" . $key . "','" . ($value["height"] + $value["maxscroll"]) . "', NOW(),'" . $_GET['hmtrackerdata'] . "')";
				$wpdb->query($q);
			} else {
				$clickStrMerged = $clickStr . "|" . ($value["height"] + $value["maxscroll"]);
				$q              = "UPDATE `" . $table4 . "` SET  `scroll_data` = '" . $clickStrMerged . "' WHERE `page_url` = '" . $key . "' ORDER BY `id` DESC LIMIT 1 ";
				$wpdb->query($q);
			}
		}
	}

	//put popular to DB
	$table5 = T_PREFIX . 'popular_' . $_GET['uid'];
	foreach ($popular_arr as $key => $value) {
		$clicks = $wpdb->get_row("SELECT * FROM $table5 WHERE `page_url` = '$key' AND `project` = '" . $_GET['hmtrackerdata'] . "' ORDER BY `id` DESC LIMIT 1");
		if (!$clicks) {
			$q   = "INSERT INTO `" . $table5 . "` (`date`,`page_url`,`points`,`project`) VALUES ('" . date("Y-m-d") . "','" . $key . "'," . $value . ",'" . $_GET['hmtrackerdata'] . "')";
			$res = $wpdb->query($q);

		} else {
			$pnts = 0;
			if ($clicks->points != "") {
				$pnts = $clicks->points;
			}
			$pnts += $value;
			$q   = "UPDATE $table5 SET `points` = " . $pnts . " WHERE `page_url` = '" . $key . "' LIMIT 1 ";
			$res = $wpdb->query($q);
		}
	}


	$table = T_PREFIX . 'main_' . $_GET['uid'];


	foreach ($sessions as $session_id => $post) {
		$data          = $post['data'];
		$session       = $wpdb->get_row("SELECT * FROM $table WHERE session_id = '$session_id' AND `project` = '" . $_GET['hmtrackerdata'] . "'");
		$session_array = array();
		// if session number exist:
		if ($session != null) {
			if (isset($session->session_spydata)) {
				$session_array = json_decode($session->session_spydata);
			}
			//get last page key
			$lastkey = "";
			foreach ($session_array[count($session_array) - 1] as $key => $value) {
				$lastkey = $key;
				break;
			}
			//get first page key
			$firstkey = "";
			foreach ($data[0] as $kkey => $vvalue) {
				$firstkey = $kkey;
				break;
			}


			$start = ($firstkey == $lastkey) ? 1 : 0;
			foreach ($data as $kkey => $vvalue) {
				if ($kkey >= $start)
					$session_array[] = $vvalue;
				else {
					foreach ($data[0] as $k2 => $v2) {
						foreach ($v2 as $k3 => $v3) {
							if (!isset($session_array[count($session_array) - 1]->$k2->$k3)) $session_array[count($session_array) - 1]->$k2->$k3 = array();
							$session_array[count($session_array) - 1]->$k2->$k3 = array_merge($session_array[count($session_array) - 1]->$k2->$k3, $v3);
						}
					}
				}
			}


			$time = 0;
			$sum  = 0;
			foreach ($session_array as $k => $v) {
				foreach ($v as $kk => $vv) {
					foreach ($vv as $kkk => $vvv) {
						if ($kkk != "responsetive") {
							foreach ($vvv as $kkkk => $vvvv) {
								if ($time < (int)$vvvv[0]) $time = (int)$vvvv[0];
							}
						}
					}
				}

				$sum += (int)$time;
				$time = 0;
			}
			//update db record

			$q = "UPDATE `" . $table . "` SET `session_end` = '" . ($session->session_start + $sum + 1) . "', `session_spydata` = '" . json_encode($session_array) . "' WHERE `session_id` = '" . $session_id . "'";
			$wpdb->query($q);
		} // if we have new session just insert new record
		else if ($post['time'] > 0) {
			$q = "INSERT INTO `" . $table . "` (`user_id`,`session_id`,`session_start`,`session_end`,`session_time`,`session_spydata`,`project`) VALUES ('" . $_REQUEST['user'] . "','" . $session_id . "','" . (time() - $post['time']) . "','" . time() . "','" . time() . "','" . json_encode($data) . "','" . $_GET['hmtrackerdata'] . "')";
			$wpdb->query($q);
		}

	}
	//end foreach


}//end if
?>