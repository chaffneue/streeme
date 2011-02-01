<?php
class listGenresAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    echo json_encode( Doctrine_Core::getTable('Genre')->getList( ( $alpha ) ? $alpha : 'all' ) ); 
    exit; 
  }
}