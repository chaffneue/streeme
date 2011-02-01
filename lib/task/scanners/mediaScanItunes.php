<?php
/**
 * mediaScanItunes
 * 
 * Itunes media ingest process
 * 
 * @package    streeme
 * @author     Richard Hoar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */ 
require_once( dirname(__FILE__) . '/../../vendor/CFPropertyList-1.1.1/CFPropertyList.php' );
   
$itunes_music_library   = sfConfig::get( 'app_itunes_xml_location' );
$mapped_drive_locations = sfConfig::get( 'app_mdl_mapped_drive_locations' );
$allowed_filetypes      = sfConfig::get( 'app_aft_allowed_file_types' );
$media_scanner         = new MediaScan();
$plist_parser          = new CFPropertyList( $itunes_music_library );
$plist                 = $plist_parser->toArray();

foreach( $plist[ 'Tracks' ] as $key => $value )
{
  //if it's not a valid filetype, ignore 
  if ( !in_array( substr( $value[ 'Location' ], -3 ), $allowed_filetypes ) ) continue;

  //update files on windows shares
  if ( is_array( $mapped_drive_locations ) && count( $mapped_drive_locations ) > 0 )
  {
    foreach ( $mapped_drive_locations as $k => $v ) 
    {      
      $value[ 'Location' ] = str_replace( $k, $v, $value[ 'Location' ] );
    }
  }
  //if this file's scanned already and nothing about the file has been modified, ignore
  if ( $media_scanner->is_scanned( StreemeUtil::itunes_format_encode( StreemeUtil::itunes_format_decode( $value[ 'Location' ] ) ), $value[ 'Date Modified' ] ) ) continue;

  //smooth times from itunes format to minutes:seconds
  $minutes = floor( $value[ 'Total Time' ] / 1000 / 60 );
  $seconds = str_replace ( '.', '0', substr( ( ( ( $value[ 'Total Time' ] ) - (floor( $value[ 'Total Time' ] / 1000 / 60 ) ) * 60 * 1000 ) / 1000 ), 0, 2 ) );       
  if ( $seconds > 60 ) $seconds = '00';

  //create an array of song information
  $song_array = array();
  $song_array[ 'artist_name' ]      = @$value[ 'Artist' ];
  $song_array[ 'album_name' ]       = @$value[ 'Album' ];
  $song_array[ 'song_name' ]        = @$value[ 'Name' ];
  $song_array[ 'song_length' ]      = @$minutes . ':' . $seconds;
  $song_array[ 'accurate_length' ]  = @$value[ 'Total Time' ];
  $song_array[ 'genre_name' ]       = @$value[ 'Genre' ];
  $song_array[ 'filesize' ]         = @$value[ 'Size' ];
  $song_array[ 'bitrate' ]          = @$value[ 'Bit Rate' ];
  $song_array[ 'yearpublished' ]    = @$value[ 'Year' ];
  $song_array[ 'tracknumber']       = @$value[ 'Track Number' ];  
  $song_array[ 'label' ]            = @null; //not available from itunes xml
  $song_array[ 'mtime' ]            = @$value[ 'Date Modified' ];
  $song_array[ 'atime' ]            = @$value[ 'Date Added' ];
  $song_array[ 'filename' ]         = StreemeUtil::itunes_format_encode( StreemeUtil::itunes_format_decode( $value[ 'Location' ] ) ); //normalize formatting  

  if( is_readable( urldecode( $song_array[ 'filename' ] ) ) )
  { 
     //it checks out, add the song
     $media_scanner->add_song( $song_array );
  }
}

//finalize the scan 
$media_scanner->finalize_scan();

//summarize the results of the scan
echo "\r\n";
echo $media_scanner->get_summary();
?>