<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13-11-2014
 * Time: 18:13
 */

include( "includes/functions/fn-rds-process.php" );
if ( $rds ) {
	$return = array( "status_formatted" => showStatus( $instance['DBInstanceStatus'] ), "status" => $instance['DBInstanceStatus'] );
} else {
	$return = array( "error" => "Unable to connect to AWS RDS" );
}
die( json_encode( $return ) );
