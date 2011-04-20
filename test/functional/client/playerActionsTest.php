<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DoctrineTestFunctional(new sfBrowser());
$browser->loadData()->restart();

$browser->
  info('1. PLayer Selector - this page should be secure')->
  
  get('/player')->

  with('request')->begin()->
    isParameter('module', 'player')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
  end()
;

$browser->
  info('2. Player Selector - login and test partials')->
  
  authenticate()->
  
  get('/player')->
  
  with('request')->begin()->
    isParameter('module', 'player')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    info('2.1 Validating response')->
    isStatusCode(200)->
    info('2.2 Validating partials')->
    checkElement('#button_text')->
  end()
;