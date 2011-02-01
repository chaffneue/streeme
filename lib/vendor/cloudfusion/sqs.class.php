<?php
/**
 * File: Amazon SQS
 * 	Amazon Simple Queue Service (http://aws.amazon.com/sqs)
 *
 * Version:
* 	2009.08.26
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
 * Constant: SQS_DEFAULT_URL
 * 	Specify the default queue URL.
 */
define('SQS_DEFAULT_URL', 'queue.amazonaws.com');

/**
 * Constant: SQS_LOCATION_US
 * 	Specify the queue URL for the U.S.-specific hostname.
 */
define('SQS_LOCATION_US', '');

/**
 * Constant: SQS_LOCATION_EU
 * 	Specify the queue URL for the E.U.-specific hostname.
 */
define('SQS_LOCATION_EU', 'eu-west-1.');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: SQS_Exception
 * 	Default SQS Exception.
 */
class SQS_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonSQS
 * 	Container for all Amazon SQS-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 */
class AmazonSQS extends CloudFusion
{
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
	 *
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
	 */
	public function __construct($key = null, $secret_key = null)
	{
		$this->api_version = '2009-02-01';
		$this->hostname = SQS_DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			throw new SQS_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new SQS_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// MISCELLANEOUS

	/**
	 * Method: set_locale()
	 * 	By default SQS will self-select the most appropriate locale. This allows you to explicitly sets the locale for SQS to use.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	locale - _string_ (Required) The locale to explicitly set for SQS. Available options are <SQS_LOCATION_US> and <SQS_LOCATION_EU>.
	 *
	 * Returns:
	 * 	void
 	 *
	 * Examples:
	 * 	example::sqs/1_create_queue3.phpt:
	 * 	example::sqs/5_send_message3.phpt:
 	 * 	example::sqs/z_delete_queue3.phpt:
	 */
	public function set_locale($locale)
	{
		$this->hostname = $locale . SQS_DEFAULT_URL;
	}


	/*%******************************************************************************************%*/
	// QUEUES

	/**
	 * Method: create_queue()
	 * 	Creates a new queue to store messages in. You must provide a queue name that is unique within the scope of the queues you own. The queue is assigned a queue URL; you must use this URL when performing actions on the queue. When you create a queue, if a queue with the same name already exists, <create_queue()> returns the queue URL with an error indicating that the queue already exists.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to use for this action. The queue name must be unique within the scope of all your queues.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/1_create_queue.phpt:
 	 * 	example::sqs/1_create_queue3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryCreateQueue.html
	 * 	Related - <delete_queue()>, <list_queues()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function create_queue($queue_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['QueueName'] = $queue_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('CreateQueue', $opt, $this->hostname);
	}

	/**
	 * Method: delete_queue()
	 * 	Deletes the queue specified by the queue URL. This will delete the queue even if it's not empty.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/z_delete_queue.phpt:
 	 * 	example::sqs/z_delete_queue3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryDeleteQueue.html
	 * 	Related - <create_queue()>, <list_queues()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function delete_queue($queue_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('DeleteQueue', $opt, $this->hostname . '/' . $queue_name);
	}

	/**
	 * Method: list_queues()
	 * 	Returns a list of your queues. A maximum 1000 queue URLs are returned. If you specify a value for the optional <queue_name_prefix> parameter, only queues with a name beginning with the specified value are returned.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name_prefix - _string_ (Optional) String to use for filtering the list results. Only those queues whose name begins with the specified string are returned.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/2_list_queues.phpt:
 	 * 	example::sqs/2_list_queues2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryListQueues.html
	 * 	Related - <create_queue()>, <delete_queue()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function list_queues($queue_name_prefix = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		if ($queue_name_prefix)
		{
			$opt['QueueNamePrefix'] = $queue_name_prefix;
		}
		return $this->authenticate('ListQueues', $opt, $this->hostname);
	}

	/**
	 * Method: get_queue_attributes()
	 * 	Gets one or all attributes of a queue.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	attributes - _string_|_array_ (Optional) The attribute you want to get. Setting this value to 'All' returns all the queue's attributes. Pass a string for a single attribute, or an indexed array for multiple attributes. Possible values are 'All', 'ApproximateNumberOfMessages', 'VisibilityTimeout', 'CreatedTimestamp', 'LastModifiedTimestamp', and 'Policy'. Defaults to 'All'.
 	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/4_get_queue_attributes.phpt:
 	 * 	example::sqs/4_get_queue_attributes4.phpt:
 	 * 	example::sqs/4_get_queue_attributes5.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html
	 * 	Related - <create_queue()>, <delete_queue()>, <list_queues()>, <set_queue_attributes()>
	 */
	public function get_queue_attributes($queue_name, $attributes = 'All', $returnCurlHandle = null)
	{
		$opt = array();

		if (is_array($attributes))
		{
			for ($i = 0, $max = count($attributes); $i < $max; $i++)
			{
				$opt['AttributeName.' . ($i + 1)] = $attributes[$i];
			}
		}
		else
		{
			$opt['AttributeName.1'] = $attributes;
		}

		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('GetQueueAttributes', $opt, $this->hostname . '/' . $queue_name);
	}

	/**
	 * Method: set_queue_attributes()
	 * 	Sets an attribute of a queue. Currently, you can set only the <VisibilityTimeout> attribute for a queue. See Visibility Timeout for more information.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	VisibilityTimeout - _integer_ (Optional) Must be an integer from 0 to 7200 (2 hours).
	 * 	Policy - _string_ (Optional) A policy generated by <generate_policy()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/3_set_queue_attributes.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html
	 * 	Related - <create_queue()>, <delete_queue()>, <list_queues()>, <get_queue_attributes()>
	 */
	public function set_queue_attributes($queue_name, $opt = null)
	{
		if (!$opt) $opt = array();

		$count = 1;

		if (isset($opt['VisibilityTimeout']))
		{
			$opt['Attribute.' . $count . '.Name'] = 'VisibilityTimeout';
			$opt['Attribute.' . $count . '.Value'] = $opt['VisibilityTimeout'];
			unset($opt['VisibilityTimeout']);
			$count++;
		}

		if (isset($opt['Policy']))
		{
			$opt['Attribute.' . $count . '.Name'] = 'Policy';
			$opt['Attribute.' . $count . '.Value'] = $opt['Policy'];
			unset($opt['VisibilityTimeout']);
			$count++;
		}

		return $this->authenticate('SetQueueAttributes', $opt, $this->hostname . '/' . $queue_name);
	}


	/*%******************************************************************************************%*/
	// MESSAGES

	/**
	 * Method: send_message()
	 * 	Delivers a message to the specified queue.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	message - _string_ (Required) Message size cannot exceed 8 KB. Allowed Unicode characters (according to http://www.w3.org/TR/REC-xml/#charsets): #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF].
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/5_send_message.phpt:
 	 * 	example::sqs/5_send_message3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QuerySendMessage.html
	 * 	Related - <send_message()>, <receive_message()>, <delete_message()>, <change_message_visibility()>
	 */
	public function send_message($queue_name, $message, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('SendMessage', $opt, $this->hostname . '/' . $queue_name, $message);
	}

	/**
	 * Method: receive_message()
	 * 	Retrieves one or more messages from the specified queue, including the message body and message ID of each message. Messages returned by this action stay in the queue until you delete them. However, once a message is returned to a <receive_message()> request, it is not returned on subsequent <receive_message()> requests for the duration of the <VisibilityTimeout>. If you do not specify a <VisibilityTimeout> in the request, the overall visibility timeout for the queue is used for the returned messages. A default visibility timeout of 30 seconds is set when you create the queue. You can also set the visibility timeout for the queue by using <set_queue_attributes()>. See Visibility Timeout for more information.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	AttributeName - _string_|_array_ (Optional) The attribute you want to get. Pass a string for a single attribute, or an indexed array for multiple attributes. Possible values are 'SenderId' and 'SentTimestamp'.
	 * 	VisibilityTimeout - _integer_ (Optional) Must be an integer from 0 to 7200 (2 hours).
	 * 	MaxNumberOfMessages - _integer_ (Optional) Maximum number of messages to return, from 1 to 10. Not necessarily all the messages in the queue are returned. If there are fewer messages in the queue than <MaxNumberOfMessages>, the maximum number of messages returned is the current number of messages in the queue. Defaults to 1 message.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/7_receive_message.phpt:
 	 * 	example::sqs/7_receive_message3.phpt:
 	 * 	example::sqs/7_receive_message4.phpt:
 	 * 	example::sqs/7_receive_message7.phpt:
 	 * 	example::sqs/7_receive_message8.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryReceiveMessage.html
	 * 	Related - <send_message()>, <receive_message()>, <delete_message()>, <change_message_visibility()>
	 */
	public function receive_message($queue_name, $opt = null)
	{
		if (!$opt) $opt = array();

		if (isset($opt['AttributeName']))
		{
			if (is_array($opt['AttributeName']))
			{
				for ($i = 0, $max = count($opt['AttributeName']); $i < $max; $i++)
				{
					$opt['AttributeName.' . ($i + 1)] = $opt['AttributeName'][$i];
				}
			}
			else
			{
				$opt['AttributeName.1'] = $opt['AttributeName'];
			}

			unset($opt['AttributeName']);
		}

		return $this->authenticate('ReceiveMessage', $opt, $this->hostname . '/' . $queue_name);
	}

	/**
	 * Method: delete_message()
	 * 	Unconditionally removes the specified message from the specified queue. Even if the message is locked by another reader due to the visibility timeout setting, it is still deleted from the queue.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	receipt_handle - _string_ (Required) The receipt handle of the message to delete, returned by <receive_message()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sqs/8_delete_message.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/Query_QueryDeleteMessage.html
	 * 	Related - <send_message()>, <receive_message()>, <delete_message()>, <change_message_visibility()>
	 */
	public function delete_message($queue_name, $receipt_handle, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ReceiptHandle'] = $receipt_handle;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('DeleteMessage', $opt, $this->hostname . '/' . $queue_name);
	}

	/**
	 * Method: change_message_visibility()
	 * 	Changes the visibility timeout of a specified message in a queue to a new value. The maximum allowed timeout value you can set the value to is 12 hours. This means you can't extend the timeout of a message in an existing queue to more than a total visibility timeout of 12 hours.
	 *
	 * 	For example, let's say you have a message and its default message visibility timeout is 30 minutes. You could call ChangeMessageVisiblity with a value of two hours and the effective timeout would be two hours and 30 minutes. When that time comes near you could again extend the time out by calling ChangeMessageVisiblity, but this time the maximum allowed timeout would be 9 hours and 30 minutes.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	receipt_handle - _string_ (Required) The receipt handle associated with the message whose visibility timeout you want to change. This parameter is returned by <receive_message()>.
	 * 	visibility_timeout - _string_ (Required) The new value for the message's visibility timeout (in seconds). This value is limited to 43200 seconds (12 hours).
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/index.html?Query_QueryChangeMessageVisibility.html
	 * 	Related - <send_message()>, <receive_message()>, <delete_message()>, <change_message_visibility()>
	 */
	public function change_message_visibility($receipt_handle, $visibility_timeout, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ReceiptHandle'] = $receipt_handle;
		$opt['VisibilityTimeout'] = $visibility_timeout;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('ChangeMessageVisibility', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// ACCESS CONTROL METHODS

	/**
	 *
	 */
	public function generate_policy()
	{

	}

	/**
	 * Method: add_permission()
	 * 	Adds a permission to a queue for a specific principal. This allows for sharing access to the queue. When you create a queue, you have full control access rights for the queue. Only you (as owner of the queue) can grant or deny permissions to the queue.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
 	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 * 	label - _string_ (Required) The unique identification of the permission you're setting. Maximum 80 characters; alphanumeric characters, hyphens (-), and underscores (_) are allowed.
	 * 	permissions - _array_ (Required) An associative array of AWS account numbers (key) and the actions they're allowed to execute (value). AWS account numbers are for those who will be given permission. Actions can be passed as a string for a single action, or an indexed array for multiple actions. Valid values are '*', 'SendMessage', 'ReceiveMessage', 'DeleteMessage', 'ChangeMessageVisibility', or 'GetQueueAttributes'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
	 * 	example::sqs/add_permission.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/index.html?Query_QueryAddPermission.html
	 * 	Related - <add_permission()>, <remove_permission()>, <generate_policy()>
	 */
	public function add_permission($queue_name, $label, $permissions, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['Label'] = $label;

		// Starting point.
		$count = 1;

		// Parse through the array
		if (is_array($permissions))
		{
			foreach ($permissions as $account_id => $actions)
			{
				if (is_array($actions))
				{
					foreach ($actions as $action)
					{
						$opt['AWSAccountId.' . $count] = $account_id;
						$opt['ActionName.' . $count] = $action;

						$count++;
					}
				}
				else
				{
					$opt['AWSAccountId.1'] = $account_id;
					$opt['ActionName.1'] = $actions;
				}

				$count++;
			}
		}
		else
		{
			throw new SQS_Exception('$permissions MUST be an associative array of AWS Account IDs and Actions they\'re allowed to execute.');
		}

		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('AddPermission', $opt, $this->hostname . '/' . $queue_name);
	}

	/**
	 * Method: remove_permission()
	 * 	Revokes any permissions in the queue policy that matches the Label parameter. Only the owner of the queue can remove permissions.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	label - _string_ (Required) This should match the label you set in <add_permission()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/index.html?Query_QueryRemovePermission.html
	 * 	Related - <add_permission()>, <remove_permission()>, <generate_policy()>
	 */
	public function remove_permission($label, $returnCurlHandle = null)
	{
		if (!$opt) $opt = array();
		$opt['Label'] = $label;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('RemovePermission', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// HELPER/UTILITY METHODS

	/**
	 * Method: get_queue_size()
	 * 	Retrieves the approximate number of messages in the queue.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to perform the action on.
	 *
	 * Returns:
	 * 	_integer_ The Approximate number of messages in the queue.
 	 *
 	 * Examples:
 	 * 	example::sqs/6_get_queue_size.phpt:
 	 *
	 * See Also:
	 * 	Related - <get_queue_attributes()>
	 */
	public function get_queue_size($queue_name)
	{
		$opt = array();
		$opt['AttributeName'] = 'ApproximateNumberOfMessages';
		$response = $this->authenticate('GetQueueAttributes', $opt, $this->hostname . '/' . $queue_name);

		if ($response->isOK() === false)
		{
			throw new SQS_Exception("Could not get queue size for $queue_name: " . $response->body->Error->Code);
		}

		return (integer) $response->body->GetQueueAttributesResult->Attribute->Value;
	}
}
