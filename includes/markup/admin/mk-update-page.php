<?php
@ini_set('max_execution_time', 300);
@ob_implicit_flush();
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
<?php

function Zip($source, $destination, $include_dir = false)
{

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		return false;
	}
	$source = str_replace('\\', '/', realpath($source));

	if (is_dir($source) === true) {

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		if ($include_dir) {

			$arr     = explode("/", $source);
			$maindir = $arr[count($arr) - 1];

			$source = "";
			for ($i = 0; $i < count($arr) - 1; $i++) {
				$source .= '/' . $arr[$i];
			}

			$source = substr($source, 1);

			$zip->addEmptyDir($maindir);

		}

		foreach ($files as $file) {
			$file = str_replace('\\', '/', $file);
			// Ignore "." and ".." folders
			if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..', 'config.php')) || strpos($file, "_backup") !== false)
				continue;

			$file = realpath($file);

			if (is_dir($file) === true) {
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			} else if (is_file($file) === true && substr($file,-9) != 'error_log') {
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
	} else if (is_file($source) === true && substr($source,-9) != 'error_log') {
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	return $zip->close();
}


//error array
$errors = array();

//check zip
if (!extension_loaded('zip')) {
	$errors[] = "Error: Can't work with ZIP" . "<br/>";
}

//create backup folder
if (count($errors) < 1) {
	echo "Creating backup..." . "<br/>";
	$bkb_dir = $this->PLUGIN_PATH . "_backup" . DIRECTORY_SEPARATOR;
	$bkb_zip = $bkb_dir . $this->OPTIONS['version'] . ".zip";
	if (!is_dir($bkb_dir)) {
		if (!mkdir($bkb_dir, 0744)) $errors[] = "Error: Could not create backup" . "<br/>";
	}
}

//make zip backup
if (count($errors) < 1) {
	try {
		if (!Zip($this->PLUGIN_PATH, $bkb_zip, false)) $errors[] = "Error: Could not make ZIP backup" . "<br/>";
	} catch (Exception $e) {
		$errors[] = $e->getMessage() . "<br/>";
	}
}

//download zip
if (count($errors) < 1) {
	echo "Downloading " . $this->OPTIONS['last_info']['link'] . "..." . "<br/>";
	$target_url = $this->OPTIONS['last_info']['link'];
	$userAgent  = 'HeatMapTracker';
	$file_zip   = $this->PLUGIN_PATH . $this->OPTIONS['last_info']['version'] . ".zip";
	$ch         = @curl_init();
	$fp         = fopen("$file_zip", "w");
	@curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	@curl_setopt($ch, CURLOPT_URL, $target_url);
	@curl_setopt($ch, CURLOPT_FAILONERROR, true);
	@curl_setopt($ch, CURLOPT_HEADER, 0);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	@curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	@curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	@curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	@curl_setopt($ch, CURLOPT_FILE, $fp);
	$page = @curl_exec($ch);

	if (!$page) {
		$errors[] = "cURL Error: " . curl_error($ch) . "<br/>";
	}
}

//unpacking
if (count($errors) < 1) {
	echo "Unzipping " . $this->OPTIONS['last_info']['version'] . ".zip" . "..." . "<br/>";
	$zip = new ZipArchive;

	if ($zip->open($file_zip) != true) {
		echo "<br>Could not open $file_zip";
		$errors[] = "Error: Could not open " . $file_zip . "<br/>";
	}
	$zip->extractTo($this->PLUGIN_PATH);
	$zip->close();
	unlink($file_zip);
}
if (count($errors) < 1) {
	$this->OPTIONS['prev_version'] = $this->OPTIONS['version'];
	update_option($this->OPTION_NAME, $this->OPTIONS);
	echo "Finished" . "<br/>";
} else {
	foreach ($errors as $key => $value) {
		echo $value . "<br/>";
	}
}
echo "<a href='" . admin_url() . "?about'>Back to HeatMapTracker</a>";
/*

 // make the cURL request to $target_url

 if (!$page) {
   echo "<br />cURL error number:" .curl_errno($ch);
   echo "<br />cURL error:" . curl_error($ch);
   exit;
 }
 curl_close($ch);
 echo "<br>Downloaded file: $target_url";
 echo "<br>Saved as file: $file_zip";
 echo "<br>About to unzip ...";
 // Un zip the file
 $zip = new ZipArchive;
   if (! $zip) {
	 echo "<br>Could not make ZipArchive object.";
	 exit;
   }
   if($zip->open("$file_zip") != "true") {
	   echo "<br>Could not open $file_zip";
		 }
   $zip->extractTo("$file_txt");
   $zip->close();
 echo "<br>Unzipped file to: $file_txt<br><br>";  */
?>
				