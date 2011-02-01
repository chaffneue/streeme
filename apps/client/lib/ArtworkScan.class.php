<?php
/**
 * Artwork Scanner
 *
 * This class manages the library scanning process for a users artwork library. It will scan
 * using a number of sources and caches retrieved imaged to speed up future scans
 *
 * @package    streeme
 * @subpackage artwork scanner
 * @author     Richard Hoar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */

Class ArtworkScan
{
  protected $scan_id = 0;
  protected $total_artwork = 0;
  protected $skipped_artwork = array();
  protected $added_artwork = 0;
  protected $source;
    
  /**
   * initialize the library scan by setting a new scan_id for the session
   * @param source str: amazon|meta|folders|service
   */
  public function __construct( $source )
  {
    //Since this class services a batch script, stop Doctrine from leaving objects in memory
    Doctrine_Manager::connection()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );
    $this->source = $source;
    $this->scan_id = Doctrine_Core::getTable('Scan')->addScan( 'artwork' );
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
  public function get_source()
  {
    return $this->source;
  }
  
  public function get_unscanned_artwork_list()
  {
    return Doctrine::getTable( 'Album' )->getUnscannedArtList( $this->get_source() );
  }
  
  /**
   * flag an album as skipped for album art - the source images were not available
   * @param album_id  int: the album's database ID
   */
  public function flag_as_skipped( $album_id )
  {
    if ( empty( $this->scan_id ) ) return false;
    $success = Doctrine_Core::getTable( 'Album' )->setAlbumArtSourceScanned( $album_id, $this->scan_id , $this->source );
    if( $success )
    {
      $this->skipped_artwork[ $album_id ] = 1;
      return true;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Artwork files were successfully added for this album, so flag an album as having album art to speed up future scans
   * @param album_id  int: the album's database ID
   */
  public function flag_as_added( $album_id )
  {
    if ( empty( $this->scan_id ) ) return false;
    $success = Doctrine_Core::getTable( 'Album' )->setAlbumArtAdded( $album_id, $this->scan_id , $this->source );
    if( $success )
    {
      $this->added_artwork++;
      return true;
    }
    else
    {
      return false;
    }
  }
    
  /**
   * Summarize changes made to a user's library at the very end of a scan
   * @return           str an summary of actions taken during scanning
   */
  public function get_summary()
  {
    $total_albums = Doctrine_Core::getTable( 'Album' )->getTotalAlbumsCount();
    $albums_with_art = Doctrine_Core::getTable( 'Album' )->getAlbumsWithArtCount();
    $skipped_artwork = count( $this->skipped_artwork );
    $string  = null;
    $string .= 'Total Albums: ' . $total_albums . " \r\n";
    $string .= 'Total Albums with Art: ' .  $albums_with_art . ' (' . @( floor ( ( $albums_with_art / $total_albums ) * 100 ) ) . '%)' . "\r\n";
    $string .= 'Artwork Unavailable this Scan: ' . (string) $skipped_artwork . " \r\n";
    $string .= 'Artwork Added this Scan: ' . (string) $this->added_artwork . " \r\n";
    
    return $string;
  }
}
?>