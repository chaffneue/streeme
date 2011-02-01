<?php
/**
 * play actions.
 *
 * @package    streeme
 * @subpackage play
 * @author     Richard Hoar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class playActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    /* Placeholder - action takes place in web/proxy-play.php
    $mediaplayer = new MediaProxy( $request->getParameter( 'unique_song_id' ), $request->getParameter( 'target_bitrate' ), $request->getParameter( 'target_format' ), $request->getParameter( 'is_icy_response' ) );
    $mediaplayer->play(); 
    */
    exit;
  }
}