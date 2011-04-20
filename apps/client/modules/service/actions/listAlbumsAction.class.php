<?php
class listAlbumsAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    $artist_id = $request->getParameter( 'artist_id' );
    $this->content = json_encode( Doctrine_Core::getTable('Album')->getList( ( $alpha ) ? $alpha : 'all', ( $artist_id ) ? $artist_id : 'all' ) ); 
    
    sfConfig::set('sf_web_debug', false);
    $this->setTemplate('output');
    $this->setLayout(false);
  }
}