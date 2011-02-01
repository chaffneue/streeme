<?php
/**
 * File: Amazon SDB
 * 	Amazon SimpleDB Service (http://aws.amazon.com/simpledb)
 *
 * Version:
 * 	2009.08.23
 *
 * Copyright:
 * 	2006-2009 Foleeo, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	CloudFusion - http://getcloudfusion.com
 * 	Amazon SDB - http://aws.amazon.com/simpledb
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: SDB_DEFAULT_URL
 * 	Specify the default queue URL.
 */
define('SDB_DEFAULT_URL', 'sdb.amazonaws.com');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: SDB_Exception
 * 	Default SDB Exception.
 */
class SDB_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonSDB
 * 	Container for all Amazon SimpleDB-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 */
class AmazonSDB extends CloudFusion
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
		$this->api_version = '2009-04-15';
		$this->hostname = SDB_DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			throw new SDB_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new SDB_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// DOMAIN

	/**
	 * Method: create_domain()
	 * 	Creates a new domain. The domain name must be unique among the domains associated with the Access Key ID provided in the request. The CreateDomain operation might take 10 or more seconds to complete.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for storing data.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/1_create_domain.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_CreateDomain.html
 	 * 	Related - <create_domain()>, <list_domains()>, <delete_domain()>, <domain_metadata()>
	 */
	public function create_domain($domain_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('CreateDomain', $opt, $this->hostname);
	}

	/**
	 * Method: list_domains()
	 * 	Lists all domains associated with the Access Key ID. It returns domain names up to the limit set by MaxNumberOfDomains. A NextToken is returned if there are more than MaxNumberOfDomains domains. Calling ListDomains successive times with the NextToken returns up to MaxNumberOfDomains more domain names each time.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	MaxNumberOfDomains - _integer_ (Optional) The maximum number of domain names you want returned. The range is 1 to 100.
	 * 	NextToken - _string_ (Optional) String that tells Amazon SimpleDB where to start the next list of domain names.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/list_domains.phpt:
 	 * 	example::sdb/list_domains2.phpt:
 	 * 	example::sdb/list_domains3.phpt:
 	 * 	example::sdb/list_domains5.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_ListDomains.html
 	 * 	Related - <create_domain()>, <list_domains()>, <delete_domain()>, <domain_metadata()>
	 */
	public function list_domains($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('ListDomains', $opt, $this->hostname);
	}

	/**
	 * Method: delete_domain()
	 * 	Deletes a domain. Any items (and their attributes) in the domain are deleted as well. The DeleteDomain operation might take 10 or more seconds to complete. Running DeleteDomain on a domain that does not exist or running the function multiple times using the same domain name will not result in an error response.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to delete.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/delete_attributes.phpt:
 	 * 	example::sdb/delete_attributes2.phpt:
 	 * 	example::sdb/delete_attributes3.phpt:
 	 * 	example::sdb/delete_attributes4.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_DeleteDomain.html
 	 * 	Related - <create_domain()>, <list_domains()>, <delete_domain()>, <domain_metadata()>
	 */
	public function delete_domain($domain_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DeleteDomain', $opt, $this->hostname);
	}

	/**
	 * Method: domain_metadata()
	 * 	Returns information about the domain, including when the domain was created, the number of items and attributes, and the size of attribute names and values.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for retrieving metadata.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/domain_metadata.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_DomainMetadata.html
 	 * 	Related - <create_domain()>, <list_domains()>, <delete_domain()>, <domain_metadata()>
	 */
	public function domain_metadata($domain_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		return $this->authenticate('DomainMetadata', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// ATTRIBUTES

	/**
	 * Method: put_attributes()
	 * 	Creates or replaces attributes in an item. You specify new attributes using a combination of the Attribute.X.Name and Attribute.X.Value parameters.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for storing data.
	 * 	item_name - _string_ (Required) The name of the base item which will contain the series of keypairs.
	 * 	keypairs - _array_ (Required) Associative array of parameters which are treated as key-value and key-multivalue pairs (i.e. a key can have one or more values; think tags).
	 * 	replace - _boolean|array_ (Optional) Whether to replace a key-value pair if a matching key already exists. Supports either a boolean (which affects ALL key-value pairs) or an indexed array of key names (which affects only the keys specified). Defaults to boolean false.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/put_attributes.phpt:
 	 * 	example::sdb/put_attributes2.phpt:
 	 * 	example::sdb/put_attributes5.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_PutAttributes.html
 	 * 	Related - <get_attributes()>, <delete_attributes()>
	 */
	public function put_attributes($domain_name, $item_name, $keypairs, $replace = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		$rstore = array();

		// Start looping through the keypairs.
		$count = 0;
		foreach ($keypairs as $k => $v)
		{
			// Is one of the values an array?
			if (is_array($v))
			{
				// Loop through each of them so that all values are passed as individual attributes.
				foreach ($v as $va)
				{
					$opt['Attribute.' . (string) $count . '.Name'] = $k;
					$opt['Attribute.' . (string) $count . '.Value'] = $va;

					// Do we want to do replacement?
					if ($replace)
					{
						// Do we have an associative array of key names?
						if (is_array($replace))
						{
							// Store this key-index pair for later.
							$rstore[] = array(
								'count' => $count,
								'key' => $k
							);
						}
						// Or just a since REPLACE ALL?
						else
						{
							$opt['Attribute.' . (string) $count . '.Replace'] = 'true';
						}
					}

					// Increment
					$count++;
				}
			}
			else
			{
				$opt['Attribute.' . (string) $count . '.Name'] = $k;
				$opt['Attribute.' . (string) $count . '.Value'] = $v;

				// Do we want to do replacement?
				if ($replace)
				{
					// Do we have an associative array of key names?
					if (is_array($replace))
					{
						// Store this key-index pair for later.
						$rstore[] = array(
							'count' => $count,
							'key' => $k
						);
					}
					// Or just a since REPLACE ALL?
					else
					{
						$opt['Attribute.' . (string) $count . '.Replace'] = 'true';
					}
				}
			}

			// Increment
			$count++;
		}

		// Go through all of the saved key-index pairs we saved earlier.
		foreach ($rstore as $k => $store)
		{
			// Did we want to replace one of these keypairs?
			if (in_array($store['key'], $replace))
			{
				// Replace!
				$opt['Attribute.' . (string) $store['count'] . '.Replace'] = 'true';
			}
		}

		return $this->authenticate('PutAttributes', $opt, $this->hostname);
	}

	/**
	 * Method: batch_put_attributes()
	 * 	Creates or replaces attributes in an item. You specify new attributes using a combination of the Attribute.X.Name and Attribute.X.Value parameters.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for storing data.
	 * 	item_keypairs - _array_ (Required) Associative array of parameters which are treated as item-key-value and item-key-multivalue pairs (i.e. a key can have one or more values; think tags).
	 * 	replace - _boolean|array_ (Optional) Whether to replace a key-value pair if a matching key already exists. Supports either a boolean (which affects ALL key-value pairs) or an indexed array of key names (which affects only the keys specified). Defaults to boolean false.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/batch_put_attributes.phpt:
 	 * 	example::sdb/batch_put_attributes2.phpt:
 	 * 	example::sdb/batch_put_attributes4.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_PutAttributes.html
 	 * 	Related - <get_attributes()>, <delete_attributes()>
	 */
	public function batch_put_attributes($domain_name, $item_keypairs, $replace = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		$is_replace_an_array = is_array($replace); // Cache this value

		// Start looping through the item-keypairs
		$item_count = 0;
		foreach ($item_keypairs as $item => $keypairs)
		{
			// Clear these for re-use
			unset($rstore);
			$rstore = array();

			// Set the item name
			$opt['Item.' . (string) $item_count . '.ItemName'] = $item;

			// Start looping through the keypairs.
			$count = 0;
			foreach ($keypairs as $k => $v)
			{
				// Is one of the values an array?
				if (is_array($v))
				{
					// Loop through each of them so that all values are passed as individual attributes.
					foreach ($v as $va)
					{
						$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Name'] = $k;
						$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Value'] = $va;

						// Do we want to do replacement?
						if ($replace)
						{
							// Do we have an array of key names?
							if ($is_replace_an_array)
							{
								// Store this key-index pair for later.
								$rstore[] = array(
									'count' => $count,
									'key' => $k
								);
							}
							// Or just a since REPLACE ALL?
							else
							{
								$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Replace'] = 'true';
							}
						}

						// Increment
						$count++;
					}
				}
				else
				{
					$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Name'] = $k;
					$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Value'] = $v;

					// Do we want to do replacement?
					if ($replace)
					{
						// Do we have an array of key names?
						if ($is_replace_an_array)
						{
							// Store this key-index pair for later.
							$rstore[] = array(
								'count' => $count,
								'key' => $k
							);
						}
						// Or just a since REPLACE ALL?
						else
						{
							$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $count . '.Replace'] = 'true';
						}
					}
				}

				// Increment
				$count++;
			}

			// Go through all of the saved key-index pairs we saved earlier.
			foreach ($rstore as $k => $store)
			{
				if (isset($replace[$item]))
				{
					// Did we want to replace one of these keypairs?
					if (in_array($store['key'], $replace[$item]))
					{
						// Replace!
						$opt['Item.' . (string) $item_count . '.Attribute.' . (string) $store['count'] . '.Replace'] = 'true';
					}
				}
			}

			// Increment
			$item_count++;
		}

		return $this->authenticate('BatchPutAttributes', $opt, $this->hostname);
	}

	/**
	 * Method: get_attributes()
	 * 	Returns all of the attributes associated with the item. Optionally, the attributes returned can be limited to one or more specified attribute name parameters. If the item does not exist on the replica that was accessed for this operation, an empty set is returned. The system does not return an error as it cannot guarantee the item does not exist on other replicas.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for storing data.
	 * 	item_name - _string_ (Required) The name of the base item which will contain the series of keypairs.
	 * 	keys - _string|array_ (Optional) The name of the key (attribute) in the key-value pair that you want to return. Supports a string value (for single keys) or an indexed array (for multiple keys).
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/get_attributes.phpt:
 	 * 	example::sdb/get_attributes2.phpt:
 	 * 	example::sdb/get_attributes5.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_GetAttributes.html
 	 * 	Related - <put_attributes()>, <delete_attributes()>
	 */
	public function get_attributes($domain_name, $item_name, $keys = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		if ($keys)
		{
			if (is_array($keys))
			{
				$count = 0;
				foreach ($keys as $key)
				{
					$opt['AttributeName.' . (string) $count] = $key;
					$count++;
				}
			}
			else
			{
				$opt['AttributeName'] = $keys;
			}
		}

		return $this->authenticate('GetAttributes', $opt, $this->hostname);
	}

	/**
	 * Method: delete_attributes()
	 * 	Deletes one or more attributes associated with the item. If all attributes of an item are deleted, the item is deleted. If you specify DeleteAttributes without attributes or values, all the attributes for the item are deleted. DeleteAttributes is an idempotent operation; running it multiple times on the same item or attribute does not result in an error response.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	domain_name - _string_ (Required) The domain name to use for storing data.
	 * 	item_name - _string_ (Required) The name of the base item which will contain the series of keypairs.
	 * 	keys - _string|array_ (Optional) The name of the key (attribute) in the key-value pair that you want to delete. Supports a string value (for single keys), an indexed array (for multiple keys), or an associative array containing one or more key-value pairs (for deleting specific values).
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/delete_attributes.phpt:
 	 * 	example::sdb/delete_attributes2.phpt:
 	 * 	example::sdb/delete_attributes3.phpt:
 	 * 	example::sdb/delete_attributes4.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_DeleteAttributes.html
 	 * 	Related - <put_attributes()>, <get_attributes()>
	 */
	public function delete_attributes($domain_name, $item_name, $keys = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Do we have a key?
		if ($keys)
		{
			// An array?
			if (is_array($keys))
			{
				// Indexed array of Attribute Names?
				if (isset($keys[0]) && !empty($keys[0]))
				{
					for ($x = 0, $i = count($keys); $x < $i; $x++)
					{
						$opt['Attribute.' . (string) $x . '.Name'] = $keys[$x];
					}
				}

				// Associative array of Name/Value pairs.
				else
				{
					$count = 0;
					foreach ($keys as $k => $v)
					{
						$opt['Attribute.' . (string) $count . '.Name'] = $k;
						$opt['Attribute.' . (string) $count . '.Value'] = $v;
						$count++;
					}
				}
			}

			// Single string Attribute Name.
			else
			{
				$opt['Attribute.Name'] = $keys;
			}
		}

		return $this->authenticate('DeleteAttributes', $opt, $this->hostname);
	}


	/*%******************************************************************************************%*/
	// SELECT

	/**
	 * Method: select()
	 * 	The Select operation returns a set of Attributes for ItemNames that match the query expression. Select is similar to the standard SQL SELECT statement.
	 *
	 * 	The total size of the response cannot exceed 1 MB in total size. Amazon SimpleDB automatically adjusts the number of items returned per page to enforce this limit. For example, even if you ask to retrieve 250 items, but each individual item is 100 kB in size, the system returns 10 items and an appropriate next token so you can get the next page of results.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	expression - _string_ (Optional) The SimpleDB select expression to use.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	NextToken - _string_ (Optional) String that tells Amazon SimpleDB where to start the next list of domain names.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
 	 * Examples:
 	 * 	example::sdb/select.phpt:
 	 * 	example::sdb/select2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonSimpleDB/latest/DeveloperGuide/SDB_API_Select.html
 	 * 	Related - <select()>
	 */
	public function select($expression, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SelectExpression'] = $expression;

		$query = $this->authenticate('Select', $opt, $this->hostname);

		return $query;
	}
}
