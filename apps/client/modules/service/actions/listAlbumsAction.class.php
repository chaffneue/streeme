<?php
class listAlbumsAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    $artist_id = $request->getParameter( 'artist_id' );
    echo json_encode( Doctrine_Core::getTable('Album')->getList( ( $alpha ) ? $alpha : 'all', ( $artist_id ) ? $artist_id : 'all' ) ); 
    exit; 
  }
}