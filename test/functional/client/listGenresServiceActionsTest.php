<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info( '1. Internal services should be secure' )->
  
  get('/service/listGenres')->
   
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'listGenres')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()->
  
  info('2. Login and Get a list of Genres')->
  
  authenticate()->
  
  get('/service/listGenres')->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"2\"/')->
    matches('/\"id\"\:\"144\"/')->
  end()->
    
  info('3. Get list by alphabetical character')->
  
  get('/service/listGenres', array('alpha'=>'p'))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\"id\"\:\"144\"/')->
  end()
;