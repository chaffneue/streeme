<?php
/**
 * File: Amazon PAS
 * 	Product Advertising Service (http://aws.amazon.com/associates)
 *
 * Version:
 * 	2009.09.04
 *
 * Copyright:
 * 	2006-2009 Foleeo, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	CloudFusion - http://getcloudfusion.com
 * 	Amazon PAS - http://aws.amazon.com/associates
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: PAS_LOCALE_US
 * 	Locale code for the United States
 */
define('PAS_LOCALE_US', 'us');

/**
 * Constant: PAS_LOCALE_UK
 * 	Locale code for the United Kingdom
 */
define('PAS_LOCALE_UK', 'uk');

/**
 * Constant: PAS_LOCALE_CANADA
 * 	Locale code for Canada
 */
define('PAS_LOCALE_CANADA', 'ca');

/**
 * Constant: PAS_LOCALE_FRANCE
 * 	Locale code for France
 */
define('PAS_LOCALE_FRANCE', 'fr');

/**
 * Constant: PAS_LOCALE_GERMANY
 * 	Locale code for Germany
 */
define('PAS_LOCALE_GERMANY', 'de');

/**
 * Constant: PAS_LOCALE_JAPAN
 * 	Locale code for Japan
 */
define('PAS_LOCALE_JAPAN', 'jp');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: PAS_Exception
 * 	Default PAS Exception.
 */
class PAS_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonPAS
 * 	Container for all Amazon PAS-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 *
 * Example Usage:
 * (start code)
 * require_once('cloudfusion.class.php');
 *
 * // Instantiate a new AmazonPAS object using the settings from the config.inc.php file.
 * $s3 = new AmazonPAS();
 *
 * // Instantiate a new AmazonPAS object using these specific settings.
 * $s3 = new AmazonPAS($key, $secret_key, $assoc_id);
 * (end)
 */
class AmazonPAS extends CloudFusion
{
	/**
	 * Property: locale
	 * The Amazon locale to use by default.
	 */
	var $locale;


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
	 * 	assoc_id - _string_ (Optional) Your Amazon Associates ID. If blank, it will look for the <AWS_ASSOC_ID> constant.
	 *
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
	 */
	public function __construct($key = null, $secret_key = null, $assoc_id = null)
	{
		$this->api_version = '2009-07-01';

		if (!$key && !defined('AWS_KEY'))
		{
			throw new PAS_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new PAS_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		if (!$assoc_id && !defined('AWS_ASSOC_ID'))
		{
			throw new PAS_Exception('No Amazon Associates ID was passed into the constructor, nor was it set in the AWS_ASSOC_ID constant.');
		}

		return parent::__construct($key, $secret_key, null, $assoc_id);
	}


	/*%******************************************************************************************%*/
	// SET CUSTOM SETTINGS

	/**
	 * Method: set_locale()
	 * 	Override the default locale to use for PAS requests.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	locale - _string_ (Optional) The locale to use. Allows PAS_LOCALE_US, PAS_LOCALE_UK, PAS_LOCALE_CANADA, PAS_LOCALE_FRANCE, PAS_LOCALE_GERMANY, PAS_LOCALE_JAPAN
	 *
	 * Examples:
	 * 	example::pas/set_locale.phpt:
	 * 	example::pas/set_locale7.phpt:
	 *
	 * Returns:
	 * 	void
	 */
	public function set_locale($locale = null)
	{
		$this->locale = $locale;
	}


	/*%******************************************************************************************%*/
	// CORE FUNCTIONALITY

	/**
	 * Method: authenticate()
	 * 	Construct a URL to request from Amazon, request it, and return a formatted response.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	action - _string_ (Required) Indicates the action to perform.
	 * 	opt - _array_ (Optional) Associative array of parameters. See the individual methods for allowed keys.
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 */
	public function pas_authenticate($action, $opt = null, $locale = null)
	{
		// If there is no locale, use the default one.
		if ($locale === null)
		{
			// Was this set with set_locale()?
			if ($this->locale !== null)
			{
				$locale = $this->locale;
			}

			// Fall back to the one set in the config file.
			else
			{
				$locale = (defined('AWS_DEFAULT_LOCALE')) ? AWS_DEFAULT_LOCALE : null;
			}
		}

		// Determine the hostname
		switch ($locale)
		{
			// United Kingdom
			case PAS_LOCALE_UK:
				$hostname = 'ecs.amazonaws.co.uk';
				break;

			// Canada
			case PAS_LOCALE_CANADA:
				$hostname = 'ecs.amazonaws.ca';
				break;

			// France
			case PAS_LOCALE_FRANCE:
				$hostname = 'ecs.amazonaws.fr';
				break;

			// Germany
			case PAS_LOCALE_GERMANY:
				$hostname = 'ecs.amazonaws.de';
				break;

			// Japan
			case PAS_LOCALE_JAPAN:
				$hostname = 'ecs.amazonaws.jp';
				break;

			// Default to United States
			default:
				$hostname = 'ecs.amazonaws.com';
				break;
		}

		// Use alternate hostname, if one exists.
		if ($this->hostname)
		{
			$hostname = $this->hostname;
		}

		$return_curl_handle = false;
		$key_prepend = 'AWSAccessKeyId=' . $this->key . '&';

		// Manage the key-value pairs that are used in the query.
		$query['Operation'] = $action;
		$query['Service'] = 'AWSECommerceService';
		$query['SignatureMethod'] = 'HmacSHA256';
		$query['SignatureVersion'] = 2;
		$query['Timestamp'] = gmdate(DATE_FORMAT_ISO8601, time() + $this->adjust_offset);
		$query['Version'] = $this->api_version;

		// Merge in any options that were passed in
		if (is_array($opt))
		{
			$query = array_merge($query, $opt);
		}

		$return_curl_handle = isset($query['returnCurlHandle']) ? $query['returnCurlHandle'] : false;
		unset($query['returnCurlHandle']);

		// Do a case-insensitive, natural order sort on the array keys.
		uksort($query, 'strcasecmp');

		// Create the string that needs to be hashed.
		$canonical_query_string = $key_prepend . $this->util->to_signable_string($query);

		// Set the proper verb.
		$verb = HTTP_GET;

		// Set the proper path
		$path = '/onca/xml';

		// Prepare the string to sign
		$stringToSign = "$verb\n$hostname\n$path\n$canonical_query_string";

		// Hash the AWS secret key and generate a signature for the request.
		$query['Signature'] = $this->util->hex_to_base64(hash_hmac('sha256', $stringToSign, $this->secret_key));

		// Generate the querystring from $query
		$querystring = $key_prepend . $this->util->to_query_string($query);

		// Gather information to pass along to other classes.
		$helpers = array(
			'utilities' => $this->utilities_class,
			'request' => $this->request_class,
			'response' => $this->response_class,
		);

		// Compose the request.
		$request_url = $this->enable_ssl ? 'https://' : 'http://';
		$request_url .= $hostname;
		$request_url .= $path;
		$request_url .= '?' . $querystring;
		$request = new $this->request_class($request_url, $this->set_proxy, $helpers);
		$request->set_useragent(CLOUDFUSION_USERAGENT);

		// If we have a "true" value for returnCurlHandle, do that instead of completing the request.
		if ($return_curl_handle)
		{
			return $request->prep_request();
		}

		// Send!
		$request->send_request();

		// Prepare the response.
		$headers = $request->get_response_header();
		$headers['x-tarzan-requesturl'] = $request_url;
		$headers['x-tarzan-stringtosign'] = $stringToSign;
		$data = new $this->response_class($headers, new SimpleXMLElement($request->get_response_body()), $request->get_response_code());

		// Return!
		return $data;
	}


	/*%******************************************************************************************%*/
	// BROWSE NODE LOOKUP

	/**
	 * Method: browse_node_lookup()
	 * 	Given a browse node ID, <browse_node_lookup()> returns the specified browse node's name, children, and ancestors. The names and browse node IDs of the children and ancestor browse nodes are also returned. <browse_node_lookup()> enables you to traverse the browse node hierarchy to find a browse node.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	browse_node_id - _integer_ (Required) A positive integer assigned by Amazon that uniquely identifies a product category.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'BrowseNodeInfo' (default), 'NewReleases', 'TopSellers'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_browse_node_lookup.php:
	 * 	example::pas/browse_node_lookup.phpt:
	 * 	example::pas/browse_node_lookup2.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/BrowseNodeLookup.html
	 */
	public function browse_node_lookup($browse_node_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['BrowseNodeId'] = $browse_node_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('BrowseNodeLookup', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// CART METHODS

	/**
	 * Method: cart_add()
	 * 	Enables you to add items to an existing remote shopping cart. <cart_add()> can only be used to place a new item in a shopping cart. It cannot be used to increase the quantity of an item already in the cart. If you would like to increase the quantity of an item that is already in the cart, you must use the <cart_modify()> operation.
	 *
	 * 	You add an item to a cart by specifying the item's OfferListingId, or ASIN and ListItemId. Once in a cart, an item can only be identified by its CartItemId. That is, an item in a cart cannot be accessed by its ASIN or OfferListingId. CartItemId is returned by <cart_create()>, <cart_get()>, and <cart_add()>.
	 *
	 * 	To add items to a cart, you must specify the cart using the CartId and HMAC values, which are returned by the <cart_create()> operation.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	cart_id - _string_ (Required) Alphanumeric token returned by <cart_create()> that identifies a cart.
	 * 	hmac - _string_ (Required) Encrypted alphanumeric token returned by <cart_create()> that authorizes access to a cart.
	 * 	offer_listing_id - _string|array_ (Required) Either a string containing the Offer ID to add, or an associative array where the Offer ID is the key and the quantity is the value. An offer listing ID is an alphanumeric token that uniquely identifies an item. Use the OfferListingId instead of an item's ASIN to add the item to the cart.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MergeCart - _boolean_ (Optional) A boolean value that when True specifies that the items in a customer's remote shopping cart are added to the customer's Amazon retail shopping cart. This occurs when the customer elects to purchase the items in their remote shopping cart. When the value is False the remote shopping cart contents are not added to the retail shopping cart. Instead, the customer is sent directly to the Order Pipeline when they elect to purchase the items in their cart. This parameter is valid only in the US locale. In all other locales, the parameter is invalid but the request behaves as though the value were set to True.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
 	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
	 * 	example::pas/help_cart_add.php:
 	 * 	example::pas/cart_add.phpt:
 	 * 	example::pas/cart_add2.phpt:
 	 * 	example::pas/cart_add3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CartAdd.html
	 */
	public function cart_add($cart_id, $hmac, $offer_listing_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['CartId'] = $cart_id;
		$opt['HMAC'] = $hmac;

		if (is_array($offer_listing_id))
		{
			$count = 1;
			foreach ($offer_listing_id as $offer => $quantity)
			{
				$opt['Item.' . $count . '.OfferListingId'] = $offer;
				$opt['Item.' . $count . '.Quantity'] = $quantity;

				$count++;
			}
		}
		else
		{
			$opt['Item.1.OfferListingId'] = $offer_listing_id;
			$opt['Item.1.Quantity'] = 1;
		}

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CartAdd', $opt, $locale);
	}

	/**
	 * Method: cart_clear()
	 * 	Enables you to remove all of the items in a remote shopping cart, including SavedForLater items. To remove only some of the items in a cart or to reduce the quantity of one or more items, use <cart_modify()>.
	 *
	 * 	To delete all of the items from a remote shopping cart, you must specify the cart using the CartId and HMAC values, which are returned by the <cart_create()> operation. A value similar to the HMAC, URLEncodedHMAC, is also returned. This value is the URL encoded version of the HMAC. This encoding is necessary because some characters, such as + and /, cannot be included in a URL. Rather than encoding the HMAC yourself, use the URLEncodedHMAC value for the HMAC parameter.
	 *
	 * 	<cart_clear()> does not work after the customer has used the PurchaseURL to either purchase the items or merge them with the items in their Amazon cart. Carts exist even though they have been emptied. The lifespan of a cart is 7 days since the last time it was acted upon. For example, if a cart created 6 days ago is modified, the cart lifespan is reset to 7 days.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	cart_id - _string_ (Required) Alphanumeric token returned by <cart_create()> that identifies a cart.
	 * 	hmac - _string_ (Required) Encrypted alphanumeric token returned by <cart_create()> that authorizes access to a cart.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MergeCart - _boolean_ (Optional) A boolean value that when True specifies that the items in a customer's remote shopping cart are added to the customer's Amazon retail shopping cart. This occurs when the customer elects to purchase the items in their remote shopping cart. When the value is False the remote shopping cart contents are not added to the retail shopping cart. Instead, the customer is sent directly to the Order Pipeline when they elect to purchase the items in their cart. This parameter is valid only in the US locale. In all other locales, the parameter is invalid but the request behaves as though the value were set to True.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_cart_clear.php:
	 * 	example::pas/cart_clear.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CartClear.html
	 */
	public function cart_clear($cart_id, $hmac, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['CartId'] = $cart_id;
		$opt['HMAC'] = $hmac;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CartClear', $opt, $locale);
	}

	/**
	 * Method: cart_create()
	 * 	Enables you to create a remote shopping cart. A shopping cart is the metaphor used by most e-commerce solutions. It is a temporary data storage structure that resides on Amazon servers. The structure contains the items a customer wants to buy. In Amazon Associates Web Service, the shopping cart is considered remote because it is hosted by Amazon servers. In this way, the cart is remote to the vendor's web site where the customer views and selects the items they want to purchase.
	 *
	 * 	Once you add an item to a cart by specifying the item's ListItemId and ASIN, or OfferListing ID, the item is assigned a CartItemId and accessible only by that value. That is, in subsequent requests, an item in a cart cannot be accessed by its ListItemId and ASIN, or OfferListingId.
	 *
	 * 	Because the contents of a cart can change for different reasons, such as item availability, you should not keep a copy of a cart locally. Instead, use the other cart operations to modify the cart contents. For example, to retrieve contents of the cart, which are represented by CartItemIds, use <cart_get()>.
	 *
	 * 	Available products are added as cart items. Unavailable items, for example, items out of stock, discontinued, or future releases, are added as SaveForLaterItems. No error is generated. The Amazon database changes regularly. You may find a product with an offer listing ID but by the time the item is added to the cart the product is no longer available. The checkout page in the Order Pipeline clearly lists items that are available and those that are SaveForLaterItems.
	 *
	 * 	It is impossible to create an empty shopping cart. You have to add at least one item to a shopping cart using a single <cart_create()> request. You can add specific quantities (up to 999) of each item. <cart_create()> can be used only once in the life cycle of a cart. To modify the contents of the cart, use one of the other cart operations.
	 *
	 * 	Carts cannot be deleted. They expire automatically after being unused for 7 days. The lifespan of a cart restarts, however, every time a cart is modified. In this way, a cart can last for more than 7 days. If, for example, on day 6, the customer modifies a cart, the 7 day countdown starts over.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	offer_listing_id - _string|array_ (Required) Either a string containing the Offer ID to add, or an associative array where the Offer ID is the key and the quantity is the value. An offer listing ID is an alphanumeric token that uniquely identifies an item. Use the OfferListingId instead of an item's ASIN to add the item to the cart.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MergeCart - _boolean_ (Optional) A boolean value that when True specifies that the items in a customer's remote shopping cart are added to the customer's Amazon retail shopping cart. This occurs when the customer elects to purchase the items in their remote shopping cart. When the value is False the remote shopping cart contents are not added to the retail shopping cart. Instead, the customer is sent directly to the Order Pipeline when they elect to purchase the items in their cart. This parameter is valid only in the US locale. In all other locales, the parameter is invalid but the request behaves as though the value were set to True.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_cart_create.php:
 	 * 	example::pas/cart_create.phpt:
 	 * 	example::pas/cart_create2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CartCreate.html
	 */
	public function cart_create($offer_listing_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();

		if (is_array($offer_listing_id))
		{
			$count = 1;
			foreach ($offer_listing_id as $offer => $quantity)
			{
				$opt['Item.' . $count . '.OfferListingId'] = $offer;
				$opt['Item.' . $count . '.Quantity'] = $quantity;

				$count++;
			}
		}
		else
		{
			$opt['Item.1.OfferListingId'] = $offer_listing_id;
			$opt['Item.1.Quantity'] = 1;
		}

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CartCreate', $opt, $locale);
	}

	/**
	 * Method: cart_get()
	 * 	Enables you to retrieve the IDs, quantities, and prices of all of the items, including SavedForLater items in a remote shopping cart.
	 *
	 * 	Because the contents of a cart can change for different reasons, such as availability, you should not keep a copy of a cart locally. Instead, use <cart_get()> to retrieve the items in a remote shopping cart. To retrieve the items in a cart, you must specify the cart using the CartId and HMAC values, which are returned in the <cart_create()> operation. A value similar to HMAC, URLEncodedHMAC, is also returned.
	 *
	 * 	This value is the URL encoded version of the HMAC. This encoding is necessary because some characters, such as + and /, cannot be included in a URL. Rather than encoding the HMAC yourself, use the URLEncodedHMAC value for the HMAC parameter.
	 *
	 * 	<cart_get()> does not work after the customer has used the PurchaseURL to either purchase the items or merge them with the items in their Amazon cart.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	cart_id - _string_ (Required) Alphanumeric token returned by <cart_create()> that identifies a cart.
	 * 	hmac - _string_ (Required) Encrypted alphanumeric token returned by <cart_create()> that authorizes access to a cart.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	CartItemId - _string_ (Optional) Alphanumeric token that uniquely identifies an item in a cart. Once an item, specified by an ASIN or OfferListingId, has been added to a cart, you must use the CartItemId to refer to it. The other identifiers will not work.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MergeCart - _boolean_ (Optional) A boolean value that when True specifies that the items in a customer's remote shopping cart are added to the customer's Amazon retail shopping cart. This occurs when the customer elects to purchase the items in their remote shopping cart. When the value is False the remote shopping cart contents are not added to the retail shopping cart. Instead, the customer is sent directly to the Order Pipeline when they elect to purchase the items in their cart. This parameter is valid only in the US locale. In all other locales, the parameter is invalid but the request behaves as though the value were set to True.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_cart_get.php:
	 * 	example::pas/cart_get.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CartGet.html
	 */
	public function cart_get($cart_id, $hmac, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['CartId'] = $cart_id;
		$opt['HMAC'] = $hmac;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CartGet', $opt, $locale);
	}

	/**
	 * Method: cart_modify()
	 * 	Enables you to change the quantity of items that are already in a remote shopping cart, move items from the active area of a cart to the SaveForLater area or the reverse, and change the MergeCart setting.
	 *
	 * 	To modify the number of items in a cart, you must specify the cart using the CartId and HMAC values that are returned in the <cart_create()> operation. A value similar to HMAC, URLEncodedHMAC, is also returned. This value is the URL encoded version of the HMAC. This encoding is necessary because some characters, such as + and /, cannot be included in a URL. Rather than encoding the HMAC yourself, use the URLEncodedHMAC value for the HMAC parameter.
	 *
	 * 	You can use <cart_modify()> to modify the number of items in a remote shopping cart by setting the value of the Quantity parameter appropriately. You can eliminate an item from a cart by setting the value of the Quantity parameter to zero. Or, you can double the number of a particular item in the cart by doubling its Quantity. You cannot, however, use <cart_modify()> to add new items to a cart.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	cart_id - _string_ (Required) Alphanumeric token returned by <cart_create()> that identifies a cart.
	 * 	hmac - _string_ (Required) Encrypted alphanumeric token returned by <cart_create()> that authorizes access to a cart.
	 * 	cart_item_id - _array_ (Required) Associative array that specifies an item to be modified in the cart where N is a positive integer between 1 and 10, inclusive. Up to ten items can be modified at a time. CartItemId is neither an ASIN nor an OfferListingId. It is, instead, an alphanumeric token returned by <cart_create()> and <cart_add()>. This parameter is used in conjunction with Item.N.Quantity to modify the number of items in a cart. Also, instead of adjusting the quantity, you can set 'SaveForLater' or 'MoveToCart' as actions instead.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	Action - _string_ (Optional) Change cart items to move items to the Saved-For-Later area, or change Saved-For- Later (SaveForLater) items to the active cart area (MoveToCart).
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	ListItemId - _string_ (Optional) The ListItemId parameter is returned by the ListItems response group. The parameter identifies an item on a list, such as a wishlist. To add this item to a cart, you must include in the <cart_create()> request the item's ASIN and ListItemId. The ListItemId includes the name and address of the list owner, which the ASIN alone does not.
	 * 	MergeCart - _boolean_ (Optional) A boolean value that when True specifies that the items in a customer's remote shopping cart are added to the customer's Amazon retail shopping cart. This occurs when the customer elects to purchase the items in their remote shopping cart. When the value is False the remote shopping cart contents are not added to the retail shopping cart. Instead, the customer is sent directly to the Order Pipeline when they elect to purchase the items in their cart. This parameter is valid only in the US locale. In all other locales, the parameter is invalid but the request behaves as though the value were set to True.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_cart_modify.php:
	 * 	example::pas/cart_modify.phpt:
	 * 	example::pas/cart_modify2.phpt:
	 * 	example::pas/cart_modify3.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CartModify.html
	 */
	public function cart_modify($cart_id, $hmac, $cart_item_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['CartId'] = $cart_id;
		$opt['HMAC'] = $hmac;

		if (is_array($cart_item_id))
		{
			$count = 1;
			foreach ($cart_item_id as $offer => $quantity)
			{
				$action = is_numeric($quantity) ? 'Quantity' : 'Action';

				$opt['Item.' . $count . '.CartItemId'] 	= $offer;
				$opt['Item.' . $count . '.' . $action] 		= $quantity;

				$count++;
			}
		}
		else
		{
			throw new PAS_Exception('$cart_item_id MUST be an array. See the ' . CLOUDFUSION_NAME . ' documentation for more details.');
		}

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CartModify', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// CUSTOMER CONTENT METHODS

	/**
	 * Method: customer_content_lookup()
	 * 	For a given customer ID, the <customer_content_lookup()> operation retrieves all of the information a customer has made public about themselves on Amazon. Such information includes some or all of the following: About Me, Birthday, City, State, Country, Customer Reviews, Customer ID, Name, Nickname, Wedding Registry, or WishList. To find a customer ID, use the <customer_content_search()> operation.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	customer_id - _string_ (Required) An alphanumeric token assigned by Amazon that uniquely identifies a customer. Only one customer_id can be submitted at a time in <customer_content_lookup()>.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'CustomerInfo' (default), 'CustomerReviews', 'CustomerLists', 'CustomerFull', 'TaggedGuides', 'TaggedItems', 'TaggedListmaniaLists', 'TagsSummary', or 'Tags'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	ReviewPage - _integer_ (Optional) A positive integer that specifies the page of reviews to read. There are ten reviews per page. For example, to read reviews 11 through 20, specify ReviewPage=2. The total number of pages is returned in the TotalPages response tag.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TagPage - _integer_ (Optional) Specifies the page of results to return. There are ten results on a page. The maximum page number is 400.
	 * 	TagsPerPage - _integer_ (Optional) The number of tags to return that are associated with a specified item.
	 * 	TagSort - _string_ (Optional) Specifies the sorting order for the results. Allows 'FirstUsed', 'LastUsed', 'Name', or 'Usages' (default)
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_customer_content_lookup.php:
 	 * 	example::pas/customer_content_lookup.phpt:
 	 * 	example::pas/customer_content_lookup2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CustomerContentLookup.html
	 * 	Related - <customer_content_lookup()>, <customer_content_search()>
	 */
	public function customer_content_lookup($customer_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['CustomerId'] = $customer_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CustomerContentLookup', $opt, $locale);
	}

	/**
	 * Method: customer_content_search()
	 * 	For a given customer Email address or name, the <customer_content_search()> operation returns matching customer IDs, names, nicknames, and residence information (city, state, and country). In general, supplying an Email address returns unique results whereas supplying a name more often returns multiple results. This operation is US-only.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	email_name - _string_ (Required) Either the email address or the name of the customer you want to look up the ID for.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	CustomerPage - _integer_ (Optional) A positive integer that specifies the page of customer IDs to return. Up to twenty customer IDs are returned per page. Defaults to 1.
	 * 	Email - _string_ (Optional) Besides the first parameter, you can set the email address here.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	Name - _string_ (Optional) Besides the first parameter, you can set the name here.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_customer_content_search.php:
 	 * 	example::pas/customer_content_search.phpt:
 	 * 	example::pas/customer_content_search2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/CustomerContentSearch.html
	 * 	Related - <customer_content_lookup()>, <customer_content_search()>
	 */
	public function customer_content_search($email_name, $opt = null)
	{
		if (!$opt) $opt = array();

		if (strpos($email_name, '@'))
		{
			$opt['Email'] = $email_name;
		}
		else
		{
			$opt['Name'] = $email_name;
		}

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('CustomerContentSearch', $opt, PAS_LOCALE_US);
	}


	/*%******************************************************************************************%*/
	// HELP

	/**
	 * Method: help()
	 * 	The Help operation provides information about PAS operations and response groups. For operations, Help lists required and optional request parameters, as well as default and optional response groups the operation can use. For response groups, Help lists the operations that can use the response group as well as the response tags returned by the response group in the XML response.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
 	 * 	about - _string_ (Required) Specifies the operation or response group about which you want more information. Allows all PAS operations, all PAS response groups.
	 * 	help_type - _string_ (Required) Specifies whether the help topic is an operation or response group. HelpType and About values must both be operations or response groups, not a mixture of the two. Allows 'Operation' or 'ResponseGroup'.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'Request' or 'Help'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/Help.html
	 */
	public function help($about, $help_type, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();

		$opt['About'] = $about;
		$opt['HelpType'] = $help_type;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('Help', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// ITEM METHODS

	/**
	 * Method: item_lookup()
	 * 	Given an Item identifier, the ItemLookup operation returns some or all of the item attributes, depending on the response group specified in the request. By default, <item_lookup()> returns an item’s ASIN, DetailPageURL, Manufacturer, ProductGroup, and Title of the item.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	item_id - _string_ (Required) A positive integer that unique identifies an item. The meaning of the number is specified by IdType. That is, if IdType is ASIN, the ItemId value is an ASIN. If ItemId is an ASIN, a search index cannot be specified in the request.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	Condition - _string_ (Optional) Specifies an item's condition. If Condition is set to "All," a separate set of responses is returned for each valid value of Condition. The default value is "New" (not "All"). So, if your request does not return results, consider setting the value to "All." When the value is "New," the ItemSearch Availability parameter cannot be set to "Available." Amazon only sells items that are "New." Allows 'New', 'Used', 'Collectible', 'Refurbished', and 'All'. Defaults to 'New'.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	IdType - _string_ (Optional) Type of item identifier used to look up an item. All IdTypes except ASINx require a SearchIndex to be specified. SKU requires a MerchantId to be specified also. Allows 'ASIN', 'SKU', 'UPC', 'EAN', 'ISBN' (US only, when search index is Books), and 'JAN'. UPC is not valid in the Canadian locale. Defaults to 'ASIN'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	OfferPage - _string_ (Optional) Page of offers returned. There are 10 offers per page. To examine offers 11 trough 20, for example, set OfferPage to 2. Allows 1 through 100.
	 * 	RelatedItemsPage - _integer_ (Optional) This optional parameter is only valid when the RelatedItems response group is used. Each ItemLookup request can return, at most, ten related items. The RelatedItemsPage value specifies the set of ten related items to return. A value of 2, for example, returns the second set of ten related items.
	 * 	RelationshipType - _string_ (Optional) This parameter is required when the RelatedItems response group is used. The type of related item returned is specified by the RelationshipType parameter. Sample values include Episode, Season, and Tracks. For a complete list of types, go to the documentation for "Relationship Types". Required when 'RelatedItems' response group is used.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Check the documentation for all allowed values.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	ReviewPage - _integer_ (Optional) Page of reviews returned. There are 5 reviews per page. To examine reviews 6 through 10, for example, set ReviewPage to 2. Allows 1 through 20.
	 * 	ReviewSort - _string_ (Optional) Specifies the order in which Reviews are sorted in the return. Allows '-HelpfulVotes', 'HelpfulVotes', '-OverallRating', 'OverallRating', 'SubmissionDate' and '-SubmissionDate'. Defaults to '-SubmissionDate'.
	 * 	SearchIndex - _string_ (Optional) The product category to search. Constraint: If ItemIds an ASIN, a search index cannot be specified in the request. Required for for non-ASIN ItemIds. Allows any valid search index. See the "Search Indices" documentation page for more details.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TagPage - _integer_ (Optional) Specifies the page of results to return. There are ten results on a page. Allows 1 through 400.
	 * 	TagsPerPage - _integer_ (Optional) The number of tags to return that are associated with a specified item.
	 * 	TagSort - _string_ (Optional) Specifies the sorting order for the results. Allows 'FirstUsed', '-FirstUsed', 'LastUsed', '-LastUsed', 'Name', '-Name', 'Usages', and '-Usages'. Defaults to '-Usages'.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	VariationPage - _string_ (Optional) Page number of variations returned by ItemLookup. By default, ItemLookup returns all variations. Use VariationPage to return a subsection of the response. There are 10 variations per page. To examine offers 11 trough 20, for example, set VariationPage to 2. Allows 1 through 150.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_item_lookup.php:
 	 * 	example::pas/item_lookup.phpt:
 	 * 	example::pas/item_lookup2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/ItemLookup.html
	 * 	Related - <item_lookup()>, <item_search()>
	 */
	public function item_lookup($item_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['ItemId'] = $item_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('ItemLookup', $opt, $locale);
	}

	/**
	 * Method: item_search()
	 * 	The <item_search()> operation returns items that satisfy the search criteria, including one or more search indices. <item_search()> is the operation that is used most often in requests. In general, when trying to find an item for sale, you use this operation.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	keywords - _string_ (Required) A word or phrase associated with an item. The word or phrase can be in various product fields, including product title, author, artist, description, manufacturer, and so forth. When, for example, the search index equals "MusicTracks", the Keywords parameter enables you to search by song title.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	Actor - _string_ (Optional) Name of an actor associated with the item. You can enter all or part of the name.
	 * 	Artist - _string_ (Optional) 	Name of an artist associated with the item. You can enter all or part of the name.
	 * 	AudienceRating - _string_ (Optional) Movie ratings based on MPAA ratings or age, depending upon the locale. You may specify one or more values in a comma-separated list.
	 * 	Author - _string_ (Optional) Name of an author associated with the item. You can enter all or part of the name.
	 * 	Availability - _string_ (Optional) Enables ItemSearch to return only those items that are available. This parameter must be used in combination with a merchant ID and Condition. When Availability is set to "Available," the Condition parameter cannot be set to "New".
	 * 	Brand - _string_ (Optional) Name of a brand associated with the item. You can enter all or part of the name.
	 * 	BrowseNode - _integer_ (Optional) Browse nodes are positive integers that identify product categories.
	 * 	City - _string_ (Optional) Name of a city associated with the item. You can enter all or part of the name. This parameter only works in the US locale.
	 * 	Composer - _string_ (Optional) Name of an composer associated with the item. You can enter all or part of the name.
	 * 	Condition - _string_ (Optional) Use the Condition parameter to filter the offers returned in the product list by condition type. By default, Condition equals "New". If you do not get results, consider changing the value to "All. When the Availability parameter is set to "Available," the Condition parameter cannot be set to "New". ItemSearch returns up to ten search results at a time. Allows 'New', 'Used', 'Collectible', 'Refurbished', 'All'.
	 * 	Conductor - _string_ (Optional) Name of a conductor associated with the item. You can enter all or part of the name.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	Director - _string_ (Optional) Name of a director associated with the item. You can enter all or part of the name.
	 * 	ItemPage - _integer_ (Optional) Retrieves a specific page of items from all of the items in a response. Up to ten items are returned on a page unless Condition equals "All." In that case, returns up to three results per Condition, for example, three new, three used, three refurbished, and three collectible items. Or, for example, if there are no collectible or refurbished items being offered, returns three new and three used items. The total number of pages of items found is returned in the TotalPages response tag. Allows 1 through 400.
	 * 	Keywords - _string_ (Optional) A word or phrase associated with an item. The word or phrase can be in various product fields, including product title, author, artist, description, manufacturer, and so forth. When, for example, the search index equals "MusicTracks," the Keywords parameter enables you to search by song title.
	 * 	Manufacturer - _string_ (Optional) Name of a manufacturer associated with the item. You can enter all or part of the name.
	 * 	MaximumPrice - _string_ (Optional) Specifies the maximum price of the items in the response. Prices are in terms of the lowest currency denomination, for example, pennies.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	MinimumPrice - _string_ (Optional) Specifies the minimum price of the items in the response. Prices are in terms of the lowest currency denomination, for example, pennies.
	 * 	Neighborhood - _string_ (Optional) Name of a neighborhood You can enter all or part of the name. The neighborhoods are located in one of the valid values for City.
	 * 	Orchestra - _string_ (Optional) Name of an orchestra associated with the item. You can enter all or part of the name.
	 * 	PostalCode - _string_ (Optional) Postal code of the merchant. In the US, the postal code is the postal code. This parameter enables you to search for items sold in a specified region of a country.
	 * 	Power - _string_ (Optional) Performs a book search using a complex query string. Only works when the search index is set equal to "Books".
	 * 	Publisher - _string_ (Optional) Name of a publisher associated with the item. You can enter all or part of the name.
	 * 	RelatedItemsPage - _integer_ (Optional) This optional parameter is only valid when the RelatedItems response group is used. Each ItemLookup request can return, at most, ten related items. The RelatedItemsPage value specifies the set of ten related items to return. A value of 2, for example, returns the second set of ten related items.
	 * 	RelationshipType - _string_ (Optional; Required when RelatedItems response group is used) This parameter is required when the RelatedItems response group is used. The type of related item returned is specified by the RelationshipType parameter. Sample values include Episode, Season, and Tracks. A complete list of values follows this table.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	ReviewSort - _string_ (Optional) Sorts reviews based on the value of the parameter. '-HelpfulVotes', 'HelpfulVotes', '-OverallRating', 'OverallRating', 'Rank', '-Rank', '-SubmissionDate', 'SubmissionDate'.
	 * 	SearchIndex - _string_ (Optional) The product category to search. Many ItemSearch parameters are valid with only specific values of SearchIndex.
	 * 	Sort - _string_ (Optional) Means by which the items in the response are ordered.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TagPage - _integer_ (Optional) Specifies the page of results to return. There are ten results on a page. The maximum page number is 400.
	 * 	TagsPerPage - _integer_ (Optional) The number of tags to return that are associated with a specified item.
	 * 	TagSort - _string_ (Optional) Specifies the sorting order for the results. Allows 'FirstUsed', '-FirstUsed', 'LastUsed', '-LastUsed', 'Name', '-Name', 'Usages', and '-Usages'. To sort items in descending order, prefix the values with a negative sign (-).
	 * 	TextStream - _string_ (Optional) A search based on two or more words. Picks out of the block of text up to ten keywords and returns up to ten items that match those keywords. For example, if five keywords are found, two items for each keyword are returned. Only one page of results is returned so ItemPage does not work with TextStream.
	 * 	Title - _string_ (Optional) The title associated with the item. You can enter all or part of the title. Title searches are a subset of Keyword searches. If a Title search yields insufficient results, consider using a Keywords search.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	VariationPage - _integer_ (Optional) Retrieves a specific page of variations returned by ItemSearch. By default, ItemSearch returns all variations. Use VariationPage to return a subsection of the response. There are 10 variations per page. To examine offers 11 trough 20, for example, set VariationPage to 2. The total number of pages is returned in the TotalPages element.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_item_search.php:
 	 * 	example::pas/item_search.phpt:
 	 * 	example::pas/item_search2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/ItemSearch.html
	 * 	Related - <item_lookup()>, <item_search()>
	 */
	public function item_search($keywords, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['Keywords'] = $keywords;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		if (!isset($opt['SearchIndex']) || empty($opt['SearchIndex']))
		{
			$opt['SearchIndex'] = 'none';
		}

		return $this->pas_authenticate('ItemSearch', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// LIST METHODS

	/**
	 * Method: list_lookup()
	 * 	The <list_lookup()> operation returns, by default, summary information about a list that you specify in the request.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	list_id - _string_ (Required) Number that uniquely identifies a list.
	 * 	list_type - _string_ (Required) Type of list. Accepts 'WeddingRegistry', 'Listmania', 'WishList'.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	Condition - _string_ (Optional) Specifies an item's condition. If Condition is set to "All", a separate set of responses is returned for each valid value of Condition. Allows 'All', 'Collectible', 'Refurbished', or 'Used'.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	IsOmitPurchasedItems - _boolean_ (Optional) If you set IsOmitPurchasedItems to TRUE, items on a wishlist that have been purchased will not be returned. Only those items that have not been purchased or those for which the entire quantity has not been purchased are returned. Defaults to FALSE.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ProductGroup - _string_ (Optional) Category of the item, for example, 'Book' or 'DVD'.
	 * 	ProductPage - _integer_ (Optional) Retrieves a specific page of lists returned. There are ten lists per page.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Sort - _string_ (Optional) Means by which the list items in the response are ordered. Use only with wishlists. Allows 'DateAdded', 'LastUpdated', 'Price', and 'Priority'.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_list_lookup.php:
 	 * 	example::pas/list_lookup.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/ListLookup.html
	 * 	Related - <list_lookup()>, <list_search()>
	 */
	public function list_lookup($list_id, $list_type, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['ListId'] = isset($list_id) ? $list_id : '';
		$opt['ListType'] = isset($list_type) ? $list_type : '';

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('ListLookup', $opt, $locale);
	}

	/**
	 * Method: list_search()
	 * 	Given a customer name or Email address, the <list_search()> operation returns the associated list ID(s) but not the list items. To find those, use the list ID returned by <list_search()> with <list_lookup()>.
	 *
	 * 	Specifying a full name or just a first or last name in the request typically returns multiple lists belonging to different people. Using Email as the identifier produces more filtered results.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	email_name - _string_ (Required) Name or email address of the list creator. This parameter is not supported for the BabyRegistry. Set this to null if you want to explicitly pass FirstName and LastName for $opt.
	 * 	list_type - _string_ (Required) Specifies the kind of list you are retrieving. Allows 'BabyRegistry', 'WeddingRegistry', 'WishList'.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	City - _string_ (Optional) City in which the list creator lives.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	Email - _string_ (Optional) E-mail address of the list creator. This parameter is not supported for the BabyRegistry.
	 * 	FirstName - _string_ (Optional) First name of the list creator. Returns all list owners that have FirstName in their first name. For example, specifying 'John', will return first names of 'John', 'Johnny', and 'Johnson'.
	 * 	LastName - _string_ (Optional) Last name of the list creator. ListSearch returns all list owners that have LastName in their last name. For example, specifying 'Ender', will return the last names of 'Ender', 'Enders', and 'Enderson'.
	 * 	ListPage - _integer_ (Optional) Retrieve a specific page of list IDs. There are ten list IDs per page. The total number of pages is returned in the TotalPages response tag. The default is to return the first page. Allows 1 through 20.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	State - _string_ (Optional) State in which the list creator lives.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_list_search.php:
 	 * 	example::pas/list_search.phpt:
 	 * 	example::pas/list_search2.phpt:
 	 * 	example::pas/list_search3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/ListSearch.html
	 * 	Related - <list_lookup()>, <list_search()>
	 */
	public function list_search($email_name, $list_type, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		if (strpos($email_name, '@'))
		{
			$opt['Email'] = $email_name;
		}
		else
		{
			$opt['Name'] = isset($email_name) ? $email_name : '';
		}

		$opt['ListType'] = $list_type;

		return $this->pas_authenticate('ListSearch', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// SELLER METHODS

	/**
	 * Method: seller_listing_lookup()
	 * 	Enables you to return information about a seller's listings, including product descriptions, availability, condition, and quantity available. The response also includes the seller's nickname. Each request requires a seller ID.
	 *
	 * 	You can also find a seller's items using ItemLookup. There are, however, some reasons why it is better to use <seller_listing_lookup()>: (a) <seller_listing_lookup()> enables you to search by seller ID. (b) <seller_listing_lookup()> returns much more information than <item_lookup()>.
	 *
	 * 	This operation only works with sellers who have less than 100,000 items for sale. Sellers that have more items for sale should use, instead of Amazon Associates Web Service, other APIs, including the Amazon Inventory Management System, and the Merchant@ API.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	item_id - _string_ (Optional) Number that uniquely identifies an item. The valid value depends on the value for IdType. Allows an Exchange ID, a Listing ID, an ASIN, or a SKU.
	 * 	id_type - _string_ (Optional) Use the IdType parameter to specify the value type of the Id parameter value. If you are looking up an Amazon Marketplace item, use Exchange, ASIN, or SKU as the value for IdType. Discontinued, out of stock, or unavailable products will not be returned if IdType is Listing, SKU, or ASIN. Those products will be returned, however, if IdType is Exchange. Allows 'Exchange', 'Listing', 'ASIN', 'SKU'.
	 * 	seller_id - _string_ (Optional) Alphanumeric token that uniquely identifies a seller. This parameter limits the results to a single seller ID.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 *  ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_seller_listing_lookup.php:
 	 * 	example::pas/seller_listing_lookup.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/SellerListingLookup.html
	 * 	Related - <seller_listing_search()>, <seller_lookup()>
	 */
	public function seller_listing_lookup($item_id, $id_type, $seller_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['Id'] = $item_id;
		$opt['IdType'] = $id_type;
		$opt['SellerId'] = $seller_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('SellerListingLookup', $opt, $locale);
	}

	/**
	 * Method: seller_listing_search()
	 * 	Enables you to search for items offered by specific sellers. You cannot use <seller_listing_search()> to look up items sold by merchants. To look up an item sold by a merchant, use <item_lookup()> or <item_search()> along with the MerchantId parameter.
	 *
	 * 	<seller_listing_search()> returns the listing ID or exchange ID of an item. Typically, you use those values with <seller_listing_lookup()> to find out more about those items.
	 *
	 * 	Each request returns up to ten items. By default, the first ten items are returned. You can use the ListingPage parameter to retrieve additional pages of (up to) ten listings. To use Amazon Associates Web Service, sellers must have less than 100,000 items for sale. Sellers that have more items for sale should use, instead of Amazon Associates Web Service, other seller APIs, including the Amazon Inventory Management System, and the Merchant@ API.
	 *
	 * 	<seller_listing_search()> requires a seller ID, which means that you cannot use this operation to search across all sellers. Amazon Associates Web Service does not have a seller-specific operation that does this. To search across all sellers, use <item_lookup()> or <item_search()>.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	seller_id - _string_ (Required) An alphanumeric token that uniquely identifies a seller. These tokens are created by Amazon and distributed to sellers.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	ListingPage - _integer_ (Optional) Page of the response to return. Up to ten lists are returned per page. For customers that have more than ten lists, more than one page of results are returned. By default, the first page is returned. To return another page, specify the page number. Allows 1 through 500.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	OfferStatus - _string_ (Optional) Specifies whether the product is available (Open), or not (Closed.) Closed products are those that are discontinued, out of stock, or unavailable. Defaults to 'Open'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Sort - _string_ (Optional) Use the Sort parameter to specify how your seller listing search results will be ordered. The -bfp (featured listings - default), applies only to the US, UK, and DE locales. Allows '-startdate', 'startdate', '+startdate', '-enddate', 'enddate', '-sku', 'sku', '-quantity', 'quantity', '-price', 'price |+price', '-title', 'title'.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Title - _string_ (Optional) Searches for products based on the product's name. Keywords and Title are mutually exclusive; you can have only one of the two in a request.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_seller_listing_search.php:
 	 * 	example::pas/seller_listing_search.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/SellerListingSearch.html
	 * 	Related - <seller_listing_lookup()>, <seller_lookup()>
	 */
	public function seller_listing_search($seller_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['SellerId'] = $seller_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('SellerListingSearch', $opt, $locale);
	}

	/**
	 * Method: seller_lookup()
	 * 	Returns detailed information about sellers and, in the US locale, merchants. To lookup a seller, you must use their seller ID. The information returned includes the seller's name, average rating by customers, and the first five customer feedback entries. <seller_lookup()> will not, however, return the seller's e-mail or business addresses.
	 *
	 * 	A seller must enter their information. Sometimes, sellers do not. In that case, <seller_lookup()> cannot return some seller-specific information.
	 *
	 * 	To look up more than one seller in a single request, insert a comma-delimited list of up to five seller IDs in the SellerId parameter of the REST request. Customers can rate sellers. 5 is the best rating; 0 is the worst. The rating reflects the customer's experience with the seller. The <seller_lookup()> operation, by default, returns review comments by individual customers.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	seller_id - _string_ (Required) An alphanumeric token that uniquely identifies a seller. These tokens are created by Amazon and distributed to sellers.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	FeedbackPage - _string_ (Optional) Specifies the page of reviews to return. Up to five reviews are returned per page. The first page is returned by default. To access additional pages, use this parameter to specify the desired page. The maximum number of pages that can be returned is 10 (50 feedback items). Allows 1 through 10.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_seller_lookup.php:
 	 * 	example::pas/seller_lookup.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/SellerLookup.html
	 * 	Related - <seller_listing_lookup()>, <seller_listing_search()>
	 */
	public function seller_lookup($seller_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['SellerId'] = $seller_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('SellerLookup', $opt, $locale);
	}


	/*%******************************************************************************************%*/
	// VEHICLE METHODS

	/**
	 * Method: vehicle_part_lookup()
	 * 	Given a car part, <vehicle_part_lookup()> returns the vehicle models and years the part works in. For example, one carburetor might work in the same vehicle model over a five year period. You can page through the parts returned using the parameters, Count and FitmentPage. This operation is US-only.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	make_id - _integer_ (Required) Identifier that uniquely identifies the make of the car. This can be retrieved by using <vehicle_search()> first.
	 * 	model_id - _integer_ (Required) Identifier that uniquely identifies the model of the car. This can be retrieved by using <vehicle_search()> first.
	 * 	year - _integer_ (Required) The year of the car the part works in.
	 * 	item_id - _string_ (Required) The part ID to lookup. This is typically an ASIN, and can be looked up with <vehicle_part_search()>.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	BedId - _integer_ (Optional) Identifier that uniquely identifies the bed style of a truck. This parameter does not pertain to cars.
	 * 	BodyStyleId - _integer_ (Optional) 	Identifier that uniquely identifies the body style of the car.
	 * 	BrakesId - _integer_ (Optional) Identifier that uniquely identifies the brake type on a car.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	DriveTypeId - _integer_ (Optional) Identifier that uniquely identifies the type of drive on the car. A drive type, for example, is four wheel drive.
	 * 	EngineId - _integer_ (Optional) Identifier that uniquely identifies the type of engine in the car. An engine type would be, for example, the piston displacement, like 409 cu. inches.
	 * 	FitmentCount - _integer_ (Optional) Specifies the number of Fitments returned per page of results. Fitments are a combination of car characteristics, including make, model, year, and trim. This parameter is only used with the Fitments response group.
	 * 	FitmentPage - _integer_ (Optional) The page number of the Fitments returned. Use FitmentPage with Count to page through the results.
	 * 	IdType - _string_ (Optional) Specifies the type of ID.
	 * 	MakeId - _integer_ (Optional; Required when using the VehiclePartFit response group) Identifier that uniquely identifies the make of the car.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	MfrBodyCodeId - _integer_ (Optional) Identifier that uniquely identifies the manufacturer's car body code.
	 * 	ModelId - _integer_ (Optional; Required when using the VehiclePartFit response group) Identifier that uniquely identifies the model of the car.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'Fitments', 'HasPartCompatibility', and 'VehiclePartFit'. Defaults to 'HasPartCompatibility'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	SpringTypesId - _integer_ (Optional) Identifier that uniquely identifies the type of spring shocks in the car.
	 * 	SteeringId - _integer_ (Optional) Identifier that uniquely identifies the steering type of the car. A steering type would be power steering.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TransmissionId - _integer_ (Optional) Identifier that uniquely identifies the transmission type used in the car.
	 * 	TrimId - _integer_ (Optional) Identifier that uniquely identifies the trim on the car. Trim generally refers to a package of car options (e.g. Volvo GL vs. Volvo DL). Using this parameter helps narrow responses.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	WheelbaseId - _integer_ (Optional) Identifier that uniquely identifies the car's wheelbase.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 * 	Year - _integer_ (Optional; Required when using the VehiclePartFit response group) The year of the vehicle.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_vehicle_part_lookup.php:
	 * 	example::pas/vehicle_part_lookup.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/VehiclePartLookup.html
	 * 	Related - <vehicle_part_search()>, <vehicle_search()>
	 */
	public function vehicle_part_lookup($make_id, $model_id, $year, $item_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['MakeId'] = $make_id;
		$opt['ModelId'] = $model_id;
		$opt['Year'] = $year;
		$opt['ItemId'] = $item_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('VehiclePartLookup', $opt, PAS_LOCALE_US);
	}

	/**
	 * Method: vehicle_part_search()
	 * 	Returns the parts that work in the car. For example, a 2008 GMC Yukon has a list of parts that can work in it. The more parameters that you supply in the request, the narrower your results.
	 *
	 * 	VehicleSearch has additional, optional parameters to narrow the results, for example BrowseNodeId and Brand. You can page through the vehicles returned using the parameters, Count, PartPageDirection, and FromItemId.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	make_id - _integer_ (Required) Identifier that uniquely identifies the make of the car. This can be retrieved by using <vehicle_search()> first.
	 * 	model_id - _integer_ (Required) Identifier that uniquely identifies the model of the car. This can be retrieved by using <vehicle_search()> first.
	 * 	year - _integer_ (Required) The year of the car the part works in.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	BedId - _integer_ (Optional) Identifier that uniquely identifies the bed style of a truck. This parameter does not pertain to cars.
	 * 	BodyStyleId - _integer_ (Optional) 	Identifier that uniquely identifies the body style of the car.
	 * 	BrakesId - _integer_ (Optional) Identifier that uniquely identifies the brake type on a car.
	 * 	Brand - _integer_ (Optional) The brand of the company that made the part.
	 * 	BrowseNodeId - _integer_ (Optional) Identifier that uniquely identifies the BrowseNode to which the part belongs.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	Count - _integer_ (Optional) Controls the number of items returned. Use Count with FitmentPage to page through the results.
	 * 	DriveTypeId - _integer_ (Optional) Identifier that uniquely identifies the type of drive on the car. A drive type, for example, is four wheel drive.
	 * 	EngineId - _integer_ (Optional) Identifier that uniquely identifies the type of engine in the car. An engine type would be, for example, the piston displacement, like 409 cu. inches.
	 * 	FromItemId - _integer_ (Optional) An ASIN that identifies where to start or end the next page of returned results. If PartPageDirection is "Next," the ASIN after this one starts the next set of up to 15 returned results. If PartPageDirection is "Previous," the ASIN is one after the previous set of up to fifteen results returned.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	MfrBodyCodeId - _integer_ (Optional) Identifier that uniquely identifies the manufacturer's car body code.
	 * 	PartPageDirection - _string_ (Optional) Specifies the direction, forward or backward, to go from FromItemId in presenting the next set of (up to) fifteen results.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'PartBrowseNodeBinsSummary', 'PartBrandBinsSummary', 'HasPartCompatibility', 'VehiclePartFit', and 'VehicleParts'. Defaults to 'VehicleParts'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	SpringTypesId - _integer_ (Optional) Identifier that uniquely identifies the type of spring shocks in the car.
	 * 	SteeringId - _integer_ (Optional) Identifier that uniquely identifies the steering type of the car. A steering type would be power steering.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TransmissionId - _integer_ (Optional) Identifier that uniquely identifies the transmission type used in the car.
	 * 	TrimId - _integer_ (Optional; Sometimes Required) Identifier that uniquely identifies the trim on the car. Required when using one of the following parameters: 'BedId', 'BodyStyleId', 'BrakesId', 'DriveTypeId', 'EngineId', 'MfrBodyCodeId', 'SpringTypesId', 'SteeringId', 'TransmissionId', 'WheelbaseId'.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	WheelbaseId - _integer_ (Optional) Identifier that uniquely identifies the car's wheelbase.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_vehicle_part_search.php:
 	 * 	example::pas/vehicle_part_search.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/VehiclePartSearch.html
	 * 	Related - <vehicle_part_lookup()>, <vehicle_search()>
	 */
	public function vehicle_part_search($make_id, $model_id, $year, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['MakeId'] = $make_id;
		$opt['ModelId'] = $model_id;
		$opt['Year'] = $year;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('VehiclePartSearch', $opt, PAS_LOCALE_US);
	}

	/**
	 * Method: vehicle_search()
	 * 	Returns all vehicles that match the specified values for year, make, model, and trim. The request can have one or more of those parameters—the more parameters, the narrower the results. Typically, VehicleSearch requests are repeated, first with the year to get the make, then with the year and make to get the model, and then with the year, make, and model, to get the trim.
	 *
	 * 	The operation can also return all of the vehicle's options, including BedId, BedName, BodyStyleId, BodyStyleName, BrakesId, BrakesName, DriveTypeId, DriveTypeName, EngineId, EngineName, MakeId, and MakeName. (The full list of options follows.) All of these parameters can be used in subsequent requests with the other vehicle operations to narrow results.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MakeId - _integer_ (Optional; Sometimes Required) Identifier that uniquely identifies the make of the car. The make is the car's manufacturer, such as Ford or General Motors. Use with 'Year' to get model.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ModelId - _integer_ (Optional; Sometimes Required) Identifier that uniquely identifies the model of the car. Use with 'Year' and 'MakeId' to get trim.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas. Allows 'VehicleYears', 'VehicleMakes', 'VehicleModels', 'VehicleTrims', and 'VehicleOptions'. Defaults to 'VehicleYears'.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TrimId - _integer_ (Optional; Sometimes Required) Identifier that uniquely identifies the trim on the car. Required when when using the 'VehicleOptions' response group.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 * 	Year - _integer_ (Optional; Sometimes Required) The year of the car the part works in. Required only if including 'MakeId' in request or if you are using 'VehicleSearch' to look up a 'MakeId'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::pas/help_vehicle_search.php:
 	 * 	example::pas/vehicle_search.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/VehicleSearch.html
	 * 	Related - <vehicle_part_lookup()>, <vehicle_part_search()>
	 */
	public function vehicle_search($opt = null)
	{
		if (!$opt) $opt = array();

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('VehicleSearch', $opt, PAS_LOCALE_US);
	}


	/*%******************************************************************************************%*/
	// OTHER LOOKUP METHODS

	/**
	 * Method: similarity_lookup()
	 * 	Returns up to ten products per page that are similar to one or more items specified in the request. This operation is typically used to pique a customer's interest in buying something similar to what they've already ordered.
	 *
	 * 	If you specify more than one item, <similarity_lookup()> returns the intersection of similar items each item would return separately. Alternatively, you can use the SimilarityType parameter to return the union of items that are similar to any of the specified items. A maximum of ten similar items are returned; the operation does not return additional pages of similar items. If there are more than ten similar items, running the same request can result in different answers because the ten that are included in the response are picked randomly. The results are picked randomly only when you specify multiple items and the results include more than ten similar items.
	 *
	 * 	When you specify multiple items, it is possible for there to be no intersection of similar items. In this case, the operation returns an error.
	 *
	 * 	Similarity is a measurement of similar items purchased, that is, customers who bought X also bought Y and Z. It is not a measure, for example, of items viewed, that is, customers who viewed X also viewed Y and Z.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	item_id - _string_ (Required) Specifies the item you want to look up. An ItemId is an alphanumeric identifier assigned to an item. You can specify up to ten ItemIds separated by commas.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	Condition - _string_ (Optional) Specifies an item's condition. If Condition is set to "All", a separate set of responses is returned for each valid value of Condition. Allows 'All', 'Collectible', 'Refurbished', or 'Used'.
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) Specifies the merchant who is offering the item. MerchantId is an alphanumeric identifier assigned by Amazon to merchants. Make sure to use a Merchant ID and not a Seller ID. Seller IDs are not supported.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	SimilarityType - _string_ (Optional) "Intersection" returns the intersection of items that are similar to all of the ASINs specified. "Random" returns the union of items that are similar to all of the ASINs specified. Only ten items are returned. So, if there are more than ten similar items found, a random selection from the group is returned. For this reason, running the same request multiple times can yield different results.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_similarity_lookup.php:
	 * 	example::pas/similarity_lookup.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/SimilarityLookup.html
	 * 	Related - <tag_lookup()>, <transaction_lookup()>
	 */
	function similarity_lookup($item_id, $opt = null, $locale = null)
	{
		if (!$opt) $opt = array();
		$opt['ItemId'] = $item_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('SimilarityLookup', $opt, $locale);
	}

	/**
	 * Method: tag_lookup()
	 * 	Returns entities based on specifying one to five tags. A tag is a descriptive word that a customer uses to label entities on Amazon's retail web site. Entities can be items for sale, Listmania lists, guides, and so forth. For example, a customer might tag a given entity with the phrase, "BestCookbook". This operation is US-only.
	 *
	 * 	In the tag-related response groups, Tags and TagSummary specify the amount of information returned. The other tag-related response groups, TaggedGuides, TaggedItems, and Tagged listmaniaLists, specify the kind of entity tagged.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	tagname - _string_ (Required) Comma separated list of tag names. Up to five tags can be included in a request.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 * 	locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	Count - _integer_ (Optional) Number of tagged entities to return per tag. The default is 5; the maximum is 20.
	 * 	CustomerId - _string_ (Optional) Alphanumeric token that uniquely identifies a customer. This parameter limits the tags returned to those provided by a single customer.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	TagPage - _integer_ (Optional) Specifies the page of results to return. There are twenty results on a page.
	 * 	TagSort - _string_ (Optional) Specifies the sorting order for the results. Allows 'FirstUsed', '-FirstUsed', 'LastUsed', '-LastUsed', 'Name', '-Name', 'Usages', and '-Usages'. To sort items in descending order, prefix the previous values with a negative sign (-).
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_tag_lookup.php:
	 * 	example::pas/tag_lookup.phpt:
	 * 	example::pas/tag_lookup2.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/TagLookup.html
	 * 	Related - <similarity_lookup()>, <transaction_lookup()>
	 */
	function tag_lookup($tagname, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TagName'] = $tagname;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('TagLookup', $opt, PAS_LOCALE_US);
	}

	/**
	 * Method: transaction_lookup()
	 * 	Returns information about up to ten purchases that have already taken place. Transaction IDs are created whenever a purchase request is made by a customer. This operation is US-only.
	 *
	 * 	If you added your Associates ID to the config.inc.php file, or you passed it into the AmazonPAS() constructor, it will be passed along in this request automatically.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	transaction_id - _string_ (Required) A number that uniquely identifies a transaction. The retail web site calls this number the Order number.
	 * 	opt - _array_ (Optional) Associative array of parameters which can have the following keys:
	 *
	 * Keys for the $opt parameter:
	 * 	THIS IS AN INCOMPLETE LIST. For the latest information, check the AWS documentation page (noted below), or run the <help()> method (noted in the examples below).
	 *
	 * 	ContentType - _string_ (Optional) Specifies the format of the content in the response. Generally, ContentType should only be changed for REST requests when the Style parameter is set to an XSLT stylesheet. For example, to transform your Amazon Associates Web Service response into HTML, set ContentType to text/html. Allows 'text/xml' and 'text/html'. Defaults to 'text/xml'.
	 * 	MerchantId - _string_ (Optional) An alphanumeric token distributed by Amazon that uniquely identifies a merchant. Allows 'All', 'Amazon', 'FeaturedBuyBoxMerchant', or a specific Merchant ID. Defaults to 'Amazon'.
	 * 	ResponseGroup - _string_ (Optional) Specifies the types of values to return. You can specify multiple response groups in one request by separating them with commas.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 	Style - _string_ (Optional) Controls the format of the data returned in Amazon Associates Web Service responses. Set this parameter to "XML," the default, to generate a pure XML response. Set this parameter to the URL of an XSLT stylesheet to have Amazon Associates Web Service transform the XML response. See ContentType.
	 * 	Validate - _boolean_ (Optional) Prevents an operation from executing. Set the Validate parameter to True to test your request without actually executing it. When present, Validate must equal True; the default value is False. If a request is not actually executed (Validate=True), only a subset of the errors for a request may be returned because some errors (for example, no_exact_matches) are only generated during the execution of a request. Defaults to FALSE.
	 * 	XMLEscaping - _string_ (Optional) Specifies whether responses are XML-encoded in a single pass or a double pass. By default, XMLEscaping is Single, and Amazon Associates Web Service responses are encoded only once in XML. For example, if the response data includes an ampersand character (&), the character is returned in its regular XML encoding (&). If XMLEscaping is Double, the same ampersand character is XML-encoded twice (&amp;). The Double value for XMLEscaping is useful in some clients, such as PHP, that do not decode text within XML elements. Defaults to 'Single'.
	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
	 * Examples:
	 * 	example::pas/help_transaction_lookup.php:
	 * 	example::pas/transaction_lookup.phpt:
	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/TransactionLookup.html
	 * 	Related - <similarity_lookup()>, <tag_lookup()>
	 */
	function transaction_lookup($transaction_id, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TransactionId'] = $transaction_id;

		if (isset($this->assoc_id))
		{
			$opt['AssociateTag'] = $this->assoc_id;
		}

		return $this->pas_authenticate('TransactionLookup', $opt, PAS_LOCALE_US);
	}
}
