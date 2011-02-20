<?php
/**
 * artworkScanAmazon
 *
 * Read album art from Amazon PAS service
 * Bes sure to review and complete the cloudfusion configuration in config/
 *
 * @package    streeme
 * @author     Richard Hoar
 */
error_reporting( 0 );
require_once( dirname( __FILE__ ) . '/../../../config/cloudfusion.php' );
require_once( dirname( __FILE__ ) . '/../../vendor/cloudfusion/cloudfusion.class.php' );

$artwork_scanner        = new ArtworkScan( 'amazon' );
$associate_services     = new AmazonPAS();

$artwork_list           = $artwork_scanner->get_unscanned_artwork_list();
$current_album_id       = 0;

if ( count( $artwork_list ) == 0 )
{
  echo( "*** Songs have all been cross-checked with Amazon ***" );
}

foreach( $artwork_list as $key => $value )
{
  //this album has copied art, skip to the next album
  if ( $current_album_id == $value[ 'album_id' ] ) continue;

  $art_dir = dirname( __FILE__ ) . '/../../../data/album_art/' . md5( $value[ 'artist_name' ] . $value[ 'album_name' ] );
  
  echo 'Scanning: ' .  $value[ 'album_name' ] . ' by: ' .  $value[ 'artist_name' ] . "\r\n";
  
  $opts = array(
              'Artist' => $value['artist_name'],
              'Creator' => $value['artist_name'],
              'SearchIndex' => 'Music',
              'ResponseGroup' => 'Medium'
           );
           
  //Sometimes Amazon returns malformed xml, so we need to catch exceptions. We'll try again on a later scan.
  try
  {
    $result = $associate_services->item_search( $value['album_name'], $opts );
  }
  catch( Exception $e )
  {
    echo sprintf( 'Error: Amazon returned invalid response for %s by: %s', $value['album_name'], $value[ 'artist_name'] ) . "\r\n";
    continue;
  }
  
  //error codes - just show the error and skip
  if ( isset( $result->body->Error->Code ) || !empty( $result->body->Error->Code ) )
  {
    //don't scan further files in this album
    $current_album_id = $value[ 'album_id' ];
    echo "Error: " . (string) $result->body->Error->Message . "\r\n";
    continue;
  }

  //Mark failed search requests as skipped - product likely doesn't exist in amazon catalog
  if ( @(string) $result->body->Items->Request->Errors->Error->Code == "AWS.ECommerceService.NoExactMatches" )
  {
    //don't scan further files in this album
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
    continue;
  }


  //loop through the amazon imagesets until the array has an entry for every image
  //prefer the first results in each imageset as they tend to be front box art
  $imageurls = array();
  if( !isset( $result->body->Items->Item->ImageSets->ImageSet ) )
  {
    //there's no art to use
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
    continue;
  }
  
  foreach ( $result->body->Items->Item->ImageSets->ImageSet as $v )
  {
    if( !isset( $imageurls['small'] ) )
    {
      $imageurls['small']  = (string) $v->SmallImage->URL;
    }
    if( !isset( $imageurls['medium'] ) )
    {
      $imageurls['medium'] = (string) $v->MediumImage->URL;
    }
    if( !isset( $imageurls['large'] ) )
    {
      $imageurls['large']  = (string) $v->LargeImage->URL;
    }
  }

  //Load the selected image url contents into a buffer
  $images = array();
  $images['small']  = @file_get_contents( $imageurls['small'] );
  $images['medium'] = @file_get_contents( $imageurls['medium'] );
  $images['large']  = @file_get_contents( $imageurls['large'] );

  //create a specially encoded directory: an md5 hash of the artist and album
  if ( !file_exists( $art_dir ) )
  {
     if ( !mkdir( $art_dir, 0777, true ) )
     {
       die('The album art directory: ' . $art_dir . ' Could not be created. This could be because one or more of the the folders is not writable' );
     }
  }

  //write all the sizes to the directory
  foreach( $images as $k => $v )
  {
     file_put_contents( $art_dir . '/' . $k . '.jpg', $v);
  }
  
  //don't scan further files in this album
  $current_album_id = $value[ 'album_id' ];
 
  $artwork_scanner->flag_as_added( $value[ 'album_id' ] );
}

//summarize the results of the scan
echo "\r\n";
echo $artwork_scanner->get_summary();

?>