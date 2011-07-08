<?php

/**
 * player_desktop actions.
 *
 * @package    streeme
 * @subpackage player_desktop
 * @author     Richard Hoar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class player_desktopActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    //get the user's media listings
    $this->genre_list = Doctrine_Core::getTable('SongGenres')->getList();
    $this->artist_list = Doctrine_Core::getTable('Artist')->getList();
    $this->album_list = Doctrine_Core::getTable('Album')->getList();
    $this->playlist_list = Doctrine_Core::getTable('Playlist')->getList();
    
    //the port used for media
    $this->music_proxy_port = sfConfig::get( 'app_music_proxy_port' );
  }
  
  /**
   * Execute the playlist action - refreshes the playlist partial in the UI
   *
   * @param sfRequest $request A request object
   */
  public function executePlaylist(sfWebRequest $request)
  {
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);
    $this->playlist_list = Doctrine_Core::getTable('Playlist')->getList();
  }
}
