<?php
/**
 * artworkScanResync
 *
 * Resync the has_art flag in the database to resync the existing art cache so it doesn't
 * get scanned again after a database initialization
 *
 * @package    streeme
 * @author     Richard Hoar
 */
error_reporting( 0 );
$artwork_scanner        = new ArtworkScan( 'resync' );
$artwork_list           = $artwork_scanner->get_unscanned_artwork_list();
$current_album_id       = 0;

if ( !$artwork_list )
{
  echo( "*** All scans are up to date: no need to resync ***" );
  $artwork_list = array();
}

foreach( $artwork_list as $key => $value )
{
  $art_dir = dirname( __FILE__ ) . '/../../../data/album_art/' . md5( $value[ 'artist_name' ] . $value[ 'album_name' ] );
  
  //this album has copied art, skip to the next album
  if ( $current_album_id == $value[ 'album_id' ] ) continue;
  
  echo 'Scanning: ' .  $value[ 'album_name' ] . ' by: ' .  $value[ 'artist_name' ] . "\r\n";
  
  if( is_readable( $art_dir . '/large.jpg' ) && is_readable( $art_dir . '/medium.jpg' ) && is_readable( $art_dir . '/small.jpg' ) )
  {
    //this item has art in cache - update the database accordingly
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_added( $value[ 'album_id' ] );
  }
  else
  {
    //this does not yet have art - update the database accordingly
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
  }
}