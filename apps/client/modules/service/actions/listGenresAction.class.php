<?php
class listGenresAction extends sfAction
{  
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    $this->content = json_encode( Doctrine_Core::getTable('Genre')->getList( ( $alpha ) ? $alpha : 'all' ) ); 
    
    sfConfig::set('sf_web_debug', false);
    $this->setTemplate('output');
    $this->setLayout(false);
  }
}