<?php
class listArtistsAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    echo json_encode( Doctrine_Core::getTable('Artist')->getList( ( $alpha ) ? $alpha : 'all' ) ); 
    exit; 
  }
}