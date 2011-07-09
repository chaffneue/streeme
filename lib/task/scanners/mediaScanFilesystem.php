<?php

/**
 * mediaScanFilesystem
 *
 * Filesystem media ingest process
 *
 * package    streeme
 * author     Richard Hoar
 */
mb_internal_encoding("UTF-8");

require_once( dirname(__FILE__) . '/../../vendor/getid3-1.9.0/getid3/getid3.php' );
   
$watched_folers         = sfConfig::get( 'app_wf_watched_folders' );
$mapped_drive_locations = sfConfig::get( 'app_mdl_mapped_drive_locations' );
$allowed_filetypes      = array_map( 'strtolower', sfConfig::get( 'app_aft_allowed_file_types' ) );
$media_scanner          = new MediaScan();
$id3_scanner            = new getID3();
$id3_scanner->encoding  = 'UTF-8';

foreach ( $watched_folders as $key => $path )
{
  scan_directory( $path, $allowed_filetypes, $media_scanner, $id3_scanner );
}

//finalize the scan
$media_scanner->finalize_scan();

//summarize the results of the scan
echo "\r\n";
echo $media_scanner->get_summary();

/**
* Recursive directory scanner
*
* param path - str: the path to scan with no trailing slash
* param allowed_filetypes  arr: mp3, ogg etc
* param media_scanner str: the media scanner object
*/
function scan_directory( $path, $allowed_filetypes, $media_scanner, $id3_scanner )
{
  $dp = opendir( $path );

  while($filename = readdir($dp))
  {
    //create the full pathname and streeme pathname
    $full_file_path = $path . '/' . $filename;
    
    //skip hidden files/folders
    if ($filename{0} === '.')
    {
      continue;
    }
    
    //it's a directory, recurse from this level
    if( is_dir( $full_file_path ) )
    {
      scan_directory( $full_file_path, $allowed_filetypes, $media_scanner, $id3_scanner );
      continue;
    }

    //Stat the file
    $file_stat = stat( $full_file_path );

    //is it a usable file?
    if ( $file_stat['size'] === 0 || !in_array( strtolower( substr( $filename, -3 ) ), $allowed_filetypes ) ) continue;
      
    $streeme_path_name = iconv( sfConfig::get( app_filesystem_encoding, 'ISO-8859-1' ), 'UTF-8//TRANSLIT', $full_file_path );
    
    //has it been scanned before?
    if ( $media_scanner->is_scanned(  $streeme_path_name, $file_stat[ 'mtime' ] ) ) continue;

    echo "Scanning " . $filename . "\n";

    //get the file information from pathinfo in case we need a substitute song name
    $pinfo = pathinfo( $full_file_path );
    
    /**
    * Pure ugliness - there's 3 possible containers for scraps of ID3 data - they're in order of preference and data integrity
    * Tempted to move this to its own container
    * high    high    medium    none
    * apex -> id3v2 -> id3v1 -> null
    * getID3 is a bit of a tricky lib to work with, but it has great features
    */
    $value = $id3_scanner->analyze( $full_file_path );
    
    $tags = $value[ 'tags' ];
    
    //track number is a nuisance - regress to find the tags
    if( isset( $tags[ 'id3v1' ][ 'track' ][0] ) && is_int( $tags[ 'id3v1' ][ 'track' ][0] ) )
    {
      //could be an int
      $tracknumber = $tags[ 'id3v1' ][ 'track' ][0];
    }
    else if( isset( $tags[ 'id3v2' ][ 'track_number' ][0] ) && !empty( $tags[ 'id3v2' ][ 'track_number' ][0] ) )
    {
      //or it could be 5/12
      $temp = explode( '/', $tags[ 'id3v2' ][ 'track_number' ][0] );
      $tracknumber = $temp[0];
    }
    else if( isset( $tags[ 'ape' ][ 'track_number' ][0] ) && !empty( $tags[ 'ape' ][ 'track_number' ][0] ) )
    {
      //or it could be 5/12 APEX
      $temp = explode( '/', $tags[ 'ape' ][ 'track_number' ][0] );
      $tracknumber = $temp[0];
    }
    else
    {
      //or it's missing
      $tracknumber = 0;
    }
    
    $song_array = array();
    $song_array[ 'artist_name' ]      = StreemeUtil::xmlize_utf8_string( ( $tags['ape'][ 'artist' ][0] ) ? $tags['ape'][ 'artist' ][0] : ( ( $tags['id3v2'][ 'artist' ][0] ) ? $tags['id3v2'][ 'artist' ][0] : ( ( $tags['id3v1'][ 'artist' ][0] ) ? $tags['id3v1'][ 'artist' ][0] : null ) ) );
    $song_array[ 'album_name' ]       = StreemeUtil::xmlize_utf8_string( ( $tags['ape'][ 'album' ][0] )  ? $tags['ape'][ 'album' ][0]  : ( ( $tags['id3v2'][ 'album' ][0] )  ? $tags['id3v2'][ 'album' ][0]  : ( ( $tags['id3v1'][ 'album' ][0] )  ? $tags['id3v1'][ 'album' ][0]  : null ) ) );
    $song_array[ 'song_name' ]        = StreemeUtil::xmlize_utf8_string( ( $tags['ape'][ 'title' ][0] )  ? $tags['ape'][ 'title' ][0]  : ( ( $tags['id3v2'][ 'title' ][0] )  ? $tags['id3v2'][ 'title' ][0]  : ( ( $tags['id3v1'][ 'title' ][0] )  ? $tags['id3v1'][ 'title' ][0]  : $pinfo['filename'] ) ) );
    $song_array[ 'song_length' ]      = $value[ 'playtime_string' ] ;
    $song_array[ 'accurate_length' ]  = ( floor( ( (float) $value[ 'playtime_seconds' ] ) * 1000 ) );
    $song_array[ 'genre_name' ]       = ( $tags['ape'][ 'genre' ][0] )  ? $tags['ape'][ 'genre' ][0]  : ( ( $tags['id3v2'][ 'genre' ] ) ? $tags['id3v2'][ 'genre' ][0] :  ( ( $tags['id3v1'][ 'genre' ][0] )  ? $tags['id3v1'][ 'genre' ][0]  : null ) );
    $song_array[ 'filesize' ]             = $file_stat[ 'size' ];
    $song_array[ 'bitrate' ]          = ( floor ( ( (int) $value[ 'audio' ][ 'bitrate' ] ) / 1000 ) );
    $song_array[ 'yearpublished' ]             = ( $tags['ape'][ 'year' ][0] )   ? $tags['ape'][ 'year' ][0]   : ( ($tags['id3v2'][ 'year' ][0] ) ? $tags['id3v2'][ 'year' ][0] : ( ( $tags['id3v1'][ 'year' ][0] )  ? $tags['id3v1'][ 'year' ][0]  : null ) );
    $song_array[ 'tracknumber']      = $tracknumber;
    $song_array[ 'label' ]            = StreemeUtil::xmlize_utf8_string( ( $tags['ape'][ 'label' ][0] )  ? $tags['ape'][ 'label' ][0]  : ( ( $tags['id3v2'][ 'label' ][0] ) ? $tags['id3v2'][ 'label' ][0] : null ) ); //not available in V1
    $song_array[ 'mtime' ]            = $file_stat[ 'mtime' ];
    $song_array[ 'atime' ]            = $file_stat[ 'atime' ];
    $song_array[ 'filename' ]         = $streeme_path_name;

    unset( $value, $tags, $file_stat, $temp ); //free the RAM used by the temp containters
    /* End Ugliness */
      
    $media_scanner->add_song( $song_array );
  }
  
  closedir($dp);
}
?>