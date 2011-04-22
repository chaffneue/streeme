<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info( '1. Internal services should be secure' )->
  
  get('/service/listAlbums')->
   
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'listAlbums')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()->
  
  info('2. Login and Get a list of Albums')->
  
  authenticate()->
  
  get('/service/listAlbums')->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"1\"/')->
    matches('/\"id\"\:\"2\"/')->
  end()->
  
  info('3. Get list by artist id')->
  
  get('/service/listAlbums', array('artist_id'=>'1'))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"1\"/')->
  end()->
  
  info('4. Get list by alphabetical character')->
  
  get('/service/listAlbums', array('alpha'=>'t'))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"2\"/')->
  end()->
  
  info('5. Get list by alphabetical character and artist id')->
  
  get('/service/listAlbums', array('artist_id'=>'2', 'alpha'=>'t'))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"2\"/')->
  end()
;