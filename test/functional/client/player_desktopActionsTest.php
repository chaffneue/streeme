<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info('1. Desktop Player - this page should be secure')->
  
  get('/player/desktop')->

  with('request')->begin()->
    isParameter('module', 'player_desktop')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()
;

$browser->
  info('2. Desktop Player - login and test partials')->
  
  authenticate()->
  
  get('/player/desktop')->
  
  with('request')->begin()->
    isParameter('module', 'player_desktop')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    info('2.1 Validating response')->
    isStatusCode(200)->
    info('2.2 Validating partials')->
    checkElement('#loadjavascript')->
    checkElement('#musicplayer')->
    checkElement('#songlist')->
    checkElement('#magnify_art')->
    checkElement('#browseplaylist')->
    checkElement('#browseartist')->
    checkElement('#browsealbum')->
  end()
;