<?php
class listPlaylistsAction extends sfAction
{
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    $this->content = json_encode( Doctrine_Core::getTable('Playlist')->getList( ( $alpha ) ? $alpha : 'all' ) );
    
    sfConfig::set('sf_web_debug', false);
    $this->setTemplate('output');
    $this->setLayout(false);
  }
}