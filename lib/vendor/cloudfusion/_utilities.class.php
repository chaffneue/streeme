<?php
/**
 * File: CFUtilities
 * 	Utilities for connecting to, and working with, AWS.
 *
 * Version:
 * 	2009.08.24
 *
 * Copyright:
 * 	2006-2009 Foleeo, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	CloudFusion - http://getcloudfusion.com
 */


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: CFUtilities
 * 	Container for all utility-related methods.
 */
class CFUtilities
{
	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	<CFUtilities> object
	 */
	public function __construct()
	{
		return $this;
	}

	/**
	 * Method: hex_to_base64()
	 * 	Convert a HEX value to Base64.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	str - _string_ (Required) Value to convert.
	 *
	 * Returns:
	 * 	_string_ Base64-encoded string.
 	 *
 	 * Examples:
 	 * 	example::utilities/hex_to_base64.phpt:
	 */
	public function hex_to_base64($str)
	{
		$raw = '';

		for ($i = 0; $i < strlen($str); $i += 2)
		{
			$raw .= chr(hexdec(substr($str, $i, 2)));
		}

		return base64_encode($raw);
	}

	/**
	 * Method: to_query_string()
	 * 	Convert an associative array into a query string.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	array - _array_ (Required) Array to convert.
	 *
	 * Returns:
	 * 	_string_ URL-friendly query string.
 	 *
 	 * Examples:
 	 * 	example::utilities/to_query_string.phpt:
	 */
	public function to_query_string($array)
	{
		return http_build_query( $array, '', '&' );
	}

	/**
	 * Method: to_signable_string()
	 * 	Convert an associative array into a sign-able string.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	array - _array_ (Required) Array to convert.
	 *
	 * Returns:
	 * 	_string_ URL-friendly sign-able string.
 	 *
 	 * Examples:
 	 * 	example::utilities/to_signable_string.phpt:
	 */
	public function to_signable_string($array)
	{
		$t = array();

		foreach ($array as $k => $v)
		{
			$t[] = $this->encode_signature2($k) . '=' . $this->encode_signature2($v);
		}

		return implode('&', $t);
	}

	/**
	 * Method: encode_signature2()
	 * 	Encode the value according to RFC 3986.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	string - _string_ (Required) String to convert
	 *
	 * Returns:
	 * 	_string_ URL-friendly sign-able string.
	 */
	public function encode_signature2($string)
	{
		$string = rawurlencode($string);
		return str_replace('%7E', '~', $string);
	}

	/**
	 * Method: query_to_array()
	 * 	Convert a query string into an associative array. Multiple, identical keys will become an indexed array.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	qs - _string_ (Required) Query string to convert.
	 *
	 * Returns:
	 * 	_array_ Associative array of keys and values.
	 *
 	 * Examples:
 	 * 	example::utilities/query_to_array.phpt:
 	 * 	example::utilities/query_to_array2.phpt:
	 */
	public function query_to_array($qs)
	{
		$query = explode('&', $qs);
		$data = array();

		foreach ($query as $q)
		{
			$q = explode('=', $q);

			if (isset($data[$q[0]]) && is_array($data[$q[0]]))
			{
				$data[$q[0]][] = urldecode($q[1]);
			}
			else if (isset($data[$q[0]]) && !is_array($data[$q[0]]))
			{
				$data[$q[0]] = array($data[$q[0]]);
				$data[$q[0]][] = urldecode($q[1]);
			}
			else
			{
				$data[urldecode($q[0])] = urldecode($q[1]);
			}
		}
		return $data;
	}

	/**
	 * Method: size_readable()
	 * 	Return human readable file sizes. Original function by Aidan Lister <mailto:aidan@php.net>, modified by Ryan Parman.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	size - _integer_ (Required) Filesize in bytes.
	 * 	unit - _string_ (Optional) The maximum unit to use. Defaults to the largest appropriate unit.
	 * 	retstring - _string_ (Optional) The format for the return string. Defaults to '%01.2f %s'
	 *
	 * Returns:
	 * 	_string_ The human-readable file size.
	 *
 	 * Examples:
 	 * 	example::utilities/size_readable.phpt:
 	 * 	example::utilities/size_readable2.phpt:
 	 * 	example::utilities/size_readable3.phpt:
 	 *
	 * See Also:
	 * 	Original Function - http://aidanlister.com/repos/v/function.size_readable.php
	 */
	public function size_readable($size, $unit = null, $retstring = null)
	{
		// Units
		$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
		$mod = 1024;
		$ii = count($sizes) - 1;

		// Max unit
		$unit = array_search((string) $unit, $sizes);
		if ($unit === null || $unit === false)
		{
			$unit = $ii;
		}

		// Return string
		if ($retstring === null)
		{
			$retstring = '%01.2f %s';
		}

		// Loop
		$i = 0;
		while ($unit != $i && $size >= 1024 && $i < $ii)
		{
			$size /= $mod;
			$i++;
		}

		return sprintf($retstring, $size, $sizes[$i]);
	}

	/**
	 * Method: time_hms()
	 * 	Convert a number of seconds into Hours:Minutes:Seconds.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	seconds - _integer_ (Required) The number of seconds to convert.
	 *
	 * Returns:
	 * 	_string_ The formatted time.
	 *
 	 * Examples:
 	 * 	example::utilities/time_hms.phpt:
	 */
	public function time_hms($seconds)
	{
		$time = '';

		// First pass
		$hours = (int) ($seconds / 3600);
		$seconds = $seconds % 3600;
		$minutes = (int) ($seconds / 60);
		$seconds = $seconds % 60;

		// Cleanup
		$time .= ($hours) ? $hours . ':' : '';
		$time .= ($minutes < 10 && $hours > 0) ? '0' . $minutes : $minutes;
		$time .= ':';
		$time .= ($seconds < 10) ? '0' . $seconds : $seconds;

		return $time;
	}

	/**
	 * Method: try_these()
	 * 	Returns the first value that is set. Based on Try.these() from Prototype <http://prototypejs.org>.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	attrs - _array_ (Required) The attributes to test, as strings. Intended for testing properties of the $base object, but also works with variables if you place an @ symbol at the beginning of the command.
	 * 	base - _object_ (Optional) The base object to use, if any.
	 * 	default - _mixed_ (Optional) What to return if there are no matches. Defaults to null.
	 *
	 * Returns:
	 * 	_mixed_ Either a matching property of a given object, _boolean_ false, or any other data type you might choose.
	 *
 	 * Examples:
 	 * 	example::utilities/try_these.phpt:
 	 * 	example::utilities/try_these2.phpt:
 	 * 	example::utilities/try_these3.phpt:
 	 * 	example::utilities/try_these4.phpt:
 	 * 	example::utilities/try_these5.phpt:
	 */
	public function try_these($attrs, $base = null, $default = null)
	{
		if ($base)
		{
			foreach ($attrs as $attr)
			{
				if (isset($base->$attr))
				{
					return $base->$attr;
				}
			}
		}
		else
		{
			foreach ($attrs as $attr)
			{
				if (isset($attr))
				{
					return $attr;
				}
			}
		}

		return $default;
	}

	/**
	 * Method: json_encode()
	 * 	Replicates json_encode() for versions of PHP 5 earlier than 5.2.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	obj - _mixed_ (Required) The PHP object to convert into a JSON string.
	 *
	 * Returns:
	 * 	_string_ A JSON string.
	 *
 	 * Examples:
 	 * 	example::utilities/json_encode2.phpt:
 	 * 	example::utilities/json_encode3.phpt:
 	 * 	example::utilities/json_encode4.phpt:
 	 * 	example::utilities/json_encode5.phpt:
 	 * 	example::utilities/json_encode6.phpt:
	 */
	public function json_encode($obj)
	{
		if (function_exists('json_encode'))
		{
			return json_encode($obj);
		}

		return $this->json_encode_php51($obj);
	}

	/**
	 * Method: json_encode_php51()
	 * 	Called by CFUtilities::json_encode() if PHP 5.2's json_encode() is unavailable. DO NOT CALL THIS METHOD DIRECTLY! Use $obj->util->json_encode() instead.
	 *
	 * Author:
	 * 	http://us2.php.net/manual/en/function.json-encode.php#82904
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	obj - _mixed_ (Required) The PHP object to convert into a JSON string.
	 *
	 * Returns:
	 * 	_string_ A JSON string.
	 */
	public function json_encode_php51($obj)
	{
		if (is_null($obj)) return 'null';
		if ($obj === false) return 'false';
		if ($obj === true) return 'true';

		if (is_scalar($obj))
		{
			if (is_float($obj))
			{
				// Always use '.' for floats.
				return str_replace(',', '.', strval($obj));
			}
			elseif (is_int($obj))
			{
				return strval($obj);
			}
			elseif (is_string($obj))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $obj) . '"';
			}
			return $obj;
		}

		$isList = true;
		for ($i = 0, reset($obj); $i < count($obj); $i++, next($obj))
		{
			if (key($obj) !== $i)
			{
				$isList = false;
				break;
			}
		}

		$result = array();

		if ($isList)
		{
			foreach ($obj as $v)
			{
				$result[] = json_encode($v);
			}

			return '[' . join(',', $result) . ']';
		}
		else
		{
			foreach ($obj as $k => $v)
			{
				$result[] = json_encode($k).':'.json_encode($v);
			}

			return '{' . join(',', $result) . '}';
		}
	}

	/**
	 * Method: convert_response_to_array()
	 * 	Converts a SimpleXML response to an array structure.
	 *
	 * Author:
	 * 	Adrien Cahen <http://gaarf.info/2009/08/13/xml-string-to-php-array/>
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	obj - _ResponseCore_ (Required) A CloudFusion ResponseCore response value.
	 *
	 * Returns:
	 * 	_array_ The response value as a standard, multi-dimensional array.
	 *
 	 * Examples:
 	 * 	example::utilities/convert_response_to_array.phpt:
	 *
	 * Requirements:
	 * 	PHP 5.2 or newer.
	 */
	public function convert_response_to_array(ResponseCore $response)
	{
		return json_decode(json_encode((array) $response), true);
	}

	/**
	 * Method: convert_date_to_iso8601()
	 * 	Checks to see if a date stamp is ISO-8601 formatted, and if not, makes it so.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	datestamp - _string_ (Required) A date stamp, or a string that can be parsed into a date stamp.
	 *
	 * Returns:
	 * 	_string_ An ISO-8601 formatted date stamp.
	 *
 	 * Examples:
 	 * 	example::utilities/convert_date_to_iso8601.phpt:
	 */
	public function convert_date_to_iso8601($datestamp)
	{
		if (!preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}((\+|-)\d{2}:\d{2}|Z)/m', $datestamp))
		{
			return gmdate(DATE_FORMAT_ISO8601, strtotime($datestamp));
		}

		return $datestamp;
	}
}
