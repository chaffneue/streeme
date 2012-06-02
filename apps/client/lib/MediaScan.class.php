<?php
/**
 * Media Scanner
 *
 * This class manages the library scanning process for a users music library. It will scan and update/add songs
 * and cleanup songs that have been removed or are out of date
 *
 * @package    streeme
 * @subpackage media scanner
 * @author     Richard Hoar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
  
class MediaScan
{
  /**
   * int: Stores the last scan id for this scanning session
   */
  public $scan_id = false;
  
  /**
   * int: This is a counter for songs skipped during the scan
   */
  public $skipped_songs= 0;
  
  /**
   * int: This is a counter for songs added during the scan
   */
  public $added_songs= 0;
  
  /**
   * int: This is a counter for total songs scanned
   */
  public $total_songs= 0;
  
  /**
   * int: count artists added during the scan
   */
  public $added_artists= array();
  
  /**
   * int: count albums added during the scan
   */
  public $added_albums= array();
  
  /**
   * int: count custom genres added during the scan
   */
  public $added_genres= array();
  
  /**
   * int: count songs removed during the scan
   */
  public $removed_songs= 0;
  
  /**
   * int: count albums removed during the scan
   */
  public $removed_albums= 0;
  
  /**
   * int: count artists removed druing the scan
   */
  public $removed_artists= 0;
  
  /**
   * int: count genres removed during the scan
   */
  public $removed_genres= 0;

  /**
   * obj: table instances
   */
  protected $artist_table, $song_table, $album_table, $song_genres_table;
  
  /**
   * initialize the library scan by setting a new last_scan_id for the session
   */
  public function __construct()
  {
    //Since this class services a batch script, stop Doctrine from leaving objects in memory
    Doctrine_Manager::connection()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );
    $this->scan_id = Doctrine_Core::getTable('Scan')->addScan( 'library' );
    $this->artist_table = Doctrine_Core::getTable( 'Artist' );
    $this->song_table = Doctrine_Core::getTable( 'Song' );
    $this->album_table = Doctrine_Core::getTable( 'Album' );
    $this->song_genres_table = Doctrine_Core::getTable('SongGenres');
    $this->genre_table = Doctrine_Core::getTable('Genre');
  }
  
  /**
   *  return the current last_scan_id in the scanning sequence
   *  @return        int:last_scan_id or false
   */
  public function get_last_scan_id()
  {
    return $this->scan_id;
  }
  
  /**
   * Check if the file we're about to add is already in the database and return true if it's scanned
   *
   * @param $filename  str itunes style filename
   * @param $mtime     int time modified unix timestamp
   * $return           bool: if is scanned = true|false
   */
  public function is_scanned( $filename, $mtime )
  {
    //increment the total song count
    $this->total_songs++;
    
    //have we seen this song before?
    $song = $this->song_table->updateScanId( $filename, (int) $mtime, $this->scan_id );
    if( $song > 0 )
    {
      $this->skipped_songs++;
      return true;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Populate the song list from an array
   * Parameter order is not important
   * @param $song_array array - contents
   *   artist_name     str name of the artist
   *   album_name      str name of the album
   *   genre_name      str genre name
   *   id3_genre_id    int id3 V1 or winamp extension ID eg. 0 - 125
   *   song_name       str name of the song
   *   song_length     str mins:secs
   *   accurate_length int milliseconds
   *   size            int file size
   *   bitrate         int bitrate
   *   year            int year
   *   track_number    int track number on the album
   *   label           str label
   *   mtime           int time modified unix timestamp
   *   atime           int time added to itunes unix timestamp
   *   filename        str itunes style filename
   *  @return          int: new song id
   */
  public function add_song( $song_array )
  {
    $artist_name = ( $song_array['artist_name'] ) ? $song_array['artist_name'] : 'Unknown Artist';
    $artist_id = $this->artist_table->addArtist( $artist_name );
    if( !empty( $artist_id ) )
    {
      $this->added_artists[ $artist_id ] = 1;
    }
    $album_name = ( $song_array['album_name'] ) ? $song_array['album_name'] : 'Unknown Album';
    $album_id = $this->album_table->addAlbum( $album_name );
    if( !empty( $album_id ) )
    {
      $this->added_albums[ $album_id ] = 1;
    }
    $song_id = $this->song_table->addSong( $artist_id, $album_id, (int) $this->scan_id, $song_array );
    $this->added_songs++;
    $genre_ids = $this->song_genres_table->addSongGenres((int) $song_id, $song_array['genre_name']);
    
    unset($artist_name, $artist_id, $album_name, $album_id, $song_id, $genre_ids, $song_array );
    
    return (int) $song_id;
  }
  
  /**
   * Clean up songs that did not check in during the scan - remove their associated
   * relations to genre, albums, artists as well
   * @return           int total records removed in the cleanup
   */
  public function finalize_scan()
  {
    $this->removed_songs   = $this->song_table->finalizeScan( $this->scan_id );
    $this->removed_artists = $this->artist_table->finalizeScan();
    $this->removed_albums  = $this->album_table->finalizeScan();
    $this->removed_genres  = $this->song_genres_table->finalizeScan();
    
    return $this->removed_songs + $this->removed_artists + $this->removed_albums;
  }
  
  /**
   * Summarize changes made to a user's library at the very end of a scan
   * @return           str an summary of actions taken during scanning
   */
  public function get_summary()
  {
    $string  = null;
    $string .= 'Total Songs Scanned: ' . (string) $this->total_songs . " \r\n";
    $string .= 'Songs Skipped: ' . (string) $this->skipped_songs . " \r\n";
    $string .= 'Songs Added: ' . (string) $this->added_songs . " \r\n";
    $string .= 'Albums Added: ' . (string) count( $this->added_albums ) . " \r\n";
    $string .= 'Artists Added: ' . (string) count( $this->added_artists ) . " \r\n";
    $string .= 'Songs Removed: ' . (string) $this->removed_songs . " \r\n";
    $string .= 'Albums Removed: ' . (string) $this->removed_albums . " \r\n";
    $string .= 'Artists Removed: ' . (string) $this->removed_artists . " \r\n";
  
    return $string;
  }
}
