<?php
/**
 * This is a mock to simulate the functionality of amazon's current API
 * under the cloudfusion connector
 *
 * @author Richard Hoar
 * @package Streeme
 */
class AmazonPAS
{
  public $missing_response = <<<HEREDOC
<?xml version="1.0" ?><ItemSearchResponse xmlns="http://webservices.amazon.com/AWSECommerceService/2009-07-01"><OperationRequest><HTTPHeaders><Header Name="UserAgent" Value="CloudFusion/2.5 (Cloud Computing Toolkit; http://getcloudfusion.com) Build/20101209043420"></Header></HTTPHeaders><RequestId>4d11e99c-a1b8-4f4d-b9ee-60804273d58f</RequestId><Arguments><Argument Name="Operation" Value="ItemSearch"></Argument><Argument Name="Service" Value="AWSECommerceService"></Argument><Argument Name="AssociateTag" Value="streeme-20"></Argument><Argument Name="Version" Value="2009-07-01"></Argument><Argument Name="Keywords" Value="Me› su› í eyrum vi› spilum end"></Argument><Argument Name="SignatureMethod" Value="HmacSHA256"></Argument><Argument Name="SearchIndex" Value="Music"></Argument><Argument Name="SignatureVersion" Value="2"></Argument><Argument Name="Signature" Value="w9eQuzgcCCf5SpVBvULtACqvH3DndpkJNRPUOMNkU6c="></Argument><Argument Name="Artist" Value="Sigur Rós"></Argument><Argument Name="Creator" Value="Sigur Rós"></Argument><Argument Name="AWSAccessKeyId" Value="0YCVF2T639NH9YPW5ZR2"></Argument><Argument Name="Timestamp" Value="2011-07-22T04:10:37Z"></Argument><Argument Name="ResponseGroup" Value="Medium"></Argument></Arguments><RequestProcessingTime>0.0455450000000000</RequestProcessingTime></OperationRequest><Items><Request><IsValid>True</IsValid><ItemSearchRequest><Artist>Sigur Rós</Artist><Condition>New</Condition><DeliveryMethod>Ship</DeliveryMethod><Keywords>Me› su› í eyrum vi› spilum end</Keywords><MerchantId>Amazon</MerchantId><ResponseGroup>Medium</ResponseGroup><ReviewSort>-SubmissionDate</ReviewSort><SearchIndex>Music</SearchIndex></ItemSearchRequest><Errors><Error><Code>AWS.ECommerceService.NoExactMatches</Code><Message>We did not find any matches for your request.</Message></Error></Errors></Request><TotalResults>0</TotalResults><TotalPages>0</TotalPages></Items></ItemSearchResponse>
HEREDOC;

  public $valid_response = <<<HEREDOC
<?xml version="1.0" ?><ItemSearchResponse xmlns="http://webservices.amazon.com/AWSECommerceService/2009-07-01"><OperationRequest><HTTPHeaders><Header Name="UserAgent" Value="CloudFusion/2.5 (Cloud Computing Toolkit; http://getcloudfusion.com) Build/20101209043420"></Header></HTTPHeaders><RequestId>9233c2a6-e5ca-4b6d-aeb0-0797b7dadf24</RequestId><Arguments><Argument Name="Operation" Value="ItemSearch"></Argument><Argument Name="Service" Value="AWSECommerceService"></Argument><Argument Name="Signature" Value="7Dnp8SN3uN/95vrDL29i+2n5KPUNId9Xqmtxc4fo1OQ="></Argument><Argument Name="AssociateTag" Value="streeme-20"></Argument><Argument Name="Version" Value="2009-07-01"></Argument><Argument Name="Artist" Value="The Album Leaf"></Argument><Argument Name="Keywords" Value="The Enchanted Hill"></Argument><Argument Name="Creator" Value="The Album Leaf"></Argument><Argument Name="SignatureMethod" Value="HmacSHA256"></Argument><Argument Name="AWSAccessKeyId" Value="0YCVF2T639NH9YPW5ZR2"></Argument><Argument Name="Timestamp" Value="2011-07-22T04:12:22Z"></Argument><Argument Name="ResponseGroup" Value="Medium"></Argument><Argument Name="SearchIndex" Value="Music"></Argument><Argument Name="SignatureVersion" Value="2"></Argument></Arguments><RequestProcessingTime>0.1378700000000000</RequestProcessingTime></OperationRequest><Items><Request><IsValid>True</IsValid><ItemSearchRequest><Artist>The Album Leaf</Artist><Condition>New</Condition><DeliveryMethod>Ship</DeliveryMethod><Keywords>The Enchanted Hill</Keywords><MerchantId>Amazon</MerchantId><ResponseGroup>Medium</ResponseGroup><ReviewSort>-SubmissionDate</ReviewSort><SearchIndex>Music</SearchIndex></ItemSearchRequest></Request><TotalResults>1</TotalResults><TotalPages>1</TotalPages><Item><ASIN>B000W7Y1AQ</ASIN><DetailPageURL>http://www.amazon.com/Green-Ep-Album-Leaf/dp/B000W7Y1AQ%3FSubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB000W7Y1AQ</DetailPageURL><ItemLinks><ItemLink><Description>Technical Details</Description><URL>http://www.amazon.com/Green-Ep-Album-Leaf/dp/tech-data/B000W7Y1AQ%3FSubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>Add To Baby Registry</Description><URL>http://www.amazon.com/gp/registry/baby/add-item.html%3Fasin.0%3DB000W7Y1AQ%26SubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>Add To Wedding Registry</Description><URL>http://www.amazon.com/gp/registry/wedding/add-item.html%3Fasin.0%3DB000W7Y1AQ%26SubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>Add To Wishlist</Description><URL>http://www.amazon.com/gp/registry/wishlist/add-item.html%3Fasin.0%3DB000W7Y1AQ%26SubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>Tell A Friend</Description><URL>http://www.amazon.com/gp/pdp/taf/B000W7Y1AQ%3FSubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>All Customer Reviews</Description><URL>http://www.amazon.com/review/product/B000W7Y1AQ%3FSubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink><ItemLink><Description>All Offers</Description><URL>http://www.amazon.com/gp/offer-listing/B000W7Y1AQ%3FSubscriptionId%3D0YCVF2T639NH9YPW5ZR2%26tag%3Dstreeme-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3DB000W7Y1AQ</URL></ItemLink></ItemLinks><SalesRank>464620</SalesRank><SmallImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL75_.jpg</URL><Height Units="pixels">75</Height><Width Units="pixels">75</Width></SmallImage><MediumImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL160_.jpg</URL><Height Units="pixels">160</Height><Width Units="pixels">160</Width></MediumImage><LargeImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL.jpg</URL><Height Units="pixels">499</Height><Width Units="pixels">500</Width></LargeImage><ImageSets><ImageSet Category="primary"><SwatchImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL30_.jpg</URL><Height Units="pixels">30</Height><Width Units="pixels">30</Width></SwatchImage><SmallImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL75_.jpg</URL><Height Units="pixels">75</Height><Width Units="pixels">75</Width></SmallImage><ThumbnailImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL75_.jpg</URL><Height Units="pixels">75</Height><Width Units="pixels">75</Width></ThumbnailImage><TinyImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL110_.jpg</URL><Height Units="pixels">110</Height><Width Units="pixels">110</Width></TinyImage><MediumImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL._SL160_.jpg</URL><Height Units="pixels">160</Height><Width Units="pixels">160</Width></MediumImage><LargeImage><URL>http://ecx.images-amazon.com/images/I/41rwJM9pcaL.jpg</URL><Height Units="pixels">499</Height><Width Units="pixels">500</Width></LargeImage></ImageSet></ImageSets><ItemAttributes><Binding>Audio CD</Binding><Creator Role="Performer">Album Leaf</Creator><EAN>9328082330084</EAN><Format>Import</Format><ListPrice><Amount>1999</Amount><CurrencyCode>USD</CurrencyCode><FormattedPrice>$19.99</FormattedPrice></ListPrice><NumberOfDiscs>1</NumberOfDiscs><PackageDimensions><Height Units="hundredths-inches">10</Height><Length Units="hundredths-inches">480</Length><Weight Units="hundredths-pounds">5</Weight><Width Units="hundredths-inches">460</Width></PackageDimensions><ProductGroup>Music</ProductGroup><ProductTypeName>ABIS_MUSIC</ProductTypeName><ReleaseDate>2007-10-02</ReleaseDate><Title>Green Ep</Title></ItemAttributes><OfferSummary><TotalNew>0</TotalNew><TotalUsed>0</TotalUsed><TotalCollectible>0</TotalCollectible><TotalRefurbished>0</TotalRefurbished></OfferSummary></Item></Items></ItemSearchResponse>
HEREDOC;

  /**
   * Method: item_search()
   *  The <item_search()> operation returns items that satisfy the search criteria, including one or more search indices. <item_search()> is the operation that is used most often in requests. In general, when trying to find an item for sale, you use this operation.

   *  @param keywords - _string_ (Required) A word or phrase associated with an item. The word or phrase can be in various product fields, including product title, author, artist, description, manufacturer, and so forth. When, for example, the search index equals "MusicTracks", the Keywords parameter enables you to search by song title.
   *  @param opt - _array_ (Optional) Associative array of parameters which can have the following keys:
   *  @param locale - _string_ (Optional) Which Amazon-supported locale do we use? Defaults to United States.
   */
  public function item_search($keywords, $opt = null, $locale = null)
  {
    return new ResponseCore(array(), new SimpleXMLElement($this->valid_response), 200);
  }
}

/**
 * Class: ResponseCore
 *  Container for all response-related methods.
 *
 *  Part of the Cloudfusion Package
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
   *  The constructor
   *
   * Access:
   *  public
   *
   * Parameters:
   *  header - _array_ (Required) Associative array of HTTP headers (typically returned by <RequestCore::getResponseHeader()>).
   *  body - _string_ (Required) XML-formatted response from AWS.
   *  status - _integer_ (Optional) HTTP response status code from the request.
   *
   * Returns:
   *  _object_ Contains an _array_ 'header' property (HTTP headers as an associative array), a _SimpleXMLElement_ or _string_ 'body' property, and an _integer_ 'status' code.
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
   *  Did we receive the status code we expected?
   *
   * Access:
   *  public
   *
   * Parameters:
   *  codes - _integer|array_ (Optional) The status code(s) to expect. Pass an _integer_ for a single acceptable value, or an _array_ of integers for multiple acceptable values. Defaults to _array_ 200|204.
   *
   * Returns:
   *  _boolean_ Whether we received the expected status code or not.
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
