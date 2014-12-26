<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13-11-2014
 * Time: 15:35
 */

use Aws\Ec2\Ec2Client;
use Aws\Rds\RdsClient;

require 'aws-autoloader.php';

class awsRds {

	protected $ec2 = false;
	private $rds = false;

	public function __construct( $key, $secret, $region ) {
		$this->rds = RdsClient::factory( array(
			'key'    => $key,
			'secret' => $secret,
			'region' => $region
		) );

		$this->ec2 = Ec2Client::factory( array(
			'key'    => $key,
			'secret' => $secret,
			'region' => $region
		) );
	}

	public function doAction( $action, $params = array(), $type = "rds" ) {
		try {
			switch ( $type ) {
				case "rds":
					return $this->rds->$action( $params );
					break;
				case "ec2":
					return $this->ec2->$action( $params );
					break;
			}
		} catch ( Exception $e ) {
			return array( 'error' => array( 'Caught exception' => $e->getMessage() ) );
		}
	}

	public function ListInstances( $identifier = false, $filters = array(), $max = null, $marker = '' ) { //List Available DB Instances by identifiers
		$options = array();

		if ( $identifier ) {
			$options['DBInstanceIdentifier'] = $identifier;
		}
		if ( ! empty( $filters ) ) {
			$options['Filters'] = $filters;
		}
		if ( is_int( $max ) ) {
			$options['MaxRecords'] = $max;
		}

		if ( strlen( $marker ) > 0 ) {
			$options['Marker'] = $marker;
		}
		$instances = $this->doAction( 'describeDBInstances', $options );

		if ( isset( $instances['error'] ) ) {
			return $instances;
		}

		return $instances->get( 'DBInstances' );
	}

	public function getInstance( $identifier = false, $filters = array(), $max = null, $marker = '' ) {
		if ( ! $identifier && empty( $filters ) ) {
			return false;
		}
		$instance = $this->ListInstances( $identifier, $filters, $max, $marker );

		return $instance[0];
	}

	public function createInstance( $options ) {
		return $this->doAction( 'createDBInstance', $options );
	}

	public function updateInstance( $identifier, $options = array() ) {
		if ( empty( $options ) ) {
			return array( 'error' => array( 'Caught exception' => "no options defined!" ) );
		}
		$options['DBInstanceIdentifier'] = $identifier;
		if ( ! isset( $options['ApplyImmediately'] ) ) {
			$options['ApplyImmediately'] = true;
		}
		if ( isset( $options['StorageType'] ) && $options['StorageType'] == 'io1' && ! isset( $options['Iops'] ) ) {
			$options['Iops'] = 1000;
		}
		if ( isset( $options['AllocatedStorage'] ) && ( $options['AllocatedStorage'] < 5 || $options['AllocatedStorage'] > 3072 ) ) {
			return array( 'error' => array( 'AllocatedStorage' => "outside of allowed limits" ) );
		}
		if ( isset( $options['MultiAZ'] ) ) {
			$options['MultiAZ'] = ( $options['MultiAZ'] == 0 ? false : true );
		}

		return $this->doAction( 'modifyDBInstance', $options );
	}

	public function createSecurityGroup( $options ) {
		$response = $this->doAction( "createSecurityGroup", $options, "ec2" );
		if ( isset( $response['error'] ) ) {
			return $response;
		}

		return $response->get( "GroupId" );
	}

	public function  authorizeSecurityGroupIngress( $options ) {
		return $this->doAction( "authorizeSecurityGroupIngress", $options, "ec2" );
	}

	public function describeSecurityGroups( $options = array() ) {
		return $this->doAction( "describeSecurityGroups", $options, "ec2" );
	}

	public function deleteSecurityGroup( $options ) {
		return $this->doAction( "deleteDBSecurityGroup", $options );
	}
}