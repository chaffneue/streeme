<?php
/**
 * File: Amazon S3
 * 	Amazon Simple Storage Service (http://aws.amazon.com/s3)
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
 * 	Amazon S3 - http://aws.amazon.com/s3
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: S3_LOCATION_US
 * 	Specify the US location.
 */
define('S3_DEFAULT_URL', 's3.amazonaws.com');

/**
 * Constant: S3_LOCATION_US
 * 	Specify the US location.
 */
define('S3_LOCATION_US', 'us');

/**
 * Constant: S3_LOCATION_EU
 * 	Specify the European Union (EU) location.
 */
define('S3_LOCATION_EU', 'eu');

/**
 * Constant: S3_ACL_PRIVATE
 * 	ACL: Owner-only read/write.
 */
define('S3_ACL_PRIVATE', 'private');

/**
 * Constant: S3_ACL_PUBLIC
 * 	ACL: Owner read/write, public read.
 */
define('S3_ACL_PUBLIC', 'public-read');

/**
 * Constant: S3_ACL_OPEN
 * 	ACL: Public read/write.
 */
define('S3_ACL_OPEN', 'public-read-write');

/**
 * Constant: S3_ACL_AUTH_READ
 * 	ACL: Owner read/write, authenticated read.
 */
define('S3_ACL_AUTH_READ', 'authenticated-read');

/**
 * Constant: S3_GRANT_READ
 * 	When applied to a bucket, grants permission to list the bucket. When applied to an object, this grants permission to read the object data and/or metadata.
 */
define('S3_GRANT_READ', 'READ');

/**
 * Constant: S3_GRANT_WRITE
 * 	When applied to a bucket, grants permission to create, overwrite, and delete any object in the bucket. This permission is not supported for objects.
 */
define('S3_GRANT_WRITE', 'WRITE');

/**
 * Constant: S3_GRANT_READ_ACP
 * 	Grants permission to read the ACL for the applicable bucket or object. The owner of a bucket or object always has this permission implicitly.
 */
define('S3_GRANT_READ_ACP', 'READ_ACP');

/**
 * Constant: S3_GRANT_WRITE_ACP
 * 	Gives permission to overwrite the ACP for the applicable bucket or object. The owner of a bucket or object always has this permission implicitly. Granting this permission is equivalent to granting FULL_CONTROL because the grant recipient can make any changes to the ACP.
 */
define('S3_GRANT_WRITE_ACP', 'WRITE_ACP');

/**
 * Constant: S3_GRANT_FULL_CONTROL
 * 	Provides READ, WRITE, READ_ACP, and WRITE_ACP permissions. It does not convey additional rights and is provided only for convenience.
 */
define('S3_GRANT_FULL_CONTROL', 'FULL_CONTROL');

/**
 * Constant: S3_USERS_AUTH
 * 	The "AuthenticatedUsers" group for access control policies.
 */
define('S3_USERS_AUTH', 'http://acs.amazonaws.com/groups/global/AuthenticatedUsers');

/**
 * Constant: S3_USERS_ALL
 * 	The "AllUsers" group for access control policies.
 */
define('S3_USERS_ALL', 'http://acs.amazonaws.com/groups/global/AllUsers');

/**
 * Constant: S3_USERS_LOGGING
 * 	The "LogDelivery" group for access control policies.
 */
define('S3_USERS_LOGGING', 'http://acs.amazonaws.com/groups/s3/LogDelivery');

/**
 * Constant: S3_PCRE_ALL
 * 	PCRE: Match all items
 */
define('S3_PCRE_ALL', '/.*/i');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: S3_Exception
 * 	Default S3 Exception.
 */
class S3_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonS3
 * 	Container for all Amazon S3-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 */
class AmazonS3 extends CloudFusion
{
	/**
	 * Property: request_url
	 * 	The request URL.
	 */
	var $request_url;

	/**
	 * Property: vhost
	 * 	The virtual host setting.
	 */
	var $vhost;

	/**
	 * Property: base_acp_xml
	 * 	The base XML elements to use for access control policy methods.
	 */
	var $base_acp_xml;

	/**
	 * Property: base_logging_xml
	 * 	The base XML elements to use for logging methods.
	 */
	var $base_logging_xml;


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
		$this->vhost = null;
		$this->api_version = '2006-03-01';
		$this->hostname = S3_DEFAULT_URL;

		$this->base_acp_xml = '<?xml version="1.0" encoding="UTF-8"?><AccessControlPolicy xmlns="http://s3.amazonaws.com/doc/latest/"></AccessControlPolicy>';
		$this->base_logging_xml = '<?xml version="1.0" encoding="utf-8"?><BucketLoggingStatus xmlns="http://doc.s3.amazonaws.com/' . $this->api_version . '"></BucketLoggingStatus>';

		if (!$key && !defined('AWS_KEY'))
		{
			throw new S3_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new S3_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// AUTHENTICATION

	/**
	 * Method: authenticate()
	 * 	Authenticates a connection to S3. This should not be used directly unless you're writing custom methods for this class.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	opt - _array_ (Optional) Associative array of parameters for authenticating. See the individual methods for allowed keys.
	 * 	location - _string_ (Do Not Use) Used internally by this function on occasions when S3 returns a redirect code and it needs to call itself recursively.
	 * 	redirects - _integer_ (Do Not Use) Used internally by this function on occasions when S3 returns a redirect code and it needs to call itself recursively.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	http://docs.amazonwebservices.com/AmazonS3/latest/RESTAuthentication.html
	 */
	public function authenticate($bucket, $opt = null, $location = null, $redirects = 0)
	{
		// If nothing was passed in, don't do anything.
		if (!$opt)
		{
			return false;
		}
		else
		{
			// Set default values
			$bucket = strtolower($bucket);
			$acl = null;
			$body = null;
			$contentType = 'application/x-www-form-urlencoded';
			$delimiter = null;
			$filename = null;
			$headers = null;
			$marker = null;
			$maxKeys = null;
			$method = null;
			$prefix = null;
			$verb = null;
			$lastmodified = null;
			$etag = null;
			$qsa = null;
			$md5 = null;
			$metadataDirective = null;
			$meta = null;
			$hmeta = null;
			$range = null;
			$returnCurlHandle = null;

			// Break the array into individual variables, while storing the original.
			$_opt = $opt;
			extract($opt);

			$filename = rawurlencode($filename);

			// Set hostname
			if ($this->vhost)
			{
				$hostname = $this->vhost;
			}
			elseif ($method == 'list_buckets')
			{
				$hostname = $this->hostname;
			}
			else
			{
				$hostname = $bucket . '.' . $this->hostname;
			}

			// Get the UTC timestamp in RFC 2616 format
			$since_epoch = time() + $this->adjust_offset;
			$httpDate = gmdate(DATE_FORMAT_RFC2616, $since_epoch);

			// Generate the request string
			$request = '';

			// Append additional parameters
			$request .= '/' . $filename;

			// List Object settings
			if ($method == 'list_objects')
			{
				$request = '';

				if (isset($prefix) && !empty($prefix))
				{
					$request .= '&prefix=' . $prefix;
				}

				if (isset($marker) && !empty($marker))
				{
					$request .= '&marker=' . $marker;
				}

				if (isset($maxKeys) && !empty($maxKeys))
				{
					$request .= '&max-keys=' . $maxKeys;
				}

				if (isset($delimiter) && !empty($delimiter))
				{
					$request .= '&delimiter=' . $delimiter;
				}

				$request = '/?' . ltrim($request, '&');
			}

			// Logging
			elseif ($method == 'get_logs' || $method == 'enable_logging' || $method == 'disable_logging')
			{
				$request .= '?logging';
				$filename .= '?logging';
			}

			// Get Bucket Locale settings
			elseif ($method == 'get_bucket_locale')
			{
				$request = '/?location';
				$filename = '?location';
			}

			// Add ACL stuff if we're getting/setting ACL preferences.
			elseif ($method == 'get_bucket_acl' || $method == 'get_object_acl' || $method == 'set_bucket_acl' || $method == 'set_object_acl')
			{
				$request .= '?acl';
				$filename .= '?acl';
			}

			elseif ($method == 'get_object_url')
			{
				$filename = rawurldecode($filename);
			}

			if (!$request == '/')
			{
				$request = '/' . $request;
			}

			// Prepare the request.
			if ($location)
			{
				$this->request_url = $location;
			}
			else
			{
				$scheme = ($this->enable_ssl) ? 'https://' : 'http://';
				$this->request_url = $scheme . $hostname . $request;
			}

			// Instantiate the request class
			$req = new $this->request_class($this->request_url, $this->set_proxy);

			// Do we have a verb?
			if (isset($verb) && !empty($verb))
			{
				$req->set_method($verb);
			}

			// Do we have a contentType?
			if (isset($contentType) && !empty($contentType))
			{
				$req->add_header('Content-Type', $contentType);
			}

			// Do we have a date?
			if (isset($httpDate) && !empty($httpDate))
			{
				$req->add_header("Date", $httpDate);
			}

			// Do we have ACL settings? (Optional in signed string)
			if (isset($acl) && !empty($acl))
			{
				$req->add_header("x-amz-acl", $acl);
				$acl = 'x-amz-acl:' . $acl . "\n";
			}

			// Do we have COPY settings?
			if ($method == 'copy_object' || $method == 'update_object')
			{
				// Copy data
				$acl .= 'x-amz-copy-source:/' . $sourceBucket . '/' . $sourceObject . "\n";
				$req->add_header('x-amz-copy-source', '/' . $sourceBucket . '/' . $sourceObject);

				// Add any standard HTTP headers.
				if ($headers)
				{
					uksort($headers, 'strnatcasecmp');

					foreach ($headers as $k => $v)
					{
						$req->add_header($k, $v);
					}
				}

				// Add any meta headers.
				if ($meta)
				{
					uksort($meta, 'strnatcasecmp');

					foreach ($meta as $k => $v)
					{
						$req->add_header('x-amz-meta-' . strtolower($k), $v);
						$acl .= 'x-amz-meta-' . strtolower($k) . ':' . $v . "\n";
					}
				}

				// Metadata directive
				$acl .= 'x-amz-metadata-directive:' . $metadataDirective . "\n";
				$req->add_header('x-amz-metadata-directive', $metadataDirective);
			}

			// Set DevPay tokens if we have them.
			if ($this->devpay_tokens)
			{
				$request->add_header('x-amz-security-token', $this->devpay_tokens);
			}

			// Are we checking for changes?
			if ($lastmodified && $etag)
			{
				$req->add_header('If-Modified-Since', $lastmodified);
				$req->add_header('If-None-Match', $etag);
			}

			// Partial content range
			if ($range)
			{
				$req->add_header('Range', 'bytes=' . $range);
			}

			// Add a body if we're creating or setting
			if ($method == 'create_object' || $method == 'create_bucket' ||
				$method == 'enable_logging' || $method == 'disable_logging' ||
				$method == 'set_object_acl' || $method == 'set_bucket_acl')
			{
				if (isset($body) && !empty($body))
				{
					$req->set_body($body);
					$md5 = $this->util->hex_to_base64(md5($body));
					$req->add_header('Content-MD5', $md5);
				}

				// Add any standard HTTP headers.
				if ($headers)
				{
					uksort($headers, 'strnatcasecmp');

					foreach ($headers as $k => $v)
					{
						$req->add_header($k, $v);
					}
				}

				// Add any meta headers.
				if ($meta)
				{
					uksort($meta, 'strnatcasecmp');

					foreach ($meta as $k => $v)
					{
						$req->add_header('x-amz-meta-' . strtolower($k), $v);
						$hmeta .= 'x-amz-meta-' . strtolower($k) . ':' . $v . "\n";
					}
				}
			}

			// Data that will be "signed".
			$filename = '/' . $filename;

			// If we're listing buckets, there is no filename value.
			if ($method == 'list_buckets')
			{
				$filename = '';
			}

			if ($qsa)
			{
				// Prepare the string to sign
				$stringToSign = "$verb\n$md5\n$contentType\n$since_epoch\n$acl$hmeta/$bucket$filename";
			}
			else
			{
				// Prepare the string to sign
				$stringToSign = "$verb\n$md5\n$contentType\n$httpDate\n$acl$hmeta/$bucket$filename";
			}

			// Hash the AWS secret key and generate a signature for the request.
			$signature = $this->util->hex_to_base64(hash_hmac('sha1', $stringToSign, $this->secret_key));

			// Pass the developer key and signature
			$req->add_header("Authorization", "AWS " . $this->key . ":" . $signature);

			// If we have a "true" value for returnCurlHandle, do that instead of completing the request.
			if ($returnCurlHandle)
			{
				return $req->prep_request();
			}

			// Are we getting a Query String Auth?
			if ($qsa)
			{
				return array(
					'bucket' => $bucket,
					'filename' => $filename,
					'key' => $this->key,
					'expires' => $since_epoch,
					'signature' => $signature,
				);
			}

			// Send!
			$req->send_request();

			// Prepare the response.
			$headers = $req->get_response_header();
			$headers['x-tarzan-redirects'] = $redirects;
			$headers['x-tarzan-requesturl'] = $this->request_url;
			$headers['x-tarzan-stringtosign'] = $stringToSign;
			$headers['x-tarzan-requestheaders'] = $req->request_headers;

			if (strpos($req->get_response_body(), '<?xml') !== false)
			{
				$data = new $this->response_class($headers, new SimpleXMLElement($req->get_response_body()), $req->get_response_code());
			}
			else
			{
				$data = new $this->response_class($headers, $req->get_response_body(), $req->get_response_code());
			}

			// Did Amazon tell us to redirect? Typically happens for multiple rapid requests EU datacenters.
			// @see http://docs.amazonwebservices.com/AmazonS3/latest/Redirects.html
			if ((int) $req->get_response_code() == 307) // Temporary redirect to new endpoint.
			{
				$redirects++;
				$data = $this->authenticate($bucket,
					$_opt,
					$headers['location'],
					$redirects);
			}

			// Return!
			return $data;
		}
	}

	/**
	 * Method: set_vhost()
	 * 	Use this virtual host instead of the normal bucket.s3.amazonaws.com domain.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	vhost - _string_ (Required) The hostname to use instead of bucket.s3.amazonaws.com.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Virtual Hosting of Buckets - http://docs.amazonwebservices.com/AmazonS3/latest/VirtualHosting.html
	 */
	public function set_vhost($vhost)
	{
		$this->vhost = $vhost;
	}


	/*%******************************************************************************************%*/
	// BUCKET METHODS

	/**
	 * Method: create_bucket()
	 * 	The bucket holds all of your objects, and provides a globally unique namespace in which you can manage the keys that identify objects. A bucket can hold any number of objects.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	locale - _string_ (Optional) Sets the preferred geographical location for the bucket. Accepts S3_LOCATION_US or S3_LOCATION_EU. Defaults to S3_LOCATION_US.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTBucketPUT.html
	 * 	Using Buckets - http://docs.amazonwebservices.com/AmazonS3/latest/UsingBucket.html
	 * 	Related - <get_bucket()>, <head_bucket()>, <delete_bucket()>
	 */
	public function create_bucket($bucket, $locale = null, $returnCurlHandle = null)
	{
		// Defaults
		$body = null;
		$contentType = null;

		if ($locale)
		{
			switch(strtolower($locale))
			{
				case 'eu':
					$body = '<CreateBucketConfiguration><LocationConstraint>' . strtoupper($locale) . '</LocationConstraint></CreateBucketConfiguration>';
					$contentType = 'application/xml';
					break;

				default:
					$body = '<CreateBucketConfiguration><LocationConstraint>US</LocationConstraint></CreateBucketConfiguration>';
					$contentType = 'application/xml';
					break;
			}
		}
		else
		{
			$body = '<CreateBucketConfiguration><LocationConstraint>US</LocationConstraint></CreateBucketConfiguration>';
			$contentType = 'application/xml';
		}

		// Authenticate to S3
		return $this->authenticate($bucket, array(
			'verb' => HTTP_PUT,
			'method' => 'create_bucket',
			'body' => $body,
			'contentType' => $contentType,
			'returnCurlHandle' => $returnCurlHandle
		));
	}

	/**
	 * Method: get_bucket()
	 * 	Referred to as "GET Bucket" in the AWS docs, but implemented here as AmazonS3::list_objects(). Therefore, this is an alias of list_objects().
	 *
	 * See Also:
 	 * 	Related - <create_bucket()>, <head_bucket()>, <delete_bucket()>, <list_objects()>
	 */
	public function get_bucket($bucket, $opt = null)
	{
		if (!$opt) $opt = array();

		return $this->list_objects($bucket, $opt);
	}

	/**
	 * Method: get_bucket_locale()
	 * 	Lists the location constraint of the bucket. U.S.-based buckets have no response.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTBucketLocationGET.html
	 */
	public function get_bucket_locale($bucket, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'get_bucket_locale';
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: head_bucket()
	 * 	Reads only the HTTP headers of a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectHEAD.html
	 * 	Related - <create_bucket()>, <get_bucket()>, <delete_bucket()>
	 */
	public function head_bucket($bucket, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_HEAD;
		$opt['method'] = 'head_bucket';
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: if_bucket_exists()
	 * 	Checks whether this bucket already exists in your account or not.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 *
	 * Returns:
	 * 	_boolean_ Whether the bucket exists or not.
	 */
	public function if_bucket_exists($bucket)
	{
		$header = $this->head_bucket($bucket);
		return $header->isOK();
	}

	/**
	 * Method: delete_bucket()
	 * 	Deletes a bucket from your account. All objects in the bucket must be deleted before the bucket itself can be deleted.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	force - _boolean_ (Optional) Whether to force-delete the bucket and all of its contents. Defaults to false.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object if normal bucket deletion or if forced bucket deletion was successful, a boolean false if the forced deletion was unsuccessful.
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTBucketDELETE.html
	 * 	Related - <create_bucket()>, <get_bucket()>, <head_bucket()>
	 */
	public function delete_bucket($bucket, $force = false, $returnCurlHandle = null)
	{
		// Set default value
		$success = true;

		if ($force)
		{
			// Delete all of the items from the bucket.
			$success = $this->delete_all_objects($bucket);
		}

		// As long as we were successful...
		if ($success)
		{
			// Add this to our request
			$opt = array();
			$opt['verb'] = HTTP_DELETE;
			$opt['method'] = 'delete_bucket';
			$opt['returnCurlHandle'] = $returnCurlHandle;

			// Authenticate to S3
			return $this->authenticate($bucket, $opt);
		}

		return false;
	}

	/**
	 * Method: copy_bucket()
	 * 	Copies the contents of a bucket into a new bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	source_bucket - _string_ (Required) The name of the source bucket.
	 * 	dest_bucket - _string_ (Required) The name of the destination bucket.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	Related - <copy_object()>, <rename_bucket()>, <list_buckets()>
	 */
	public function copy_bucket($source_bucket, $dest_bucket, $acl = S3_ACL_PRIVATE)
	{
		// Since S3 can't yet copy across geographical locations, make sure that the new bucket matches the existing bucket.
		$locale = $this->get_bucket_locale($source_bucket);
		switch ($locale->body)
		{
			case S3_LOCATION_EU:
				$locale = S3_LOCATION_EU;
				break;

			default:
				$locale = S3_LOCATION_US;
				break;
		}

		$dest = $this->create_bucket($dest_bucket, $locale);

		if ($dest->isOK())
		{
			$list = $this->get_object_list($source_bucket);
			$handles = array();

			foreach ($list as $item)
			{
				$handles[] = $this->copy_object($source_bucket, $item, $dest_bucket, $item, array(
					'acl' => $acl,
					'returnCurlHandle' => true
				));
			}

			$request = new $this->request_class(null);
			return $request->send_multi_request($handles);
		}

		return false;
	}

	/**
	 * Method: rename_bucket()
	 * 	Renames a bucket by making a copy and deleting the original.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	source_bucket - _string_ (Required) The name of the source bucket.
	 * 	dest_bucket - _string_ (Required) The name of the destination bucket.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	Related - <rename_object()>, <copy_bucket()>, <list_buckets()>
	 */
	public function rename_bucket($source_bucket, $dest_bucket)
	{
		$responses['copy'] = $this->copy_bucket($source_bucket, $dest_bucket);
		$responses['delete'] = $this->delete_bucket($source_bucket, true);

		return $responses;
	}

	/**
	 * Method: get_bucket_size()
	 * 	Gets the number of files in the bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 *
	 * Returns:
	 * 	_integer_ The number of files in the bucket.
 	 *
	 * See Also:
	 * 	Related - <get_bucket_filesize()>
	 */
	public function get_bucket_size($bucket)
	{
		return count($this->get_object_list($bucket));
	}

	/**
	 * Method: get_bucket_filesize()
	 * 	Gets the file size of the contents of the bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	friendly_format - _boolean_ (Optional) Whether to format the value to 2 decimal points using the largest possible unit (i.e. 3.42 GB).
	 *
	 * Returns:
	 * 	_integer_|_string_ The number of bytes as an integer, or the friendly format as a string.
 	 *
	 * See Also:
	 * 	Related - <get_bucket_size()>, <get_object_filesize()>
	 */
	public function get_bucket_filesize($bucket, $friendly_format = false)
	{
		$filesize = 0;
		$list = $this->list_objects($bucket);

		foreach ($list->body->Contents as $filename)
		{
			$filesize += (int) $filename->Size;
		}

		if ($friendly_format)
		{
			$filesize = $this->util->size_readable($filesize);
		}

		return $filesize;
	}

	/**
	 * Method: list_buckets()
	 * 	Gets a list of all of the buckets on the S3 account.
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
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTServiceGET.html
	 * 	Related - <get_bucket_list()>
	 */
	public function list_buckets($returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'list_buckets';
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate('', $opt);
	}

	/**
	 * Method: get_bucket_list()
	 * 	ONLY lists the bucket names, as an array, on the S3 account.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	pcre - _string_ (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against.
	 *
	 * Returns:
	 * 	_array_ The list of matching bucket names.
 	 *
	 * See Also:
	 * 	Related - <list_buckets()>
	 */
	public function get_bucket_list($pcre = null)
	{
		// Set some default values
		$bucketnames = array();

		// Get a list of buckets.
		$list = $this->list_buckets();

		// If we have a PCRE regex, store it.
		if ($pcre)
		{
			// Loop through and find the bucket names.
			foreach ($list->body->Buckets->Bucket as $bucket)
			{
				$bucket = (string) $bucket->Name;

				if (preg_match($pcre, $bucket))
				{
					$bucketnames[] = $bucket;
				}
			}
		}
		else
		{
			// Loop through and find the bucket names.
			foreach ($list->body->Buckets->Bucket as $bucket)
			{
				$bucketnames[] = (string) $bucket->Name;
			}
		}

		return (count($bucketnames) > 0) ? $bucketnames : null;
	}

	/**
	 * Method: get_bucket_acl()
	 * 	Gets the ACL settings for a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTAccessPolicy.html
	 * 	Related - <set_object_acl()>, <set_bucket_acl()>, <get_object_acl()>
	 */
	public function get_bucket_acl($bucket, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'get_bucket_acl';
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: set_bucket_acl()
	 * 	Sets the ACL settings for a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Alternatively, an array of associative arrays. Each associative array contains an 'id' and a 'permission'. Defaults to <S3_ACL_PRIVATE>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTAccessPolicy.html
	 * 	Related - <set_object_acl()>, <get_bucket_acl()>, <get_object_acl()>
	 */
	public function set_bucket_acl($bucket, $acl = S3_ACL_PRIVATE, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'set_bucket_acl';
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Make sure these are defined.
		if (!defined('AWS_CANONICAL_ID') || !defined('AWS_CANONICAL_NAME'))
		{
			// Fetch the data live.
			$canonical = $this->get_canonical_user_id();
			define('AWS_CANONICAL_ID', $canonical['id']);
			define('AWS_CANONICAL_NAME', $canonical['display_name']);

			// Issue a notice.
			trigger_error('One or both of the configuration settings AWS_CANONICAL_ID and AWS_CANONICAL_NAME have NOT been set in config.inc.php. ' . CLOUDFUSION_NAME . ' must make additional requests to fetch the data, resulting in slower performance for ' . __FUNCTION__ . '(). For best performance, be sure to define these values in your config.inc.php file. For more details, see http://tarzan-aws.googlecode.com/svn/tags/' . CLOUDFUSION_VERSION . '/config-sample.inc.php', E_USER_NOTICE);
		}

		if (is_array($acl))
		{
			$opt['body'] = $this->generate_access_policy(AWS_CANONICAL_ID, AWS_CANONICAL_NAME, $acl);
		}
		else
		{
			$opt['acl'] = $acl;
		}

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}


	/*%******************************************************************************************%*/
	// OBJECT METHODS

	/**
	 * Method: create_object()
	 * 	Once you have a bucket, you can start storing objects in it. Objects are stored using the HTTP PUT method. Each object can hold up to 5 GB of data. When you store an object, S3 streams the data to multiple storage servers in multiple data centers to ensure that the data remains available in the event of internal network or hardware failure.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
 	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
 	 *
 	 * Keys for the $opt parameter:
 	 * 	filename - _string_ (Required) The filename for the object.
	 * 	body - _string_ (Required) The data to be stored in the object.
	 * 	contentType - _string_ (Required) The type of content that is being sent in the body.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 * 	headers - _array_ (Optional) Standard HTTP headers to send along in the request.
	 * 	meta - _array_ (Optional) Associative array of key-value pairs. Represented by x-amz-meta-: Any header starting with this prefix is considered user metadata. It will be stored with the object and returned when you retrieve the object. The total size of the HTTP request, not including the body, must be less than 4 KB.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectPUT.html
	 * 	ACL Policy - http://docs.amazonwebservices.com/AmazonS3/latest/RESTAccessPolicy.html
	 * 	Related - <get_object()>, <head_object()>, <delete_object()>
	 */
	public function create_object($bucket, $opt = null)
	{
		if (!$opt) $opt = array();

		// Add this to our request
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'create_object';
		$opt['filename'] = $opt['filename'];

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: get_object()
	 * 	Reads the contents of an object within a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	lastmodified - _string_ (Optional) The LastModified header passed in from a previous request. If used, requires 'etag' as well. Will return a 304 if file hasn't changed.
	 * 	etag - _string_ (Optional) The ETag header passed in from a previous request. If used, requires 'lastmodified' as well. Will return a 304 if file hasn't changed.
	 * 	range - _string_ (Optional) A range of bytes to fetch from the file. Useful for downloading partial bits or completing incomplete files. Range notated with a hyphen (e.g. 0-10485759). Defaults to the complete file.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectGET.html
	 * 	Related - <create_object()>, <head_object()>, <delete_object()>
	 */
	public function get_object($bucket, $filename, $opt = null)
	{
		if (!$opt) $opt = array();

		// Add this to our request
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'get_object';
		$opt['filename'] = $filename;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: head_object()
	 * 	Reads only the HTTP headers of an object within a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
 	 * 	filename - _string_ (Required) The filename for the object.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectHEAD.html
	 * 	Related - <create_object()>, <get_object()>, <delete_object()>, <if_object_exists()>
	 */
	public function head_object($bucket, $filename, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_HEAD;
		$opt['method'] = 'head_object';
		$opt['filename'] = $filename;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: if_object_exists()
	 * 	Checks whether this object already exists in this bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
 	 * 	filename - _string_ (Required) The filename for the object.
	 *
	 * Returns:
	 * 	_boolean_ Whether the object exists or not.
 	 *
	 * See Also:
	 * 	Related - <head_object()>
	 */
	public function if_object_exists($bucket, $filename)
	{
		$header = $this->head_object($bucket, $filename);
		return $header->isOK();
	}

	/**
	 * Method: delete_object()
	 * 	Deletes an object from within a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
 	 * 	filename - _string_ (Required) The filename for the object.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectDELETE.html
	 * 	Related - <create_object()>, <get_object()>, <head_object()>, <delete_all_objects()>
	 */
	public function delete_object($bucket, $filename, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_DELETE;
		$opt['method'] = 'delete_object';
		$opt['filename'] = $filename;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: delete_all_objects()
	 * 	Delete all of the objects inside the specified bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	pcre - _string_ (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against. Defaults to <S3_PCRE_ALL>.
	 *
	 * Returns:
	 * 	_boolean_ Determines the success of deleting all files.
 	 *
	 * See Also:
	 * 	Related - <delete_object()>
	 */
	public function delete_all_objects($bucket, $pcre = S3_PCRE_ALL)
	{
		// Collect all matches
		$list = $this->get_object_list($bucket, array('pcre' => $pcre));

		// As long as we have at least one match...
		if (count($list) > 0)
		{
			// Hold CURL handles
			$handles = array();

			// Go through all of the items and delete them.
			foreach ($list as $item)
			{
				$handles[] = $this->delete_object($bucket, $item, true);
			}

			$request = new $this->request_class(null);
			return $request->send_multi_request($handles);
		}

		return false;
	}

	/**
	 * Method: list_objects()
	 * 	Lists the objects in a bucket. Provided as the 'GetBucket' action in Amazon's REST API.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	prefix - _string_ (Optional) Restricts the response to only contain results that begin with the specified prefix.
	 * 	marker - _string_ (Optional) It restricts the response to only contain results that occur alphabetically after the value of marker.
	 * 	maxKeys - _string_ (Optional) Limits the number of results returned in response to your query. Will return no more than this number of results, but possibly less.
	 * 	delimiter - _string_ (Optional) Unicode string parameter. Keys that contain the same string between the prefix and the first occurrence of the delimiter will be rolled up into a single result element in the CommonPrefixes collection.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTBucketGET.html
	 * 	List Keys - http://docs.amazonwebservices.com/AmazonS3/latest/ListingKeysRequest.html
	 * 	Related - <get_bucket()>, <get_object_list()>
	 */
	public function list_objects($bucket, $opt = null)
	{
		if (!$opt) $opt = array();

		// Add this to our request
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'list_objects';

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: get_object_filesize()
	 * 	Gets the file size of the object.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
 	 * 	filename - _string_ (Required) The filename for the object.
	 * 	friendly_format - _boolean_ (Optional) Whether to format the value to 2 decimal points using the largest possible unit (i.e. 3.42 GB).
	 *
	 * Returns:
	 * 	_integer_|_string_ The number of bytes as an integer, or the friendly format as a string.
 	 *
	 * See Also:
	 * 	Related - <get_bucket_filesize()>
	 */
	public function get_object_filesize($bucket, $filename, $friendly_format = false)
	{
		$object = $this->head_object($bucket, $filename);
		$filesize = (integer) $object->header['content-length'];

		if ($friendly_format)
		{
			$filesize = $this->util->size_readable($filesize);
		}

		return $filesize;
	}

	/**
	 * Method: get_object_list()
	 * 	ONLY lists the object filenames from a bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	prefix - _string_ (Optional) Restricts the response to only contain results that begin with the specified prefix.
	 * 	marker - _string_ (Optional) It restricts the response to only contain results that occur alphabetically after the value of marker.
	 * 	maxKeys - _string_ (Optional) Limits the number of results returned in response to your query. Will return no more than this number of results, but possibly less.
	 * 	delimiter - _string_ (Optional) Unicode string parameter. Keys that contain the same string between the prefix and the first occurrence of the delimiter will be rolled up into a single result element in the CommonPrefixes collection.
	 * 	pcre - _string_ (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against. This is applied AFTER any native S3 filtering from 'prefix', 'marker', 'maxKeys', or 'delimiter'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	_array_ The list of matching object names.
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/ListingKeysRequest.html
	 * 	List Keys - http://docs.amazonwebservices.com/AmazonS3/latest/gsg/ListKeys.html
	 * 	Related - <get_bucket_list()>, <list_objects()>
	 */
	public function get_object_list($bucket, $opt = null)
	{
		// Set some default values
		$filenames = array();
		$pcre = null;

		// Get a list of files.
		$list = $this->list_objects($bucket, $opt);

		// Extract the options
		if ($opt)
		{
			extract($opt);
		}

		// If we have a PCRE regex, store it.
		if ($pcre)
		{
			if (isset($list))
			{
				// Loop through and find the filenames.
				foreach ($list->body->Contents as $file)
				{
					$file = (string) $file->Key;

					if (preg_match($pcre, $file))
					{
						$filenames[] = $file;
					}
				}
			}
		}
		else
		{
			if (isset($list))
			{
				// Loop through and find the filenames.
				foreach ($list->body->Contents as $file)
				{
					$filenames[] = (string) $file->Key;
				}
			}
		}

		return (count($filenames) > 0) ? $filenames : null;
	}

	/**
	 * Method: copy_object()
	 * 	Copies an object to a new location, whether in the same locale/bucket or otherwise.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	source_bucket - _string_ (Required) The name of the bucket that contains the source file.
	 * 	source_filename - _string_ (Required) The source filename that you want to copy.
	 * 	dest_bucket - _string_ (Required) The name of the bucket that you want to copy the file to.
	 * 	dest_filename - _string_ (Required) The filename that you want to give to the copy.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 * 	headers - _array_ (Optional) Standard HTTP headers to send along in the request.
	 * 	meta - _array_ (Optional) Associative array of key-value pairs. Represented by x-amz-meta-: Any header starting with this prefix is considered user metadata. It will be stored with the object and returned when you retrieve the object. The total size of the HTTP request, not including the body, must be less than 4 KB.
	 * 	metadataDirective - _string_ (Optional) Accepts either COPY or REPLACE. You will likely never need to use this, as it manages itself with no issues.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectCOPY.html
	 * 	Using and Copying Objects - http://docs.amazonwebservices.com/AmazonS3/latest/UsingCopyingObjects.html
	 * 	PUT Request Headers - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectPUT.html#RESTObjectPUTRequestHeaders
	 * 	Related - <copy_bucket()>, <duplicate_object()>, <move_object()>, <rename_object()>
	 */
	public function copy_object($source_bucket, $source_filename, $dest_bucket, $dest_filename, $opt = null)
	{
		if (!$opt) $opt = array();

		// Add this to our request
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'copy_object';
		$opt['sourceBucket'] = $source_bucket;
		$opt['sourceObject'] = $source_filename;
		$opt['destinationBucket'] = $dest_bucket;
		$opt['destinationObject'] = $dest_filename;
		$opt['filename'] = $dest_filename;
		$opt['metadataDirective'] = isset($opt['metadataDirective']) ? $opt['metadataDirective'] : 'COPY';

		// Do we have metadata?
		if (isset($opt['meta']) && is_array($opt['meta']))
		{
			$opt['metadataDirective'] = 'REPLACE';
		}

		// Authenticate to S3
		return $this->authenticate($dest_bucket, $opt);
	}

	/**
	 * Method: update_object()
	 * 	Updates an existing object with new content or settings.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket that contains the source file.
	 * 	filename - _string_ (Required) The source filename that you want to update.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 * 	headers - _array_ (Optional) Standard HTTP headers to send along in the request.
	 * 	meta - _array_ (Optional) Associative array of key-value pairs. Represented by x-amz-meta-: Any header starting with this prefix is considered user metadata. It will be stored with the object and returned when you retrieve the object. The total size of the HTTP request, not including the body, must be less than 4 KB.
	 * 	metadataDirective - _string_ (Optional) Accepts either COPY or REPLACE. You will likely never need to use this, as it manages itself with no issues.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectCOPY.html
	 * 	Using and Copying Objects - http://docs.amazonwebservices.com/AmazonS3/latest/UsingCopyingObjects.html
	 * 	PUT Request Headers - http://docs.amazonwebservices.com/AmazonS3/latest/RESTObjectPUT.html#RESTObjectPUTRequestHeaders
	 * 	Related - <copy_bucket()>, <duplicate_object()>, <move_object()>, <rename_object()>
	 */
	public function update_object($bucket, $filename, $opt)
	{
		if (!$opt) $opt = array();

		// Add this to our request
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'update_object';
		$opt['sourceBucket'] = $bucket;
		$opt['sourceObject'] = $filename;
		$opt['destinationBucket'] = $bucket;
		$opt['destinationObject'] = $filename;
		$opt['filename'] = $filename;
		$opt['metadataDirective'] = 'REPLACE';

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: duplicate_object()
	 * 	Identical to <copy_object()>, except that it only copies within a single bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket that contains the file.
	 * 	source_filename - _string_ (Required) The source filename that you want to copy.
	 * 	dest_filename - _string_ (Required) The filename that you want to give to the copy.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	Related - <copy_object()>, <move_object()>, <rename_object()>
	 */
	public function duplicate_object($bucket, $source_filename, $dest_filename, $acl = S3_ACL_PRIVATE)
	{
		return $this->copy_object($bucket, $source_filename, $bucket, $dest_filename, array('acl' => $acl));
	}

	/**
	 * Method: move_object()
	 * 	Moves an object to a new location, whether in the same locale/bucket or otherwise.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	source_bucket - _string_ (Required) The name of the bucket that contains the source file.
	 * 	source_filename - _string_ (Required) The source filename that you want to copy.
	 * 	dest_bucket - _string_ (Required) The name of the bucket that you want to copy the file to.
	 * 	dest_filename - _string_ (Required) The filename that you want to give to the copy.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 *
	 * Returns:
	 * 	_array_ <ResponseCore> objects for the copy and the delete.
 	 *
	 * See Also:
	 * 	Related - <copy_object()>, <duplicate_object()>, <rename_object()>
	 */
	public function move_object($source_bucket, $source_filename, $dest_bucket, $dest_filename, $acl = S3_ACL_PRIVATE)
	{
		$return = array();
		$return['copy'] = $this->copy_object($source_bucket, $source_filename, $dest_bucket, $dest_filename, array('acl' => $acl));
		$return['delete'] = $this->delete_object($source_bucket, $source_filename);
		return $return;
	}

	/**
	 * Method: rename_object()
	 * 	Identical to <move_object()>, except that it only moves within a single bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket that contains the file.
	 * 	source_filename - _string_ (Required) The source filename that you want to copy.
	 * 	dest_filename - _string_ (Required) The filename that you want to give to the copy.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	Related - <copy_object()>, <duplicate_object()>, <move_object()>
	 */
	public function rename_object($bucket, $source_filename, $dest_filename, $acl = S3_ACL_PRIVATE)
	{
		return $this->move_object($bucket, $source_filename, $bucket, $dest_filename, $acl);
	}

	/**
	 * Method: get_object_acl()
	 * 	Gets the ACL settings for a object.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTAccessPolicy.html
	 * 	Related - <set_object_acl()>, <set_bucket_acl()>, <get_bucket_acl()>
	 */
	public function get_object_acl($bucket, $filename, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'get_object_acl';
		$opt['filename'] = $filename;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: set_object_acl()
	 * 	Sets the ACL settings for a object.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Alternatively, an array of associative arrays. Each associative array contains an 'id' and a 'permission'. Defaults to <S3_ACL_PRIVATE>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/RESTAccessPolicy.html
	 * 	Related - <set_bucket_acl()>, <get_bucket_acl()>, <get_object_acl()>
	 */
	public function set_object_acl($bucket, $filename, $acl = S3_ACL_PRIVATE, $returnCurlHandle = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'set_object_acl';
		$opt['filename'] = $filename;
		$opt['returnCurlHandle'] = $returnCurlHandle;

		// Make sure these are defined.
		if (!defined('AWS_CANONICAL_ID') || !defined('AWS_CANONICAL_NAME'))
		{
			// Fetch the data live.
			$canonical = $this->get_canonical_user_id();
			define('AWS_CANONICAL_ID', $canonical['id']);
			define('AWS_CANONICAL_NAME', $canonical['display_name']);

			// Issue a notice.
			trigger_error('One or both of the configuration settings AWS_CANONICAL_ID and AWS_CANONICAL_NAME have NOT been set in config.inc.php. ' . CLOUDFUSION_NAME . ' must make additional requests to fetch the data, resulting in slower performance for ' . __FUNCTION__ . '(). For best performance, be sure to define these values in your config.inc.php file. For more details, see http://tarzan-aws.googlecode.com/svn/tags/' . CLOUDFUSION_VERSION . '/config-sample.inc.php', E_USER_NOTICE);
		}

		if (is_array($acl))
		{
			$opt['body'] = $this->generate_access_policy(AWS_CANONICAL_ID, AWS_CANONICAL_NAME, $acl);
		}
		else
		{
			$opt['acl'] = $acl;
		}

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}


	/*%******************************************************************************************%*/
	// LOGGING METHODS

	/**
	 * Method: get_logs()
	 * 	Get the access logs associated with a given bucket.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used. Pass null if using <set_vhost()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/ServerLogs.html
	 * 	Related - <get_logs()>, <enable_logging()>, <disable_logging()>
	 */
	public function get_logs($bucket)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_GET;
		$opt['method'] = 'get_logs';

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: enable_logging()
	 * 	Enable access logging.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be log. Pass null if using <set_vhost()>.
	 * 	target_bucket - _string_ (Required) The name of the bucket to store the logs in.
	 * 	target_prefix - _string_ (Required) The prefix to give to the log filenames.
	 * 	users - _array_ (Optional) Any non-owner users to give access to. Set as an array of key-value pairs: the email address (must be tied to an AWS account) is the key, and the permission is the value. Allowable permissions are <S3_GRANT_READ>, <S3_GRANT_WRITE>, <S3_GRANT_READ_ACP>, <S3_GRANT_WRITE_ACP>, and <S3_GRANT_FULL_CONTROL>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/LoggingAPI.html
	 * 	Permissions - http://docs.amazonwebservices.com/AmazonS3/latest/S3_ACLs.html#S3_ACLs_Permissions
	 * 	Related - <get_logs()>, <enable_logging()>, <disable_logging()>
	 */
	public function enable_logging($bucket, $target_bucket, $target_prefix, $users = null)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'enable_logging';

		$xml = simplexml_load_string($this->base_logging_xml);
		$LoggingEnabled = $xml->addChild('LoggingEnabled');
		$LoggingEnabled->addChild('TargetBucket', $target_bucket);
		$LoggingEnabled->addChild('TargetPrefix', $target_prefix);
		$TargetGrants = $LoggingEnabled->addChild('TargetGrants');

		if ($users && is_array($users))
		{
			foreach ($users as $email => $permission)
			{
				$Grant = $TargetGrants->addChild('Grant');
				$Grantee = $Grant->addChild('Grantee');
				$Grantee->addAttribute('xsi:type', 'AmazonCustomerByEmail', 'http://www.w3.org/2001/XMLSchema-instance');
				$Grantee->addChild('EmailAddress', $email);
				$Grant->addChild('Permission', $permission);
			}
		}

		$opt['body'] = $xml->asXML();

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}

	/**
	 * Method: disable_logging()
	 * 	Disable access logging.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used. Pass null if using <set_vhost()>.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/LoggingAPI.html
	 * 	Related - <get_logs()>, <enable_logging()>, <disable_logging()>
	 */
	public function disable_logging($bucket)
	{
		// Add this to our request
		$opt = array();
		$opt['verb'] = HTTP_PUT;
		$opt['method'] = 'disable_logging';
		$opt['body'] = $this->base_logging_xml;

		// Authenticate to S3
		return $this->authenticate($bucket, $opt);
	}


	/*%******************************************************************************************%*/
	// CONVENIENCE METHODS

	/**
	 * Method: store_remote_file()
	 * 	Takes an existing remote URL, stores it to S3, and returns a URL for the stored copy. For creating new objects in S3, use the <create_object()> method.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
 	 * 	remote_file - _string_ (Required) The full URL of the file to store on the S3 service.
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	acl - _string_ (Optional) One of the following options: <S3_ACL_PRIVATE>, <S3_ACL_PUBLIC>, <S3_ACL_OPEN>, or <S3_ACL_AUTH_READ>. Defaults to <S3_ACL_PRIVATE>.
	 * 	overwrite - _boolean_ (Optional) If set to true, checks to see if the file exists and will overwrite the old data with new data. Defaults to false.
	 *
	 * Returns:
	 * 	_string_ The S3 URL for the uploaded file. Returns null if unsuccessful.
	 */
	public function store_remote_file($remote_file, $bucket, $filename, $opt = null)
	{
		// Set default values.
		$acl = S3_ACL_PUBLIC;
		$overwrite = false;
		$cname = null;

		if ($opt)
		{
			// Break the options out.
			extract($opt);
		}

		// Does the file already exist?
		$object = $this->head_object($bucket, $filename);

		// As long as it doesn't already exist, fetch and store it.
		if (!$object->isOK() || $overwrite)
		{
			// Fetch the file
			$file = new $this->request_class($remote_file);
			$file->send_request();

			// Store it in S3
			unset($object);
			$object = $this->create_object($bucket, array(
				'filename' => $filename,
				'body' => $file->get_response_body(),
				'contentType' => $file->get_response_header('content-type'),
				'acl' => $acl
			));
		}

		// Was the request successful?
		if ($object->isOK())
		{
			$url = $object->header['x-tarzan-requesturl'];

			// If we have a virtual host value, use that instead of Amazon's hostname. There are better ways of doing this, but it works for now.
			if ($this->vhost)
			{
				$url = str_ireplace('http://', '', $url);
				$url = explode('/', $url);
				$url[0] = $this->vhost;
				$url = 'http://' . implode('/', $url);
			}

			return $url;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Method: change_content_type()
	 * 	Changes the content type for an existing object.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	contentType - _string_ (Required) The content-type to apply to the object.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 */
	public function change_content_type($bucket, $filename, $contentType, $returnCurlHandle = null)
	{
		return $s3->copy_object($bucket, $filename, $bucket, $filename, array(
			'contentType' => $contentType,
			'metadataDirective' => 'REPLACE',
			'returnCurlHandle' => $returnCurlHandle
		));
	}


	/*%******************************************************************************************%*/
	// URLS

	/**
	 * Method: get_object_url()
	 * 	Gets the web-accessible URL for the file (assuming you've set the ACL settings to <S3_ACL_PUBLIC>).
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	qsa - _integer_ (Optional) How many seconds should the query string authenticated URL work for? Only generates query string authentication parameters if value is greater than 0. Defaults to 0.
	 * 	torrent - _boolean_ (Optional) Whether to return the torrent version of the URL or not. Defaults to false.
	 *
	 * Returns:
	 * 	_string_ The file URL (with authentication and/or torrent parameters if requested).
	 *
	 * See Also:
	 * 	Query String Authentication - http://docs.amazonwebservices.com/AmazonS3/latest/S3_QSAuth.html
	 * 	Related - <get_torrent_url()>
	 */
	public function get_object_url($bucket, $filename, $qsa = 0, $torrent = false)
	{
		if ($qsa)
		{
			// Add this to our request
			$opt = array();
			$opt['verb'] = HTTP_GET;
			$opt['method'] = 'get_object_url';
			$opt['filename'] = $filename . (($torrent) ? '?torrent' : '');
			$opt['qsa'] = $qsa;

			// Adjust the clock
			$old_offset = $this->adjust_offset;
			$this->adjust_offset($qsa);

			// Authenticate to S3
			$data = $this->authenticate($bucket, $opt);

			// Reset the clock
			$this->adjust_offset = $old_offset;

			if ($this->vhost)
			{
				return 'http://' . $this->vhost . $data['filename'] . ((!$torrent) ? '?' : '&') . 'AWSAccessKeyId=' . $data['key'] . '&Expires=' . $data['expires'] . '&Signature=' . rawurlencode($data['signature']);
			}

			return 'http://' . $data['bucket'] . '.s3.amazonaws.com' . $data['filename'] . ((!$torrent) ? '?' : '&') . 'AWSAccessKeyId=' . $data['key'] . '&Expires=' . $data['expires'] . '&Signature=' . rawurlencode($data['signature']);
		}
		else
		{
			// If we're using a virtual host, use that instead.
			if ($this->vhost)
			{
				return 'http://' . $this->vhost . '/' . $filename . (($torrent) ? '?torrent' : '');
			}

			return 'http://' . $bucket . '.s3.amazonaws.com/' . $filename . (($torrent) ? '?torrent' : '');
		}
	}

	/**
	 * Method: get_torrent_url()
	 * 	Gets the web-accessible torrent URL for the file (assuming you've set the ACL settings to <S3_ACL_PUBLIC>).
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	bucket - _string_ (Required) The name of the bucket to be used.
	 * 	filename - _string_ (Required) The filename for the object.
	 * 	qsa - _integer_ (Optional) How many seconds should the query string authenticated URL work for? Only generates query string authentication parameters if value is greater than 0. Defaults to 0.
	 *
	 * Returns:
 	* 	_string_ The torrent URL (with authentication parameters if requested).
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/index.html?S3TorrentRetrieve.html
	 * 	Related - <get_object_url()>
	 */
	public function get_torrent_url($bucket, $filename, $qsa = 0)
	{
		if ($qsa)
		{
			return $this->get_object_url($bucket, $filename, $qsa, true);
		}
		return $this->get_object_url($bucket, $filename, 0 , true);
	}


	/*%******************************************************************************************%*/
	// ACCESS CONTROL POLICY

	/**
	 * Method: generate_access_policy()
	 * 	Generate the XML to be used for the Access Control Policy.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	canonical_id - _string_ (Required) The Canonical ID for the Owner. Use the <AWS_CANONICAL_ID> constant or the 'id' value from <get_canonical_user_id()>.
	 * 	canonical_name - _string_ (Required) The Canonical Display Name for the Owner. Use the <AWS_CANONICAL_NAME> constant or the 'display_name' value from <get_canonical_user_id()>.
	 * 	users - _array_ (Optional) Array of associative arrays. Each associative array contains an 'id' and a 'permission'.
	 *
	 * Returns:
	 * 	_string_ Access Control Policy XML.
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonS3/latest/S3_ACLs.html
	 */
	public function generate_access_policy($canonical_id, $canonical_name, $users)
	{
		$xml = simplexml_load_string($this->base_acp_xml);
		$owner = $xml->addChild('Owner');
		$owner->addChild('ID', $canonical_id);
		$owner->addChild('DisplayName', $canonical_name);
		$acl = $xml->addChild('AccessControlList');

		foreach ($users as $user)
		{
			$grant = $acl->addChild('Grant');
			$grantee = $grant->addChild('Grantee');

			switch ($user['id'])
			{
				// Authorized Users
				case S3_USERS_AUTH:
					$grantee->addAttribute('xsi:type', 'Group', 'http://www.w3.org/2001/XMLSchema-instance');
					$grantee->addChild('URI', S3_USERS_AUTH);
					break;

				// All Users
				case S3_USERS_ALL:
					$grantee->addAttribute('xsi:type', 'Group', 'http://www.w3.org/2001/XMLSchema-instance');
					$grantee->addChild('URI', S3_USERS_ALL);
					break;

				// The Logging User
				case S3_USERS_LOGGING:
					$grantee->addAttribute('xsi:type', 'Group', 'http://www.w3.org/2001/XMLSchema-instance');
					$grantee->addChild('URI', S3_USERS_LOGGING);
					break;

				// Assume an Email Address
				default:
					$grantee->addAttribute('xsi:type', 'AmazonCustomerByEmail', 'http://www.w3.org/2001/XMLSchema-instance');
					$grantee->addChild('EmailAddress', $user['id']);
					break;
			}

			$grant->addChild('Permission', $user['permission']);
		}

		return $xml->asXML();
	}

	/**
	 * Method: get_canonical_user_id()
	 * 	Obtains the CanonicalUser ID and DisplayName from the server.
	 *
	 * Access:
	 * 	public
 	 *
	 * Returns:
 	 * 	_array_ The id and display_name values.
	 */
	public function get_canonical_user_id()
	{
		$id = $this->list_buckets();
		return array(
			'id' => (string) $id->body->Owner->ID,
			'display_name' => (string) $id->body->Owner->DisplayName
		);
	}
}
