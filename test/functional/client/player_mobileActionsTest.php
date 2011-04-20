<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info('1. Mobile Player - this page should be secure')->
  
  get('/player/mobile')->

  with('request')->begin()->
    isParameter('module', 'player_mobile')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()
;

$browser->
  info('2. Mobile Player - login and test partials')->
  
  authenticate()->
  
  get('/player/mobile')->
  
  with('request')->begin()->
    isParameter('module', 'player_mobile')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    info('2.1 Validating response')->
    isStatusCode(200)->
    info('2.2 Validating partials')->
    checkElement('#container')->
    checkElement('#songlist')->
    checkElement('#artistlistcontainer')->
    checkElement('#albumlistcontainer')->
    checkElement('#musicplayer')->
    checkElement('#settingscontainer')->
    checkElement('#playlistlistcontainer')->
    checkElement('#loadjavascript')->
    checkElement('#welcomescreen')->
    checkElement('#header')->
    checkElement('#genrelistcontainer')->
  end()
;