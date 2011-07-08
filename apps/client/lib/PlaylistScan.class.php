<?php
/**
 * Playlist Scanner
 *
 * This class manages the library scanning process for a user's playlists.
 *
 * @package    streeme
 * @subpackage playlist scanner
 * @author     Richard Hoar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */

Class PlaylistScan
{
  protected $scan_id = 0;
  protected $total_playlists = 0;
  protected $skipped_playlists = 0;
  protected $added_playlists = 0;
  protected $updated_playlists = 0;
  protected $removed_playlists = 0;
  protected $service_name = null;
    
  /**
   * initialize the library scan by setting a new scan_id for the session
   * @param source str: add a service name for each scanner
   */
  public function __construct( $service_name )
  {
    //Since this class services a batch script, stop Doctrine from leaving objects in memory
    Doctrine_Manager::connection()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );
    $this->service_name = $service_name;
    $this->scan_id = Doctrine_Core::getTable('Scan')->addScan('playlist');
  }
  
  /**
   *  return the current scan_id in the scanning sequence
   *  @return        int:scan_id
   */
  public function get_last_scan_id()
  {
    return $this->scan_id;
  }

  /**
   * return the source type
   */
  public function get_service_name()
  {
    return $this->service_name;
  }
  
  /**
   * Check if the file we're about to add is already in the database and return true if it's scanned
   *
   * @param filename          str: service name to add
   * @param playlist_name     str: the name of the playlist
   * @param service_unique_id str: anything string/permalink that helps make the playlist unique
   * $return                  int: playlist_id
   */
  public function is_scanned( $service_name, $playlist_name, $service_unique_id = null )
  {
    //increment the total playlist count for this service
    $this->total_playlists++;
    
    //have we seen this playlist before?
    $playlist_id = Doctrine_Core::getTable( 'Playlist' )->updateScanId( $service_name, $playlist_name, $service_unique_id, $this->get_last_scan_id() );
  
    return $playlist_id;
  }
  
  /**
   * Remove and replace all playlist files for a given playlist or add a new playlist
   * from scratch.
   *
   * @param playlist_name     str: new playlist name
   * $param playlist_files    arr: the files to be added to the playlist
   * @param scan_id           int: the scan id for a service scanner
   * @param service_name      str: the name of the service this playlist comes from eg.itunes
   * @param service_unique_id str: any metadata key string to make the playlist more unique
   * @return                  int: playlist_id
   */
  public function add_playlist($playlist_name, $playlist_files=array(), $playlist_id=0, $scan_id=0, $service_name=null, $service_unique_id=null)
  {
    if(
           isset($playlist_name)
           &&
           strlen($playlist_name) > 0
           &&
           $playlist_id === 0
           &&
           is_array($playlist_files)
           &&
           count($playlist_files) > 0
       )
    {
      $this->added_playlists++;
      $playlist_id = PlaylistTable::getInstance()->addPlaylist($playlist_name, $scan_id, $service_name, $service_unique_id);
      PlaylistFilesTable::getInstance()->addFiles($playlist_id, $playlist_files);
    }
    else if(
           isset($playlist_name)
           &&
           strlen($playlist_name) > 0
           &&
           $playlist_id !== 0
           &&
           is_array($playlist_files)
           &&
           count($playlist_files) > 0
       )
    {
      $this->updated_playlists++;
      PlaylistFilesTable::getInstance()->deleteAllPlaylistFiles( $playlist_id );
      PlaylistFilesTable::getInstance()->addFiles($playlist_id, $playlist_files);
    }
    else
    {
      $this->skipped_playlists++;
    }
    
    return $playlist_id;
  }
  
  /**
   * Purge playlists that did not make it in the scan.
   * @return           int total records removed in the cleanup
   */
  public function finalize_scan( PlaylistFilesTable $playlist_files)
  {
    $this->removed_playlists = Doctrine_Core::getTable('Playlist')->finalizeScan( $playlist_files, $this->scan_id, $this->service_name );
    
    return $this->removed_playlists;
  }
  
  /**
   * Get the total playlist count
   * @return          int: count
   */
  public function get_total_playlists()
  {
    return $this->total_playlists;
  }
  
  /**
   * Get the skipped playlist count
   * @return          int: count
   */
  public function get_skipped_playlists()
  {
    return $this->skipped_playlists;
  }

  /**
   * Get the added playlist count
   * @return          int: count
   */
  public function get_added_playlists()
  {
    return $this->added_playlists;
  }
  
  /**
   * Get the updated playlist count
   * @return          int: count
   */
  public function get_updated_playlists()
  {
    return $this->updated_playlists;
  }
  
  /**
   * Get the removed playlist count
   * @return          int: count
   */
  public function get_removed_playlists()
  {
    return $this->removed_playlists;
  }
  
  /**
   * Summarize changes made to a user's library at the very end of a scan
   * @return           str: an summary of actions taken during scanning
   */
  public function get_summary()
  {
    $string  = null;
    $string .= 'Total Playlists Scanned: ' . $this->get_total_playlists() . " \r\n";
    $string .= 'Playlists Skipped: ' . $this->get_skipped_playlists() . " \r\n";
    $string .= 'Playlists Added: ' . $this->get_added_playlists() . " \r\n";
    $string .= 'Playlists Updated: ' . $this->get_updated_playlists() . " \r\n";
    $string .= 'Playlists Removed: ' . $this->get_removed_playlists() . " \r\n";

    return $string;
  }
}