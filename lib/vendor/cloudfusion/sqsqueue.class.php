<?php
/**
 * File: Amazon SQS Queue
 * 	Queue-centric wrapper for Amazon Simple Queue Service
 *
 * Version:
 * 	2009.09.02
 *
 * Copyright:
 * 	2006-2009 Foleeo, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	CloudFusion - http://getcloudfusion.com
 * 	Amazon SQS - http://aws.amazon.com/sqs
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: SQSQUEUE_DEFAULT_ERROR
 * 	Specify the default error message.
 */
define('SQSQUEUE_DEFAULT_ERROR', 'The required queue URL was not provided in a previous action and we have NO idea which queue to execute this action on.');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: SQSQueue_Exception
 * 	Default SQS SQSQueue_Exception.
 */
class SQSQueue_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonSQSQueue
 * 	Container for all Amazon SQS-related methods. Inherits additional methods from AmazonSQS.
 *
 * Extends:
 * 	AmazonSQS
 */
class AmazonSQSQueue extends AmazonSQS
{
	/**
	 * Property: queue_name
	 * 	The queue URL to use for every request.
	 */
	var $queue_name;

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
	 * 	queue - _string_ (Optional) The NAME for the queue to revolve around. Set as null if you plan to create a new queue, as it will be auto-set.
	 * 	key - _string_ (Optional) Your Amazon API Key. If blank, it will look for the <AWS_KEY> constant.
	 * 	secret_key - _string_ (Optional) Your Amazon API Secret Key. If blank, it will look for the <AWS_SECRET_KEY> constant.
	 *
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
 	 *
	 * Examples:
	 * 	example::sqsqueue/5_send_message.phpt:
	 * 	example::sqsqueue/7_receive_message.phpt:
 	 * 	example::sqsqueue/8_delete_message.phpt:
	 */
	public function __construct($queue = null, $key = null, $secret_key = null)
	{
		$this->queue_name = $queue;
		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// QUEUES

	/**
	 * Method: create_queue()
	 * 	Identical to <AmazonSQS::create_queue()>. The queue URL created from this method will replace the queue URL already being used with this class.
	 *
	 * 	New queue URL will NOT automatically apply when using MultiCurl for parallel requests.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - See <AmazonSQS::create_queue()>.
	 * 	returnCurlHandle - See <AmazonSQS::create_queue()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/1_create_queue.phpt:
 	 * 	example::sqsqueue/1_create_queue3.phpt:
	 */
	public function create_queue($queue_name, $returnCurlHandle = null)
	{
		$data = parent::create_queue($queue_name, $returnCurlHandle);

		if ($data instanceof ResponseCore)
		{
			$this->queue_name = (string) $data->body->CreateQueueResult->QueueUrl;
		}

		return $data;
	}

	/**
	 * Method: delete_queue()
	 * 	Identical to <AmazonSQS::delete_queue()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	returnCurlHandle - See <AmazonSQS::delete_queue()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/z_delete_queue.phpt:
 	 * 	example::sqsqueue/z_delete_queue3.phpt:
	 */
	public function delete_queue($returnCurlHandle = null)
	{
		if ($this->queue_name)
		{
			return parent::delete_queue($this->queue_name, $returnCurlHandle);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}

	/**
	 * Method: get_queue_attributes()
	 * 	Identical to <AmazonSQS::get_queue_attributes()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
 	 * 	attributes - _string_|_array_ (Optional) The attribute you want to get. Setting this value to 'All' returns all the queue's attributes. Pass a string for a single attribute, or an indexed array for multiple attributes. Possible values are 'All', 'ApproximateNumberOfMessages', 'VisibilityTimeout', 'CreatedTimestamp', 'LastModifiedTimestamp', and 'Policy'. Defaults to 'All'.
	 * 	returnCurlHandle - See <AmazonSQS::get_queue_attributes()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/4_get_queue_attributes.phpt:
 	 * 	example::sqsqueue/4_get_queue_attributes4.phpt:
 	 * 	example::sqsqueue/4_get_queue_attributes5.phpt:
	 */
	public function get_queue_attributes($attributes = 'All', $returnCurlHandle = null)
	{
		if ($this->queue_name)
		{
			return parent::get_queue_attributes($this->queue_name, $attributes, $returnCurlHandle);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}

	/**
	 * Method: set_queue_attributes()
	 * 	Identical to <AmazonSQS::set_queue_attributes()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - See <AmazonSQS::set_queue_attributes()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/3_set_queue_attributes.phpt:
	 */
	public function set_queue_attributes($opt = null)
	{
		if ($this->queue_name)
		{
			return parent::set_queue_attributes($this->queue_name, $opt);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}


	/*%******************************************************************************************%*/
	// MESSAGES

	/**
	 * Method: send_message()
	 * 	Identical to <AmazonSQS::send_message()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	message - See <AmazonSQS::send_message()>.
	 * 	returnCurlHandle - See <AmazonSQS::send_message()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/5_send_message.phpt:
 	 * 	example::sqsqueue/5_send_message3.phpt:
	 */
	public function send_message($message, $returnCurlHandle = null)
	{
		if ($this->queue_name)
		{
			return parent::send_message($this->queue_name, $message, $returnCurlHandle);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}

	/**
	 * Method: receive_message()
	 * 	Identical to <AmazonSQS::receive_message()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - See <AmazonSQS::receive_message()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/7_receive_message.phpt:
 	 * 	example::sqsqueue/7_receive_message3.phpt:
 	 * 	example::sqsqueue/7_receive_message4.phpt:
 	 * 	example::sqsqueue/7_receive_message7.phpt:
 	 * 	example::sqsqueue/7_receive_message8.phpt:
	 */
	public function receive_message($opt = null)
	{
		if ($this->queue_name)
		{
			return parent::receive_message($this->queue_name, $opt);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}

	/**
	 * Method: delete_message()
	 * 	Identical to <AmazonSQS::delete_message()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	receipt_handle - See <AmazonSQS::delete_message()>.
	 * 	returnCurlHandle - See <AmazonSQS::delete_message()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/8_delete_message.phpt:
	 */
	public function delete_message($receipt_handle, $returnCurlHandle = null)
	{
		if ($this->queue_name)
		{
			return parent::delete_message($this->queue_name, $receipt_handle, $returnCurlHandle);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}

	/**
	 *
	 */
	public function change_message_visibility()
	{

	}


	/*%******************************************************************************************%*/
	// ACCESS CONTROL METHODS

	// Inherit from parent class
	// public function generate_policy() {}

	public function add_permission() {}

	public function remove_permission() {}


	/*%******************************************************************************************%*/
	// HELPER/UTILITY METHODS

	/**
	 * Method: get_queue_size()
	 * 	Identical to <AmazonSQS::get_queue_size()>, except that you don't need to pass in a queue URL.
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	_integer_ The Approximate number of messages in the queue.
 	 *
 	 * Examples:
 	 * 	example::sqsqueue/6_get_queue_size.phpt:
	 */
	public function get_queue_size()
	{
		if ($this->queue_name)
		{
			return parent::get_queue_size($this->queue_name);
		}

		throw new SQSQueue_Exception(SQSQUEUE_DEFAULT_ERROR);
	}
}
