<?php
/**
 * art actions.
 *
 * @package    streeme
 * @subpackage art
 * @author     Richard Hoar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class artActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    /* Placeholder - action takes place in web/proxy-art.php
    $art_proxy = new ArtProxy( $request->getParameter( 'hash' ),  $request->getParameter( 'size' ) );
    $art_proxy->getImage();
    */
    exit;
  }
}