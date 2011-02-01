<?php
class listPlaylistsAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    echo json_encode( Doctrine_Core::getTable('Playlist')->getList( ( $alpha ) ? $alpha : 'all' ) ); 
    exit; 
  }
}