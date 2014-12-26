<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 14-11-2014
 * Time: 09:31
 */

include( "includes/functions/fn-rds-class.php" );

function renderStatusbar( $message, $status = 'error' ) {
	return "<div class='alert alert-block alert-$status fade in'><button data-dismiss='alert' class='close' type='button'>×</button>$message</div>";
}

function showError( $key, $errors, $notice_bar = false ) {
	$error = "";
	if ( isset( $errors[ $key ] ) ) {
		if ( ! $notice_bar ) {
			$error = "<div><span style='color: #cc0000;'>{$errors[$key]}</span></div>";
		} else {
			$error = '';
			foreach ( $errors[ $key ] as $key => $value ) {
				$error .= "$key: $value";
			}
			renderStatusbar( $error );
		}
	}

	return $error;
}

function showStatus( $status ) {

	switch ( $status ) {
		case 'available':
			$colour = "#009933";
			break;
		case 'creating':
		case 'modifying':
		case 'backing-up':
			$colour = "#e07700";
			break;
		case 'rebooting':
			$colour = "#1166bb";
			break;
		default:
			$colour = "#cc0000";
			break;
	}

	$status = ucwords( $status );

	return "<span id='status' style='font-size: 20px; display: inline; color: {$colour};'>{$status}</span>";
}

$errors = array();
if ( isset( $_POST['form'] ) ) {
	switch ( $_POST['form'] ) {
		case "aim":
			if ( ! isset( $_POST['iam_key'] ) || ( isset( $_POST['iam_key'] ) && $_POST['iam_key'] == "" ) ) {
				$errors['iam_key'] = "AIM Key field is required";
			}
			if ( ! isset( $_POST['iam_secret'] ) || ( isset( $_POST['iam_secret'] ) && $_POST['iam_secret'] == "" ) ) {
				$errors['iam_secret'] = "AIM Secret field is required";
			}
			if ( empty( $errors ) ) {
				$this->OPTIONS['iam_key']    = $_POST['iam_key'];
				$this->OPTIONS['iam_secret'] = $_POST['iam_secret'];
				update_option( $this->OPTION_NAME, $this->OPTIONS );
			}
			break;
		case "rds":
			//If we're modifying the instance, only modify what's changed
			//Otherwise we potentially needlessly down the RDS server
			$updateoptions = array();
			foreach ( $_POST['options'] as $postoptionkey => $postoptionvalue ) {
				if ( $instance[ $postoptionkey ] != $postoptionvalue ) {
					$updateoptions[ $postoptionkey ] = $postoptionvalue;
				}
			}
			if ( ! isset( $updateoptions['StorageType'] ) && isset( $updateoptions['AllocatedStorage'] ) && $_POST['options']['StorageType'] == 'io1' && ! isset( $_POST['options']['Iops'] ) ) {
				$updateoptions['Iops'] = 1000;
			}
			if ( ! empty( $updateoptions ) ) {
				$update = $rds->updateInstance( $_POST['DBInstanceIdentifier'], $updateoptions );
				if ( isset( $update['error'] ) ) {
					$errors['rds_error'] = $update['error'];
				}
			}
			break;
		case "rebootAZ":
			$options['ForceFailover'] = true;
		case "reboot":
			$options = array( 'DBInstanceIdentifier' => $_POST['DBInstanceIdentifier'] );
			$rds->doAction( 'RebootDBInstance', $options );
			break;
	}

}