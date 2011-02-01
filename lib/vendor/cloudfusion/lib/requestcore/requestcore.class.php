<?php
/**
 * File: RequestCore
 * 	Handles all linear and parallel HTTP requests using cURL and manages the responses.
 *
 * Version:
 * 	2009.12.17
 *
 * Copyright:
 * 	2006-2009 LifeNexus Digital, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 */


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: RequestCore_Exception
 * 	Default RequestCore Exception.
 */
class RequestCore_Exception extends Exception {}


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: HTTP_GET
 * HTTP method type: Get
 */
if (!defined('HTTP_GET')) define('HTTP_GET', 'GET');

/**
 * Constant: HTTP_POST
 * HTTP method type: Post
 */
if (!defined('HTTP_POST')) define('HTTP_POST', 'POST');

/**
 * Constant: HTTP_PUT
 * HTTP method type: Put
 */
if (!defined('HTTP_PUT')) define('HTTP_PUT', 'PUT');

/**
 * Constant: HTTP_DELETE
 * HTTP method type: Delete
 */
if (!defined('HTTP_DELETE')) define('HTTP_DELETE', 'DELETE');

/**
 * Constant: HTTP_HEAD
 * HTTP method type: Head
 */
if (!defined('HTTP_HEAD')) define('HTTP_HEAD', 'HEAD');


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: RequestCore
 * 	Container for all request-related methods.
 */
class RequestCore
{
	/**
	 * Property: request_url
	 * 	The URL being requested.
	 */
	var $request_url;

	/**
	 * Property: request_headers
	 * 	The headers being sent in the request.
	 */
	var $request_headers;

	/**
	 * Property: request_body
	 * 	The body being sent in the request.
	 */
	var $request_body;

	/**
	 * Property: response
	 * 	The response returned by the request.
	 */
	var $response;

	/**
	 * Property: response_headers
	 * 	The headers returned by the request.
	 */
	var $response_headers;

	/**
	 * Property: response_body
	 * 	The body returned by the request.
	 */
	var $response_body;

	/**
	 * Property: response_code
	 * 	The HTTP status code returned by the request.
	 */
	var $response_code;

	/**
	 * Property: response_info
	 * 	Additional response data.
	 */
	var $response_info;

	/**
	 * Property: curl_handle
	 * 	The handle for the cURL object.
	 */
	var $curl_handle;

	/**
	 * Property: method
	 * 	The method by which the request is being made.
	 */
	var $method;

	/**
	 * Property: proxy
	 * 	Stores the proxy settings to use for the request.
	 */
	var $proxy = null;

	/**
	 * Property: username
	 * 	The username to use for the request.
	 */
	var $username = null;

	/**
	 * Property: password
	 * 	The password to use for the request.
	 */
	var $password = null;

	/**
	 * Property: curlopts
	 * 	Custom CURLOPT settings.
	 */
	var $curlopts = null;

	/**
	 * Property: request_class
	 * 	The default class to use for HTTP Requests (defaults to <RequestCore>).
	 */
	var $request_class = 'RequestCore';

	/**
	 * Property: response_class
	 * 	The default class to use for HTTP Responses (defaults to <ResponseCore>).
	 */
	var $response_class = 'ResponseCore';

	/**
	 * Property: useragent
	 * 	Default useragent string to use.
	 */
	var $useragent = 'RequestCore/1.1';


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
	 * 	url - _string_ (Optional) The URL to request or service endpoint to query.
	 * 	proxy - _string_ (Optional) The faux-url to use for proxy settings. Takes the following format: proxy://user:pass@hostname:port
	 * 	helpers - _array_ (Optional) An associative array of classnames to use for request, and response functionality. Gets passed in automatically by the calling class.
	 *
	 * Returns:
	 * 	$this
	 */
	public function __construct($url = null, $proxy = null, $helpers = null)
	{
		// Set some default values.
		$this->request_url = $url;
		$this->method = HTTP_GET;
		$this->request_headers = array();
		$this->request_body = '';

		// Set a new Request class if one was set.
		if (isset($helpers['request']) && !empty($helpers['request']))
		{
			$this->request_class = $helpers['request'];
		}

		// Set a new Request class if one was set.
		if (isset($helpers['response']) && !empty($helpers['response']))
		{
			$this->response_class = $helpers['response'];
		}

		if ($proxy)
		{
			$this->set_proxy($proxy);
		}

		return $this;
	}


	/*%******************************************************************************************%*/
	// REQUEST METHODS

	/**
	 * Method: set_credentials()
	 * 	Sets the credentials to use for authentication.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	user - _string_ (Required) The username to authenticate with.
	 * 	pass - _string_ (Required) The password to authenticate with.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_credentials($user, $pass)
	{
		$this->username = $user;
		$this->password = $pass;
		return $this;
	}

	/**
	 * Method: add_header()
	 * 	Adds a custom HTTP header to the cURL request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key - _string_ (Required) The custom HTTP header to set.
	 * 	value - _mixed_ (Required) The value to assign to the custom HTTP header.
	 *
	 * Returns:
	 * 	$this
	 */
	public function add_header($key, $value)
	{
		$this->request_headers[$key] = $value;
		return $this;
	}

	/**
	 * Method: remove_header()
	 * 	Removes an HTTP header from the cURL request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key - _string_ (Required) The custom HTTP header to set.
	 *
	 * Returns:
	 * 	$this
	 */
	public function remove_header($key)
	{
		if (isset($this->request_headers[$key]))
		{
			unset($this->request_headers[$key]);
		}
		return $this;
	}

	/**
	 * Method: set_method()
	 * 	Set the method type for the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	method - _string_ (Required) One of the following constants: <HTTP_GET>, <HTTP_POST>, <HTTP_PUT>, <HTTP_HEAD>, <HTTP_DELETE>.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_method($method)
	{
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Method: set_useragent()
	 * 	Sets a custom useragent string for the class.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	method - _string_ (Required) The useragent string to use.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_useragent($ua)
	{
		$this->useragent = $ua;
		return $this;
	}

	/**
	 * Method: set_body()
	 * 	Set the body to send in the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	body - _string_ (Required) The textual content to send along in the body of the request.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_body($body)
	{
		$this->request_body = $body;
		return $this;
	}

	/**
	 * Method: set_request_url()
	 * 	Set the URL to make the request to.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	body - _string_ (Required) The textual content to send along in the body of the request.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_request_url($url)
	{
		$this->request_url = $url;
		return $this;
	}

	/**
	 * Method: set_curlopts()
	 * 	Set additional CURLOPT settings. These will merge with the default settings, and override if there is a duplicate.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	curlopts - _array_ (Optional) A set of key-value pairs that set CURLOPT options. These will merge with the existing CURLOPTs, and ones passed here will override the defaults. Keys should be the CURLOPT_* constants, not strings.
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_curlopts($curlopts)
	{
		$this->curlopts = $curlopts;
		return $this;
	}

	/**
	 * Method: set_proxy()
	 * 	Set the proxy to use for making requests.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	proxy - _string_ (Optional) The faux-url to use for proxy settings. Takes the following format: proxy://user:pass@hostname:port
	 *
	 * Returns:
	 * 	$this
	 */
	public function set_proxy($proxy)
	{
		$proxy = parse_url($proxy);
		$proxy['user'] = isset($proxy['user']) ? $proxy['user'] : null;
		$proxy['pass'] = isset($proxy['pass']) ? $proxy['pass'] : null;
		$proxy['port'] = isset($proxy['port']) ? $proxy['port'] : null;
		$this->proxy = $proxy;
		return $this;
	}


	/*%******************************************************************************************%*/
	// PREPARE, SEND, AND PROCESS REQUEST

	/**
	 * Method: prep_request()
	 * 	Prepares and adds the details of the cURL request. This can be passed along to a curl_multi_exec() function.
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	The handle for the cURL object.
	 */
	public function prep_request()
	{
		$this->add_header('Expect', '100-continue');
		$this->add_header('Connection', 'close');

		$curl_handle = curl_init();

		// Set default options.
		curl_setopt($curl_handle, CURLOPT_URL, $this->request_url);
		curl_setopt($curl_handle, CURLOPT_FILETIME, true);
		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
		curl_setopt($curl_handle, CURLOPT_HEADER, true);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
		curl_setopt($curl_handle, CURLOPT_REFERER, $this->request_url);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->useragent);

		// Merge in the CURLOPTs
		if (isset($this->curlopts) && sizeof($this->curlopts) > 0)
		{
			foreach ($this->curlopts as $k => $v)
			{
				curl_setopt($curl_handle, $k, $v);
			}
		}

		// Enable a proxy connection if requested.
		if ($this->proxy)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, true);

			$host = $this->proxy['host'];
			$host .= ($this->proxy['port']) ? ':' . $this->proxy['port'] : '';
			curl_setopt($curl_handle, CURLOPT_PROXY, $host);

			if (isset($this->proxy['user']) && isset($this->proxy['pass']))
			{
				curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $this->proxy['user'] . ':' . $this->proxy['pass']);
			}
		}

		// Set credentials for HTTP Basic/Digest Authentication.
		if ($this->username && $this->password)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}

		// Handle the encoding if we can.
		if (extension_loaded('zlib'))
		{
			curl_setopt($curl_handle, CURLOPT_ENCODING, '');
		}

		// Process custom headers
		if (isset($this->request_headers) && count($this->request_headers))
		{
			$temp_headers = array();

			foreach ($this->request_headers as $k => $v)
			{
				$temp_headers[] = $k . ': ' . $v;
			}

			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
		}

		switch ($this->method)
		{
			case HTTP_PUT:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;

			case HTTP_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;

			case HTTP_HEAD:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, HTTP_HEAD);
				curl_setopt($curl_handle, CURLOPT_NOBODY, 1);
				break;

			default:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $this->method);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;
		}

		return $curl_handle;
	}

	/**
	 * Method: process_response()
	 * 	Take the post-processed cURL data and break it down into useful header/body/info chunks. Uses the data stored in the <curl_handle> and <response> properties unless replacement data is passed in via parameters.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	curl_handle - _string_ (Optional) The reference to the already executed cURL request.
	 * 	response - _string_ (Optional) The actual response content itself that needs to be parsed.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 */
	public function process_response($curl_handle = null, $response = null)
	{
		// Accept a custom one if it's passed.
		if ($curl_handle && $response)
		{
			$this->curl_handle = $curl_handle;
			$this->response = $response;
		}

		// As long as this came back as a valid resource...
		if (is_resource($this->curl_handle))
		{
			// Determine what's what.
			$header_size = curl_getinfo($this->curl_handle, CURLINFO_HEADER_SIZE);
			$this->response_headers = substr($this->response, 0, $header_size);
			$this->response_body = substr($this->response, $header_size);
			$this->response_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
			$this->response_info = curl_getinfo($this->curl_handle);

			// Parse out the headers
			$this->response_headers = explode("\r\n\r\n", trim($this->response_headers));
			$this->response_headers = array_pop($this->response_headers);
			$this->response_headers = explode("\r\n", $this->response_headers);
			array_shift($this->response_headers);

			// Loop through and split up the headers.
			$header_assoc = array();
			foreach ($this->response_headers as $header)
			{
				$kv = explode(': ', $header);
				$header_assoc[strtolower($kv[0])] = $kv[1];
			}

			// Reset the headers to the appropriate property.
			$this->response_headers = $header_assoc;
			$this->response_headers['_info'] = $this->response_info;
			$this->response_headers['_info']['method'] = $this->method;

			if ($curl_handle && $response)
			{
				return new $this->response_class($this->response_headers, $this->response_body, $this->response_code);
			}
		}

		// Return false
		return false;
	}

	/**
	 * Method: send_request()
	 * 	Sends the request, calling necessary utility functions to update built-in properties.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	parse - _boolean_ (Optional) Whether to parse the response with ResponseCore or not.
	 *
	 * Returns:
	 * 	_string_ The resulting unparsed data from the request.
	 */
	public function send_request($parse = false)
	{
		$curl_handle = $this->prep_request();
		$this->response = curl_exec($curl_handle);
		$parsed_response = $this->process_response($curl_handle, $this->response);

		curl_close($curl_handle);

		if ($parse)
		{
			return $parsed_response;
		}

		return $this->response;
	}

	/**
	 * Method: send_multi_request()
	 * 	Sends the request using curl_multi_exec(), enabling parallel requests.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	handles - _array_ (Required) An indexed array of cURL handles to process simultaneously.
	 *
	 * Returns:
	 * 	_array_ Post-processed cURL responses.
	 */
	public function send_multi_request($handles)
	{
		// Initialize MultiCURL
		$multi_handle = curl_multi_init();

		// Loop through each of the CURL handles and add them to the MultiCURL request.
		foreach ($handles as $handle)
		{
			curl_multi_add_handle($multi_handle, $handle);
		}

		$count = 0;

		// Execute
		do
		{
			$status = curl_multi_exec($multi_handle, $active);
		}
		while ($status == CURLM_CALL_MULTI_PERFORM || $active);

		// Define this.
		$handles_post = array();

		// Retrieve each handle response
		foreach ($handles as $handle)
		{
			if (curl_errno($handle) == CURLE_OK)
			{
				$http = new $this->request_class(null);
				$handles_post[] = $http->process_response($handle, curl_multi_getcontent($handle));
			}
			else
			{
				throw new RequestCore_Exception(curl_error($handle));
			}

			// Explicitly close each cURL handle.
			curl_multi_remove_handle($multi_handle, $handle);
			curl_close($handle);
		}

		return $handles_post;
	}


	/*%******************************************************************************************%*/
	// RESPONSE METHODS

	/**
	 * Method: get_response_header()
	 * 	Get the HTTP response headers from the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	header - _string_ (Optional) A specific header value to return. Defaults to all headers.
	 *
	 * Returns:
	 * 	_string_|_array_ All or selected header values.
	 */
	public function get_response_header($header = null)
	{
		if ($header)
		{
			return $this->response_headers[strtolower($header)];
		}
		return $this->response_headers;
	}

	/**
	 * Method: get_response_body()
	 * 	Get the HTTP response body from the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	_string_ The response body.
	 */
	public function get_response_body()
	{
		return $this->response_body;
	}

	/**
	 * Method: get_response_code()
	 * 	Get the HTTP response code from the request.
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	_string_ The HTTP response code.
	 */
	public function get_response_code()
	{
		return $this->response_code;
	}
}


/**
 * Class: ResponseCore
 * 	Container for all response-related methods.
 */
class ResponseCore
{
	/**
	 * Property: header
	 * Stores the HTTP header information.
	 */
	var $header;

	/**
	 * Property: body
	 * Stores the SimpleXML response.
	 */
	var $body;

	/**
	 * Property: status
	 * Stores the HTTP response code.
	 */
	var $status;

	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	header - _array_ (Required) Associative array of HTTP headers (typically returned by <RequestCore::getResponseHeader()>).
	 * 	body - _string_ (Required) XML-formatted response from AWS.
	 * 	status - _integer_ (Optional) HTTP response status code from the request.
	 *
	 * Returns:
	 * 	_object_ Contains an _array_ 'header' property (HTTP headers as an associative array), a _SimpleXMLElement_ or _string_ 'body' property, and an _integer_ 'status' code.
	 */
	public function __construct($header, $body, $status = null)
	{
		$this->header = $header;
		$this->body = $body;
		$this->status = $status;
		return $this;
	}

	/**
	 * Method: isOK()
	 * 	Did we receive the status code we expected?
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	codes - _integer|array_ (Optional) The status code(s) to expect. Pass an _integer_ for a single acceptable value, or an _array_ of integers for multiple acceptable values. Defaults to _array_ 200|204.
	 *
	 * Returns:
	 * 	_boolean_ Whether we received the expected status code or not.
	 */
	public function isOK($codes = array(200, 201, 204))
	{
		if (is_array($codes))
		{
			return in_array($this->status, $codes);
		}
		else
		{
			return ($this->status == $codes);
		}
	}
}
