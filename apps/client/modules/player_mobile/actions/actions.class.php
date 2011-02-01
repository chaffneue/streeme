<?php

/**
 * player_mobile actions.
 *
 * @package    streeme
 * @subpackage player_mobile
 * @author     Richard Hoar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class player_mobileActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    //get the user's media listings
    $this->genre_list = null;//Doctrine_Core::getTable('Genre')->getList();
    $this->artist_list = Doctrine_Core::getTable('Artist')->getList('B');
    $this->album_list = Doctrine_Core::getTable('Album')->getList();
    $this->playlist_list = null;//Doctrine_Core::getTable('Playlist')->getList();
    
    //the port used for media
    $this->music_proxy_port = sfConfig::get( 'app_music_proxy_port' );
  }
}
