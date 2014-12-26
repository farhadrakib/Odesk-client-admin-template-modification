<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13-11-2014
 * Time: 18:14
 */

require_once( 'includes/aws_sdk/rds_class.php' );

function checkIamConfig( $options ) {
	$valid_key    = isset( $options['iam_key'] ) && $options['iam_key'] != "";
	$valid_secret = isset( $options['iam_secret'] ) && $options['iam_secret'] != "";
	$valid_region = isset( $options['iam_region'] ) && $options['iam_region'] != "";


	return $valid_key && $valid_secret && $valid_region;
}

$options = array();
if ( isset( $this->OPTIONS ) ) {
	$options = $this->OPTIONS;
} elseif ( isset( $_POST['iam_key'] ) && isset( $_POST['iam_secret'] ) && isset( $_POST['iam_region'] ) ) {
	$options['iam_key']      = $_POST['iam_key'];
	$options['iam_secret']   = $_POST['iam_secret'];
	$options['iam_region']   = $_POST['iam_region'];
	$options['rds_instance'] = $_POST['instance'];
} else {
	global $HMTrackerPro_OPTION_NAME;
	$options = get_option( $HMTrackerPro_OPTION_NAME );
}

$rds = false;
if ( checkIamConfig( $options ) ) {
	$rds      = new awsRds( $options['iam_key'], $options['iam_secret'], $options['iam_region'] );
	$instance = $rds->GetInstance( $options['rds_instance'] );
}