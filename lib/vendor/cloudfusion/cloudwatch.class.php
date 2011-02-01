<?php
/**
 * File: CloudWatch
 * 	Amazon CloudWatch Monitoring Service (http://aws.amazon.com/cloudwatch)
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
 * 	Amazon CloudWatch - http://aws.amazon.com/cloudwatch
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: CW_DEFAULT_URL
 * 	Specify the default queue URL.
 */
define('CW_DEFAULT_URL', 'monitoring.amazonaws.com');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: CloudWatch_Exception
 * 	Default CloudWatch Exception.
 */
class CloudWatch_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonCloudWatch
 * 	Container for all Amazon CloudWatch-related methods. Inherits additional methods from CloudFusion.
 *
 * Extends:
 * 	CloudFusion
 */
class AmazonCloudWatch extends CloudFusion
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
		$this->api_version = '2009-05-15';
		$this->hostname = CW_DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			throw new CloudWatch_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new CloudWatch_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// METHODS

	/**
	 * Method: list_metrics()
	 * 	Returns a list of up to 500 valid metrics for which there is recorded data available to a you and a NextToken string that can be used to query for the next set of results.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
 	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
 	 *
 	 * Keys for the $opt parameter:
 	 * 	NextToken - _string_ (Optional) Allows you to retrieve the next set of results for your ListMetrics query.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
 	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::cloudwatch/list_metrics.phpt:
 	 * 	example::cloudwatch/list_metrics2.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?API-ListMetrics.html
	 */
	public function list_metrics($opt = null)
	{
		if (!$opt) $opt = array();
		return $this->authenticate('ListMetrics', $opt, $this->hostname);
	}

	/**
	 * Method: get_metric_statistics()
	 * 	Returns data for one or more statistics of given a metric.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
 	 * 	measure_name - _string_ (Required) The measure name that corresponds to the measure for the gathered metric. See http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?arch-AmazonCloudWatch-metricscollected.html for a list of available measurements.
 	 * 	statistics - _string_|_array_ (Required) The statistics to be returned for the given metric. You can pass a string for a single statistic, or an indexed array for multiple statistics. See http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?arch-Amazon-CloudWatch-statistics.html for a list of available statistics.
 	 * 	unit - _string_ (Required) The standard unit of measurement for a given Measure. See http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?DT_StandardUnit.htmlfor a list of available units.
 	 * 	start_time - _string_ (Required) A time stamp representing the beginning of the period to get results for. Looks for an ISO-8601 formatted time stamp, but can convert any understandable time stamp into the correct format automatically.
 	 * 	end_time - _string_ (Required) A time stamp representing the end of the period to get results for. Looks for an ISO-8601 formatted time stamp, but can convert any understandable time stamp into the correct format automatically.
 	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
 	 *
 	 * Keys for the $opt parameter:
 	 * 	CustomUnit - _array_ (Optional) The user-defined CustomUnit applied to a Measure.
 	 * 	Dimensions - _array_ (Optional) Allows you to specify one Dimension to further filter metric data on. If you don't specify a dimension, the service returns the aggregate of all the measures with the given measure name and time range. See http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?arch-Amazon-CloudWatch-dimensions.html for a list of available dimensions.
 	 * 	Namespace - _array_ (Optional) The namespace corresponding to the service of interest. For example, "AWS/EC2" represents Amazon EC2.
 	 * 	Period - _integer_ (Required) The granularity (in seconds) of the returned datapoints. Must be a multiple of 60. Defaults to 60.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
 	 *
	 * Returns:
	 * 	<ResponseCore> object
	 *
 	 * Examples:
 	 * 	example::cloudwatch/get_metric_statistics.phpt:
 	 * 	example::cloudwatch/get_metric_statistics2.phpt:
 	 * 	example::cloudwatch/get_metric_statistics3.phpt:
 	 *
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AmazonCloudWatch/latest/DeveloperGuide/index.html?API_GetMetricStatistics.html
	 */
	public function get_metric_statistics($measure_name, $statistics, $unit, $start_time, $end_time, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['MeasureName'] = $measure_name;
		$opt['Unit'] = $unit;
		$opt['StartTime'] = $this->util->convert_date_to_iso8601($start_time);
		$opt['EndTime'] = $this->util->convert_date_to_iso8601($end_time);

		if (is_array($statistics))
		{
			for ($i = 0, $max = count($statistics); $i < $max; $i++)
			{
				$opt['Statistics.member.' . ($i + 1)] = $statistics[$i];
			}
		}
		else
		{
			$opt['Statistics.member.1'] = $statistics;
		}

		if (!isset($opt['Period']))
		{
			$opt['Period'] = 60;
		}

		return $this->authenticate('GetMetricStatistics', $opt, $this->hostname);
	}
}
