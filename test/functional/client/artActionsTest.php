<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  get('/art/default/large')->

  with('request')->begin()->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()
;
