<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info( '1. Internal services should be secure' )->
  
  get('/service/addPlaylist')->
   
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'addPlaylist')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()->
  
  info('2. Login and add a playlist')->
 
  authenticate()->
  post('/service/addPlaylist', array('name' => 'testplaylist'))->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  
  info('3. Read the new playlist')->
  
  get('/service/listPlaylists', array( 'alpha' => 't' ))->
  
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'listPlaylists')->
  end()->
  
  with('response')->begin()->
    isStatusCode(200)->
    isHeader('Content-Type', 'application/json')->
  end()
;
