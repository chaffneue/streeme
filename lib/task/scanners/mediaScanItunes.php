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
$itunes_music_library   = sfConfig::get( 'app_itunes_xml_location' );
$mapped_drive_locations = sfConfig::get( 'app_mdl_mapped_drive_locations' );
$allowed_filetypes      = array_map( 'strtolower', sfConfig::get( 'app_aft_allowed_file_types' ) );
$media_scanner          = new MediaScan();
$itunes_parser          = new StreemeItunesTrackParser( $itunes_music_library );

while( $value = $itunes_parser->getTrack() )
{
  //if it's not a valid filetype, ignore 
  if ( !in_array( strtolower( substr( $value[ 'Location' ], -3 ) ), $allowed_filetypes ) ) continue;

  //decode the itunes file scheme for checking is_readable
  $location = StreemeUtil::itunes_format_decode( $value[ 'Location' ], StreemeUtil::is_windows(), $mapped_drive_locations);
  
  //convert it from user's filesystem value to UTF-8 for the database
  $value[ 'Location' ] = iconv( sfConfig::get( app_filesystem_encoding, 'ISO-8859-1' ), 'UTF-8//TRANSLIT', $location );
  
  //if this file's scanned already and nothing about the file has been modified, ignore
  if ( $media_scanner->is_scanned( $value[ 'Location' ], strtotime( $value[ 'Date Modified' ] ) ) ) continue;

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
  $song_array[ 'mtime' ]            = @strtotime( $value[ 'Date Modified' ] );
  $song_array[ 'atime' ]            = @strtotime( $value[ 'Date Added' ] );
  $song_array[ 'filename' ]         = @$value[ 'Location' ];

  if( is_readable( $location ) )
  { 
     //it checks out, add the song
     $media_scanner->add_song( $song_array );
  }
  else
  {
    echo sprintf( 'File %s is unreadable',  $value[ 'Location' ] ) . "\r\n";
  }
}

//finalize the scan 
$media_scanner->finalize_scan();

//summarize the results of the scan
echo "\r\n";
echo $media_scanner->get_summary();
?>