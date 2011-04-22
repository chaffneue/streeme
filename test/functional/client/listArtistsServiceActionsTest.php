<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info( '1. Internal services should be secure' )->
  
  get('/service/listArtists')->
   
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'listArtists')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()->
  
  info('2. Login and Get a list of Artists')->
  
  authenticate()->
  
  get('/service/listArtists')->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"1\"/')->
    matches('/\"id\"\:\"2\"/')->
  end()->
    
  info('3. Get list by alphabetical character')->
  
  get('/service/listArtists', array('alpha'=>'s'))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"1\"/')->
  end()
;