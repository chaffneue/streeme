<?php
/**
 * File: Amazon EC2
 * 	Amazon Elastic Compute Cloud (http://aws.amazon.com/ec2)
 *
 * Version:
 * 	2010.01.10
 *
 * Copyright:
 * 	2006-2009 Foleeo, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	CloudFusion - http://getcloudfusion.com
 * 	Amazon EC2 - http://aws.amazon.com/ec2
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: EC2_DEFAULT_URL
 * 	Specify the default queue URL.
 */
define('EC2_DEFAULT_URL', 'ec2.amazonaws.com');

/**
 * Constant: EC2_LOCATION_US
 * 	Specify the queue URL for the U.S.-specific hostname.
 */
define('EC2_LOCATION_US', 'us-east-1.');

/**
 * Constant: EC2_LOCATION_EU
 * 	Specify the queue URL for the E.U.-specific hostname.
 */
define('EC2_LOCATION_EU', 'eu-west-1.');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: EC2_Exception
 * 	Default EC2 Exception.
 */
class EC2_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonEC2
 * 	Container for all Amazon EC2-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 *
 * Example Usage:
 * (start code)
 * require_once('cloudfusion.class.php');
 *
 * // Instantiate a new AmazonEC2 object using the settings from the config.inc.php file.
 * $s3 = new AmazonEC2();
 *
 * // Instantiate a new AmazonEC2 object using these specific settings.
 * $s3 = new AmazonEC2($key, $secret_key);
 * (end)
 */
class AmazonEC2 extends CloudFusion
{
	/**
	 * Property: hostname
	 * Stores the hostname to use to make the request.
	 */
	var $hostname;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key - _string_ (Optional) Your Amazon API Key. If blank, it will look for the <AWS_KEY> constant.
	 * 	secret_key - _string_ (Optional) Your Amazon API Secret Key. If blank, it will look for the <AWS_SECRET_KEY> constant.
	 * 	account_id - _string_ (Optional) Your Amazon Account ID, without hyphens. If blank, it will look for the <AWS_ACCOUNT_ID> constant.
	 *
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
 	 *
	 * See Also:
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/__construct.phps
	 */
	public function __construct($key = null, $secret_key = null, $account_id = null)
	{
		$this->api_version = '2008-12-01';
		$this->hostname = EC2_DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			throw new EC2_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new EC2_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		if (!$account_id && !defined('AWS_ACCOUNT_ID'))
		{
			throw new EC2_Exception('No Amazon account ID was passed into the constructor, nor was it set in the AWS_ACCOUNT_ID constant.');
		}

		return parent::__construct($key, $secret_key, $account_id);
	}


	/*%******************************************************************************************%*/
	// MISCELLANEOUS

	/**
	 * Method: set_locale()
	 * 	By default EC2 will self-select the most appropriate locale. This allows you to explicitly sets the locale for EC2 to use.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	locale - _string_ (Required) The locale to explicitly set for EC2. Available options are <EC2_LOCATION_US> and <EC2_LOCATION_EU>.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/set_locale.phps
	 */
	public function set_locale($locale)
	{
		$this->hostname = $locale . EC2_DEFAULT_URL;
	}


	/*%******************************************************************************************%*/
	// AVAILABILITY ZONES

	/**
	 * Method: describe_availability_zones()
	 * 	Describes availability zones that are currently available to the account and their states.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	ZoneName.n - _string_ (Optional) Name of an availability zone.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeAvailabilityZones.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/describe_availability_zones.phps
	 */
	public function describe_availability_zones($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeAvailabilityZones', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// ELASTIC IP ADDRESSES

	/**
	 * Method: allocate_address()
	 * 	Acquires an elastic IP address for use with your account.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-AllocateAddress.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <associate_address()>, <describe_addresses()>, <disassociate_address()>, <release_address()>
	 */
	public function allocate_address($returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('AllocateAddress', $opt, $this->hostname);
	}

	/**
	 * Method: associate_address()
	 * 	Associates an elastic IP address with an instance.
	 *
	 * 	If the IP address is currently assigned to another instance, the IP address is assigned to the new instance. This is an idempotent operation. If you enter it more than once, Amazon EC2 does not return an error.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	instance_id - _string_ (Required) The instance to which the IP address is assigned.
	 * 	public_ip - _string_ (Required) IP address that you are assigning to the instance, retrieved from <allocate_address()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-AssociateAddress.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <allocate_address()>, <describe_addresses()>, <disassociate_address()>, <release_address()>
	 */
	public function associate_address($instance_id, $public_ip, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['InstanceId'] = $instance_id;
		$opt['PublicIp'] = $public_ip;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('AssociateAddress', $opt, $this->hostname);
	}

	/**
	 * Method: describe_addresses()
	 * 	Lists elastic IP addresses assigned to your account.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	PublicIp.1 - _string_ (Required but can be empty) One Elastic IP addresses to describe.
	 * 	PublicIp.n - _string_ (Optional) More than one Elastic IP addresses to describe.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeAddresses.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <allocate_address()>, <associate_address()>, <disassociate_address()>, <release_address()>
	 */
	public function describe_addresses($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeAddresses', $opt, $this->hostname);
	}

	/**
	 * Method: disassociate_address()
	 * 	Disassociates the specified elastic IP address from the instance to which it is assigned.
	 *
	 * 	This is an idempotent operation. If you enter it more than once, Amazon EC2 does not return an error.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	public_ip - _string_ (Required) IP address that you are disassociating from the instance.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DisassociateAddress.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <allocate_address()>, <associate_address()>, <describe_addresses()>, <release_address()>
	 */
	public function disassociate_address($public_ip, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['PublicIp'] = $public_ip;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DisassociateAddress', $opt, $this->hostname);
	}

	/**
	 * Method: release_address()
	 * 	Releases an elastic IP address associated with your account. If you run this operation on an elastic IP address that is already released, the address might be assigned to another account which will cause Amazon EC2 to return an error.
	 *
	 * 	Releasing an IP address automatically disassociates it from any instance with which it is associated. For more information, see <disassociate_address()>.
	 *
	 * 	After releasing an elastic IP address, it is released to the IP address pool and might no longer be available to your account. Make sure to update your DNS records and any servers or devices that communicate with the address.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	public_ip - _string_ (Required) IP address that you are releasing from your account.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-ReleaseAddress.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <allocate_address()>, <associate_address()>, <describe_addresses()>, <disassociate_address()>
	 */
	public function release_address($public_ip, $returnCurlHandle = null)
	{
		if (!$opt) $opt = array();
		$opt['PublicIp'] = $public_ip;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('ReleaseAddress', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// EBS SNAPSHOTS TO S3

	/**
	 * Method: create_snapshot()
	 * 	Creates a snapshot of an Amazon EBS volume and stores it in Amazon S3. You can use snapshots for backups, to launch instances from identical snapshots, and to save data before shutting down an instance. For more information, see Amazon Elastic Block Store.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	volume_id - _string_ (Required) The ID of the Amazon EBS volume to snapshot. Must be a volume that you own.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-CreateSnapshot.html
	 * 	Related - <describe_snapshots()>, <delete_snapshot()>
	 */
	public function create_snapshot($volume_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['VolumeId'] = $volume_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('CreateSnapshot', $opt, $this->hostname);
	}

	/**
	 * Method: describe_snapshots()
	 * 	Describes the status of Amazon EBS snapshots.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	SnapshotId.n - _string_ (Optional) The ID of the Amazon EBS snapshot.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeSnapshots.html
	 * 	Related - <create_snapshot()>, <delete_snapshot()>
	 */
	public function describe_snapshots($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeSnapshots', $opt, $this->hostname);
	}

	/**
	 * Method: delete_snapshot()
	 * 	Deletes a snapshot of an Amazon EBS volume that is stored in Amazon S3.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	snapshot_id - _string_ (Optional) The ID of the Amazon EBS snapshot to delete.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DeleteSnapshot.html
	 * 	Related - <create_snapshot()>, <describe_snapshots()>
	 */
	public function delete_snapshot($snapshot_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['SnapshotId'] = $snapshot_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeleteSnapshot', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// EBS VOLUMES

	/**
	 * Method: create_volume()
	 * 	Creates a new Amazon EBS volume that you can mount from any Amazon EC2 instance. You must specify an availability zone when creating a volume. The volume and any instance to which it attaches must be in the same availability zone.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	sizesnapid - _mixed_ (Required) Either the size of the volume in GB as an integer (from 1 to 1024), or the ID of the snapshot from which to create the new volume as a string.
	 * 	zone - _string_ (Required) The availability zone in which to create the new volume.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-CreateVolume.html
	 * 	Related - <describe_volumes()>, <attach_volume()>, <detach_volume()>, <delete_volume()>
	 */
	public function create_volume($sizesnapid, $zone, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['AvailabilityZone'] = $zone;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		if (is_numeric($sizesnapid))
		{
			$opt['Size'] = $sizesnapid;
		}
		else
		{
			$opt['SnapshotId'] = $sizesnapid;
		}

		return $this->authenticate('CreateVolume', $opt, $this->hostname);
	}

	/**
	 * Method: describe_volumes()
	 * 	Lists one or more Amazon EBS volumes that you own. If you do not specify any volumes, Amazon EBS returns all volumes that you own.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	VolumeId.n - _string_ (Optional) The ID of the volume to list.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeVolumes.html
	 * 	Related - <create_volume()>, <attach_volume()>, <detach_volume()>, <delete_volume()>
	 */
	public function describe_volumes($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeVolumes', $opt, $this->hostname);
	}

	/**
	 * Method: attach_volume()
	 * 	Attaches an Amazon EBS volume to an instance.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	volume_id - _string_ (Required) The ID of the Amazon EBS volume.
	 * 	instance_id - _string_ (Required) The ID of the instance to which the volume attaches.
	 * 	device - _string_ (Required) Specifies how the device is exposed to the instance (e.g., /dev/sdh). For information on standard storage locations, see Storage Locations.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-AttachVolume.html
	 * 	Storage Locations - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/instance-storage.html#storage-locations
	 * 	Related - <create_volume()>, <describe_volumes()>, <detach_volume()>, <delete_volume()>
	 */
	public function attach_volume($volume_id, $instance_id, $device, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['VolumeId'] = $volume_id;
		$opt['InstanceId'] = $instance_id;
		$opt['Device'] = $device;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('AttachVolume', $opt, $this->hostname);
	}

	/**
	 * Method: detach_volume()
	 * 	Detaches an Amazon EBS volume from an instance.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	volume_id - _string_ (Required) The ID of the Amazon EBS volume.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	InstanceId - _string_ (Optional) The ID of the instance from which the volume will detach.
	 * 	Device - _string_ (Optional) The name of the device.
	 * 	Force - _boolean_ (Optional) Forces detachment if the previous detachment attempt did not occur cleanly (logging into an instance, unmounting the volume, and detaching normally). This option can lead to data loss or a corrupted file system. Use this option only as a last resort to detach an instance from a failed instance. The instance will not have an opportunity to flush file system caches nor file system meta data. If you use this option, you must perform file system check and repair procedures.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DetachVolume.html
	 * 	Related - <create_volume()>, <describe_volumes()>, <attach_volume()>, <delete_volume()>
	 */
	public function detach_volume($volume_id, $opt = null)
	{
		if (!$opt) $opt = array();

		$opt['VolumeId'] = $volume_id;

		return $this->authenticate('DetachVolume', $opt, $this->hostname);
	}

	/**
	 * Method: delete_volume()
	 * 	Deletes an Amazon EBS volume.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	volume_id - _string_ (Required) The ID of the Amazon EBS volume.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DeleteVolume.html
	 * 	Related - <create_volume()>, <describe_volumes()>, <attach_volume()>, <detach_volume()>
	 */
	public function delete_volume($volume_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['VolumeId'] = $volume_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeleteVolume', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// MISCELLANEOUS

	/**
	 * Method: get_console_output()
	 * 	Retrieves console output for the specified instance. Instance console output is buffered and posted shortly after instance boot, reboot, and termination. Amazon EC2 preserves the most recent 64 KB output which will be available for at least one hour after the most recent post.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	instance_id - _string_ (Required) An instance ID returned from a previous call to <run_instances()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-GetConsoleOutput.html
	 * 	Related - <reboot_instances()>
	 */
	public function get_console_output($instance_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['InstanceId'] = $instance_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('GetConsoleOutput', $opt, $this->hostname);
	}

	/**
	 * Method: reboot_instances()
	 * 	Requests a reboot of one or more instances. This operation is asynchronous; it only queues a request to reboot the specified instance(s). The operation will succeed if the instances are valid and belong to the user. Requests to reboot terminated instances are ignored.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	InstanceId.1 - _string_ (Required) One instance ID returned from previous calls to <run_instances()>.
	 * 	InstanceId.n - _string_ (Optional) More than one instance IDs returned from previous calls to <run_instances()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-RebootInstances.html
	 * 	Related - <get_console_output()>
	 */
	public function reboot_instances($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('RebootInstances', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// IMAGES

	/**
	 * Method: deregister_image()
	 * 	De-registers an AMI. Once de-registered, instances of the AMI may no longer be launched.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_id - _string_ (Required) Unique ID of a machine image, returned by a call to <register_image()> or <describe_images()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DeregisterImage.html
	 * 	Related - <describe_images()>, <register_image()>
	 */
	public function deregister_image($image_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ImageId'] = $image_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeregisterImage', $opt, $this->hostname);
	}

	/**
	 * Method: describe_images()
	 * 	The DescribeImages operation returns information about AMIs, AKIs, and ARIs available to the user. Information returned includes image type, product codes, architecture, and kernel and RAM disk IDs. Images available to the user include public images available for any user to launch, private images owned by the user making the request, and private images owned by other users for which the user has explicit launch permissions.
	 *
	 * 	Launch permissions fall into three categories: (a) 'public' where the owner of the AMI granted launch permissions for the AMI to the all group. All users have launch permissions for these AMIs. (b) 'explicit' where the owner of the AMI granted launch permissions to a specific user. (c) 'implicit' where a user has implicit launch permissions for all AMIs he or she owns.
	 *
	 * 	The list of AMIs returned can be modified by specifying AMI IDs, AMI owners, or users with launch permissions. If no options are specified, Amazon EC2 returns all AMIs for which the user has launch permissions.
	 *
	 * 	If you specify one or more AMI IDs, only AMIs that have the specified IDs are returned. If you specify an invalid AMI ID, a fault is returned. If you specify an AMI ID for which you do not have access, it will not be included in the returned results.
	 *
	 * 	If you specify one or more AMI owners, only AMIs from the specified owners and for which you have access are returned. The results can include the account IDs of the specified owners, amazon for AMIs owned by Amazon or self for AMIs that you own.
	 *
	 * 	If you specify a list of executable users, only users that have launch permissions for the AMIs are returned. You can specify account IDs (if you own the AMI(s)), self for AMIs for which you own or have explicit permissions, or all for public AMIs.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
 	 * 	ExecutableBy.n - _string_ (Optional) Describe AMIs that the specified users have launch permissions for. Accepts Amazon account ID, 'self', or 'all' for public AMIs.
	 * 	ImageId.n - _string_ (Optional) A list of image descriptions.
	 * 	Owner.n - _string_ (Optional) Owners of AMIs to describe.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeImages.html
	 * 	Related - <deregister_image()>, <register_image()>
	 */
	public function describe_images($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeImages', $opt, $this->hostname);
	}

	/**
	 * Method: register_image()
	 * 	Registers an AMI with Amazon EC2. Images must be registered before they can be launched. For more information, see <run_instances()>.
	 *
	 * 	Each AMI is associated with an unique ID which is provided by the Amazon EC2 service through the <register_image()> operation. During registration, Amazon EC2 retrieves the specified image manifest from Amazon S3 and verifies that the image is owned by the user registering the image.
	 *
	 * 	The image manifest is retrieved once and stored within the Amazon EC2. Any modifications to an image in Amazon S3 invalidates this registration. If you make changes to an image, deregister the previous image and register the new image. For more information, see <deregister_image()>.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_location - _string_ (Required) Full path to your AMI manifest in Amazon S3 storage (i.e. mybucket/myimage.manifest.xml).
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-RegisterImage.html
	 * 	Related - <deregister_image()>, <describe_images()>
	 */
	public function register_image($image_location, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ImageLocation'] = $image_location;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('RegisterImage', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// IMAGE ATTRIBUTES

	/**
	 * Method: describe_image_attribute()
	 * 	Returns information about an attribute of an AMI. Only one attribute may be specified per call.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_id - _string_ (Required) ID of the AMI for which an attribute will be described, returned by a call to <register_image()> or <describe_images()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeImageAttribute.html
	 * 	Related - <modify_image_attribute()>, <reset_image_attribute()>
	 */
	public function describe_image_attribute($image_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ImageId'] = $image_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// This is the only supported value in the current release.
		$opt['Attribute'] = 'launchPermission';

		return $this->authenticate('DescribeImageAttribute', $opt, $this->hostname);
	}

	/**
	 * Method: modify_image_attribute()
	 * 	Modifies an attribute of an AMI.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_id - _string_ (Required) AMI ID to modify an attribute on.
	 * 	attribute - _string_ (Required) Specifies the attribute to modify. Supports 'launchPermission' and 'productCodes'.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
 	 * 	OperationType - _string_ (Required for 'launchPermission' Attribute) Specifies the operation to perform on the attribute. Supports 'add' and 'remove'.
 	 * 	UserId.n - _string_ (Required for 'launchPermission' Attribute) User IDs to add to or remove from the 'launchPermission' attribute.
 	 * 	UserGroup.n - _string_ (Required for 'launchPermission' Attribute) User groups to add to or remove from the 'launchPermission' attribute. Currently, only the 'all' group is available, specifiying all Amazon EC2 users.
 	 * 	ProductCode.n - _string_ (Required for 'productCodes' Attribute) Attaches product codes to the AMI. Currently only one product code may be associated with an AMI. Once set, the product code can not be changed or reset.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-ModifyImageAttribute.html
	 * 	Related - <describe_image_attribute()>, <reset_image_attribute()>
	 */
	public function modify_image_attribute($image_id, $attribute, $opt = null)
	{
		if (!$opt) $opt = array();

		$opt['ImageId'] = $image_id;
		$opt['Attribute'] = $attribute;

		return $this->authenticate('ModifyImageAttribute', $opt, $this->hostname);
	}

	/**
	 * Method: reset_image_attribute()
	 * 	Resets an attribute of an AMI to its default value (the productCodes attribute cannot be reset).
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_id - _string_ (Required) ID of the AMI for which an attribute will be described, returned by a call to <register_image()> or <describe_images()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-ResetImageAttribute.html
	 * 	Related - <describe_image_attribute()>, <modify_image_attribute()>
	 */
	public function reset_image_attribute($image_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ImageId'] = $image_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// This is the only supported value in the current release.
		$opt['Attribute'] = 'launchPermission';

		return $this->authenticate('ResetImageAttribute', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// INSTANCES

	/**
	 * Method: confirm_product_instance()
	 * 	Returns true if the given product code is attached to the instance with the given instance ID. The operation returns false if the product code is not attached to the instance.
	 *
	 * 	Can only be executed by the owner of the AMI. This feature is useful when an AMI owner is providing support and wants to verify whether a user's instance is eligible.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	product_code - _string_ (Required) The product code to confirm is attached to the instance.
	 * 	instance_id - _string_ (Required) The instance for which to confirm the product code.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-ConfirmProductInstance.html
	 * 	Related - <describe_instances()>, <run_instances()>, <terminate_instances()>
	 */
	public function confirm_product_instance($product_code, $instance_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ProductCode'] = $product_code;
		$opt['InstanceId'] = $instance_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('ConfirmProductInstance', $opt, $this->hostname);
	}

	/**
	 * Method: describe_instances()
	 * 	Returns information about instances owned by the user making the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	InstanceId.n - _string_ (Required) Set of instances IDs to get the status of.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeInstances.html
	 * 	Related - <confirm_product_instance()>, <run_instances()>, <terminate_instances()>
	 */
	public function describe_instances($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeInstances', $opt, $this->hostname);
	}

	/**
	 * Method: run_instances()
	 * 	The RunInstances operation launches a specified number of instances. The Query version of <run_instances()> only allows instances of a single AMI to be launched in one call. This is different from the SOAP API version of the call, but similar to the ec2-run-instances command line tool.
	 *
	 * 	If Amazon EC2 cannot launch the minimum number AMIs you request, no instances launch. If there is insufficient capacity to launch the maximum number of AMIs you request, Amazon EC2 launches as many as possible to satisfy the requested maximum values.
	 *
	 * 	Every instance is launched in a security group. If you do not specify a security group at launch, the instances start in your default security group. For more information on creating security groups, see <create_security_group()>.
	 *
	 * 	You can provide an optional key pair ID for each image in the launch request (for more information, see <create_key_pair()>). All instances that are created from images that use this key pair will have access to the associated public key at boot. You can use this key to provide secure access to an instance of an image on a per-instance basis. Amazon EC2 public images use this feature to provide secure access without passwords. IMPORTANT: Launching public images without a key pair ID will leave them inaccessible.
	 *
	 * 	The public key material is made available to the instance at boot time by placing it in the openssh_id.pub file on a logical device that is exposed to the instance as /dev/sda2 (the instance store). The format of this file is suitable for use as an entry within ~/.ssh/authorized_keys (the OpenSSH format). This can be done at boot (e.g., as part of rc.local) allowing for secure access without passwords.
	 *
	 * 	Optional user data can be provided in the launch request. All instances that collectively comprise the launch request have access to this data For more information, see Instance Metadata. NOTE: If any of the AMIs have a product code attached for which the user has not subscribed, the <run_instances()> call will fail.
	 *
	 * 	IMPORTANT: We strongly recommend using the 2.6.18 Xen stock kernel with the c1.medium and c1.xlarge instances. Although the default Amazon EC2 kernels will work, the new kernels provide greater stability and performance for these instance types. For more information about kernels, see Kernels, RAM Disks, and Block Device Mappings.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	image_id - _string_ (Required) ID of the AMI to launch instances based on.
	 * 	min_count - _integer_ (Required) Minimum number of instances to launch.
	 * 	max_count - _integer_ (Required) Maximum number of instances to launch.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	BlockDeviceMapping.n.DeviceName - _string_ (Optional; Required if BlockDeviceMapping.n.VirtualName is used) Specifies the device to which you are mapping a virtual name. For example: sdb.
	 * 	BlockDeviceMapping.n.VirtualName - _string_ (Optional; Required if BlockDeviceMapping.n.DeviceName is used) 	Specifies the virtual name to map to the corresponding device name. For example: instancestore0.
	 * 	InstanceType - _string_ (Optional) Specifies the instance type. Options include 'm1.small', 'm1.large', 'm1.xlarge', 'c1.medium', and 'c1.xlarge'. Defaults to 'm1.small'.
	 * 	KernelId - _string_ (Optional) 	The ID of the kernel with which to launch the instance. For information on finding available kernel IDs, see ec2-describe-images.
	 * 	KeyName - _string_ (Optional) Name of the keypair to launch instances with.
	 * 	Placement.AvailabilityZone - _string_ (Optional) Specifies the availability zone in which to launch the instance(s). To display a list of availability zones in which you can launch the instances, use the <describe_availability_zones()> operation. Default is determined by Amazon EC2.
	 * 	RamdiskId - _string_ (Optional) The ID of the RAM disk with which to launch the instance. Some kernels require additional drivers at launch. Check the kernel requirements for information on whether you need to specify a RAM disk. To find kernel requirements, go to the Resource Center and search for the kernel ID.
	 * 	SecurityGroup.n - _string_ (Optional) Names of the security groups to associate the instances with.
	 * 	UserData - _string_ (Optional) The user data available to the launched instances. This should be base64-encoded. See the UserDataType data type for encoding details.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-RunInstances.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <confirm_product_instance()>, <describe_instances()>, <terminate_instances()>
	 */
	public function run_instances($image_id, $min_count, $max_count, $opt = null)
	{
		if (!$opt) $opt = array();

		$opt['ImageId'] = $image_id;
		$opt['MinCount'] = $min_count;
		$opt['MaxCount'] = $max_count;

		return $this->authenticate('RunInstances', $opt, $this->hostname);
	}

	/**
	 * Method: terminate_instances()
	 * 	Shuts down one or more instances. This operation is idempotent and terminating an instance that is in the process of shutting down (or already terminated) will succeed.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	InstanceId.1 - _string_ (Required) One instance ID returned from previous calls to <run_instances()>.
	 * 	InstanceId.n - _string_ (Optional) More than one instance IDs returned from previous calls to <run_instances()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-TerminateInstances.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/elastic_ip.phps
	 * 	Related - <confirm_product_instance()>, <describe_instances()>, <run_instances()>
	 */
	public function terminate_instances($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('TerminateInstances', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// KEYPAIRS

	/**
	 * Method: create_key_pair()
	 * 	Creates a new 2048 bit RSA key pair and returns a unique ID that can be used to reference this key pair when launching new instances. For more information, see <run_instances()>.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key_name - _string_ (Required) A unique name for the key pair.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-CreateKeyPair.html
	 * 	Related - <delete_key_pair()>, <describe_key_pairs()>
	 */
	public function create_key_pair($key_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['KeyName'] = $key_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('CreateKeyPair', $opt, $this->hostname);
	}

	/**
	 * Method: delete_key_pair()
	 * 	Deletes a keypair.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key_name - _string_ (Required) Unique name for this key.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DeleteKeyPair.html
	 * 	Related - <create_key_pair()>, <describe_key_pairs()>
	 */
	public function delete_key_pair($key_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['KeyName'] = $key_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeleteKeyPair', $opt, $this->hostname);
	}

	/**
	 * Method: describe_key_pairs()
	 * 	Returns information about key pairs available to you. If you specify key pairs, information about those key pairs is returned. Otherwise, information for all registered key pairs is returned.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	KeyName.n - _string_ (Optional) One or more keypair IDs to describe.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeKeyPairs.html
	 * 	Related - <create_key_pair()>, <delete_key_pair()>
	 */
	public function describe_key_pairs($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeKeyPairs', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// SECURITY GROUPS

	/**
	 * Method: authorize_security_group_ingress()
	 * 	Adds permissions to a security group.
	 *
	 * 	Permissions are specified in terms of the IP protocol (TCP, UDP or ICMP), the source of the request (by IP range or an Amazon EC2 user-group pair), source and destination port ranges (for TCP and UDP), and ICMP codes and types (for ICMP). When authorizing ICMP, -1 may be used as a wildcard in the type and code fields.
	 *
	 * 	Permission changes are propagated to instances within the security group being modified as quickly as possible. However, a small delay is likely, depending on the number of instances that are members of the indicated group.
	 *
	 * 	When authorizing a user/group pair permission, group_name, SourceSecurityGroupName and SourceSecurityGroupOwnerId must be specified. When authorizing a CIDR IP permission, GroupName, IpProtocol, FromPort, ToPort and CidrIp must be specified. Mixing these two types of parameters is not allowed.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	group_name - _string_ (Required) Name of the security group to modify.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	CidrIp - _string_ (Required when authorizing CIDR IP permission) CIDR IP range to authorize access to when operating on a CIDR IP.
	 * 	FromPort - _integer_ (Required when authorizing CIDR IP permission) Bottom of port range to authorize access to when operating on a CIDR IP. This contains the ICMP type if ICMP is being authorized.
	 * 	ToPort - _integer_ (Required when authorizing CIDR IP permission) Top of port range to authorize access to when operating on a CIDR IP. This contains the ICMP code if ICMP is being authorized.
	 * 	IpProtocol - _string_ (Required when authorizing CIDR IP permission) IP protocol to authorize access to when operating on a CIDR IP. Valid values are 'tcp', 'udp' and 'icmp'.
	 * 	SourceSecurityGroupName - _string_ (Required when authorizing user/group pair permission) Name of security group to authorize access to when operating on a user/group pair.
	 * 	SourceSecurityGroupOwnerId - _string_ (Required when authorizing user/group pair permission) Owner of security group to authorize access to when operating on a user/group pair.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-AuthorizeSecurityGroupIngress.html
	 * 	Related - <revoke_security_group_ingress()>, <create_security_group()>, <delete_security_group()>, <describe_security_groups()>
	 */
	public function authorize_security_group_ingress($group_name, $opt = null)
	{
		if (!$opt) $opt = array();

		$opt['GroupName'] = $group_name;

		return $this->authenticate('AuthorizeSecurityGroupIngress', $opt, $this->hostname);
	}

	/**
	 * Method: create_security_group()
	 * 	Every instance is launched in a security group. If none is specified as part of the launch request then instances are launched in the default security group. Instances within the same security group have unrestricted network access to one another. Instances will reject network access attempts from other instances in a different security group. As the owner of instances you may grant or revoke specific permissions using the <authorize_security_group_ingress()> and <revoke_security_group_ingress()> operations.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	group_name - _string_ (Required) Name for the new security group.
	 * 	group_description - _string_ (Required) Description of the new security group.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-CreateSecurityGroup.html
	 * 	Related - <authorize_security_group_ingress()>, <revoke_security_group_ingress()>, <delete_security_group()>, <describe_security_groups()>
	 */
	public function create_security_group($group_name, $group_description, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['GroupName'] = $group_name;
		$opt['GroupDescription'] = $group_description;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('CreateSecurityGroup', $opt, $this->hostname);
	}

	/**
	 * Method: delete_security_group()
	 * 	Deletes a security group.
	 *
	 * 	If you attempt to delete a security group that contains instances, a fault is returned. If you attempt to delete a security group that is referenced by another security group, a fault is returned. For example, if security group B has a rule that allows access from security group A, security group A cannot be deleted until the allow rule is removed.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	group_name - _string_ (Required) Name for the security group.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DeleteSecurityGroup.html
	 * 	Related - <authorize_security_group_ingress()>, <revoke_security_group_ingress()>, <create_security_group()>, <describe_security_groups()>
	 */
	public function delete_security_group($group_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['GroupName'] = $group_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeleteSecurityGroup', $opt, $this->hostname);
	}

	/**
	 * Method: describe_security_groups()
	 * 	Returns information about security groups owned by the user making the request. An optional list of security group names may be provided to request information for those security groups only. If no security group names are provided, information of all security groups will be returned. If a group is specified that does not exist a fault is returned.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	GroupName.n - _string_ (Optional) List of security groups to describe.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeSecurityGroups.html
	 * 	Related - <authorize_security_group_ingress()>, <revoke_security_group_ingress()>, <create_security_group()>, <delete_security_group()>
	 */
	public function describe_security_groups($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeSecurityGroups', $opt, $this->hostname);
	}

	/**
	 * Method: revoke_security_group_ingress()
	 * 	Revokes existing permissions that were previously granted to a security group. The permissions to revoke must be specified using the same values originally used to grant the permission.
	 *
	 * 	Permissions are specified in terms of the IP protocol (TCP, UDP or ICMP), the source of the request (by IP range or an Amazon EC2 user-group pair), source and destination port ranges (for TCP and UDP), and ICMP codes and types (for ICMP). When authorizing ICMP, -1 may be used as a wildcard in the type and code fields.
	 *
	 * 	Permission changes are propagated to instances within the security group being modified as quickly as possible. However, a small delay is likely, depending on the number of instances that are members of the indicated group.
	 *
	 * 	When revoking a user/group pair permission, group_name, SourceSecurityGroupName and SourceSecurityGroupOwnerId must be specified. When authorizing a CIDR IP permission, GroupName, IpProtocol, FromPort, ToPort and CidrIp must be specified. Mixing these two types of parameters is not allowed.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	group_name - _string_ (Required) Name of the security group to modify.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	CidrIp - _string_ (Required when revoking CIDR IP permission) CIDR IP range to authorize access to when operating on a CIDR IP.
	 * 	FromPort - _integer_ (Required when revoking CIDR IP permission) Bottom of port range to authorize access to when operating on a CIDR IP. This contains the ICMP type if ICMP is being authorized.
	 * 	ToPort - _integer_ (Required when revoking CIDR IP permission) Top of port range to authorize access to when operating on a CIDR IP. This contains the ICMP code if ICMP is being authorized.
	 * 	IpProtocol - _string_ (Required when revoking CIDR IP permission) IP protocol to authorize access to when operating on a CIDR IP. Valid values are 'tcp', 'udp' and 'icmp'.
	 * 	SourceSecurityGroupName - _string_ (Required when revoking user/group pair permission) Name of security group to authorize access to when operating on a user/group pair.
	 * 	SourceSecurityGroupOwnerId - _string_ (Required when revoking user/group pair permission) Owner of security group to authorize access to when operating on a user/group pair.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-RevokeSecurityGroupIngress.html
	 * 	Related - <authorize_security_group_ingress()>, <create_security_group()>, <delete_security_group()>, <describe_security_groups()>
	 */
	public function revoke_security_group_ingress($group_name, $opt = null)
	{
		if (!$opt) $opt = array();

		$opt['GroupName'] = $group_name;

		return $this->authenticate('RevokeSecurityGroupIngress', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// BUNDLE WINDOWS AMIS

	/**
	 * Method: bundle_instance()
	 * 	Bundles an Amazon EC2 instance running Windows. For more information, see Bundling an AMI in Windows. This operation is for Windows instances only.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	instanceId - _string_ (Required) The ID of the instance to bundle.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	Bucket - _string_ (Required) The bucket in which to store the AMI.
	 * 	Prefix - _string_ (Required) The prefix to append to the AMI.
	 * 	UploadPolicy - _array_ (Optional) The upload policy gives Amazon EC2 limited permission to upload items into your Amazon S3 bucket. See example for documentation. If an Upload Policy is not provided, this method will generate one from the provided information setting ONLY the required values, and will set an expiration of 12 hours.
	 * 	AWSAccessKeyId - _string_ (Optional) The Access Key ID of the owner of the Amazon S3 bucket. Defaults to the AWS Key used to authenticate the request.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-BundleInstance.html
	 * 	Upload Policy - http://docs.amazonwebservices.com/AmazonS3/latest/HTTPPOSTExamples.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/bundle_windows.phps
	 * 	Related - <bundle_instance()>, <cancel_bundle_task()>, <describe_bundle_tasks()>
	 */
	public function bundle_instance($instance_id, $opt = null)
	{
		if (!$opt) $opt = array();

		// Instance ID
		$opt['instanceId'] = $instance_id;

		// Storage.S3.Bucket
		if (isset($opt['Bucket']))
		{
			$opt['Storage.S3.Bucket'] = $opt['Bucket'];
			unset($opt['Bucket']);
		}

		// Storage.S3.Prefix
		if (isset($opt['Prefix']))
		{
			$opt['Storage.S3.Prefix'] = $opt['Prefix'];
			unset($opt['Prefix']);
		}

		// Storage.S3.AWSAccessKeyId
		if (isset($opt['AWSAccessKeyId']))
		{
			$opt['Storage.S3.AWSAccessKeyId'] = $opt['AWSAccessKeyId'];
			unset($opt['AWSAccessKeyId']);
		}
		else
		{
			$opt['Storage.S3.AWSAccessKeyId'] = $this->key;
		}

		// Storage.S3.UploadPolicy
		if (isset($opt['UploadPolicy']))
		{
			$opt['Storage.S3.UploadPolicy'] = base64_encode($this->util->json_encode($opt['UploadPolicy']));
			unset($opt['UploadPolicy']);
		}
		else
		{
			$opt['Storage.S3.UploadPolicy'] = base64_encode($this->util->json_encode(array(
				'expiration' => gmdate(DATE_FORMAT_ISO8601, strtotime('+12 hours')),
				'conditions' => array(
					array('bucket' => $opt['Storage.S3.Bucket']),
					array('acl' => 'ec2-bundle-read')
				)
			)));
		}

		// Storage.S3.UploadPolicySignature
		$opt['Storage.S3.UploadPolicySignature'] = $this->util->hex_to_base64(hash_hmac('sha1', base64_encode($opt['Storage.S3.UploadPolicy']), $this->secret_key));

		return $this->authenticate('BundleInstance', $opt, $this->hostname);
	}

	/**
	 * Method: cancel_bundle_task()
	 * 	Cancels an Amazon EC2 bundling operation. For more information on bundling instances, see Bundling an AMI in Windows. This operation is for Windows instances only.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	bundle_id - _string_ (Required) The ID of the bundle task to cancel.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-CancelBundleTask.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/bundle_windows.phps
	 * 	Related - <bundle_instance()>, <cancel_bundle_task()>, <describe_bundle_tasks()>
	 */
	public function cancel_bundle_task($bundle_id, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['bundleId'] = $bundle_id;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('CancelBundleTask', $opt, $this->hostname);
	}

	/**
	 * Method: describe_bundle_tasks()
	 * 	Describes current bundling tasks. For more information on bundling instances, see Bundling an AMI in Windows. This operation is for Windows instances only.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	bundleId - _string_ (Optional) The ID of the bundle task to describe. If no ID is specified, all bundle tasks are described.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSEC2/latest/DeveloperGuide/ApiReference-Query-DescribeBundleTasks.html
	 * 	Example Usage - http://getcloudfusion.com/docs/examples/ec2/bundle_windows.phps
	 * 	Related - <bundle_instance()>, <cancel_bundle_task()>, <describe_bundle_tasks()>
	 */
	public function describe_bundle_tasks($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('DescribeBundleTasks', $opt, $this->hostname);
	}
}
