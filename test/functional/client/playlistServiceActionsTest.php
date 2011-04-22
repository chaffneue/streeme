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
  
  info('Without an alpha letter')->
  get('/service/listPlaylists', array())->
  
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'listPlaylists')->
  end()->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\[\{\"id\"\:\"1\",\"name\"\:\"Default Playlist\"\},\{\"id\"\:\"2\",\"name\"\:\"testplaylist\"\}\]/')->
  end()->
  
  info('With alpha letter "t"')->
  get('/service/listPlaylists', array( 'alpha' => 't' ))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\[\{\"id\"\:\"2\",\"name\"\:\"testplaylist\"\}\]/')->
  end()->
  
  info('4. Add content to the playlist')->

  info('no playlist specified for adding content')->
  post('/service/addPlaylistContent', array())->
  
  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'addPlaylistContent')->
  end()->
  
  with('response')->begin()->
    isStatusCode(404)->
  end()->
  
  info('Playlist 2 specified adding song 1')->
  post('/service/addPlaylistContent', array('playlist_id'=>'2', 'id' =>'9qw9dwj9wqdjw9qjqw9jd', 'type' => 'song'))->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  
  info('validate content added')->
  
  get('/service/listSongs?_=1304003896164&sEcho=2&iColumns=9&sColumns=&iDisplayStart=0&iDisplayLength=60&sSearch=playlistid%3A2&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=true&sSearch_1=&bRegex_1=false&bSearchable_1=true&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=true&sSearch_5=&bRegex_5=false&bSearchable_5=true&sSearch_6=&bRegex_6=false&bSearchable_6=true&sSearch_7=&bRegex_7=false&bSearchable_7=true&sSearch_8=&bRegex_8=false&bSearchable_8=true&iSortingCols=1&iSortCol_0=4&sSortDir_0=desc&bSortable_0=true&bSortable_1=true&bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true&bSortable_7=true&bSortable_8=true')->
  with('response')->matches('/9qw9dwj9wqdjw9qjqw9jd/')->
  
  info('no playlist content specified to delete')->
  post('/service/deletePlaylistContent', array())->

  with('request')->begin()->
    isParameter('module', 'service')->
    isParameter('action', 'deletePlaylistContent')->
  end()->
  
  with('response')->begin()->
    isStatusCode(404)->
  end()->
  
  info('Playlist 2 specified deleting song 1')->
  post('/service/deletePlaylistContent', array('playlist_id'=>'2', 'id' =>'9qw9dwj9wqdjw9qjqw9jd', 'type' => 'song'))->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  
  info('validate content added')->
  
  get('/service/listSongs?_=1304003896164&sEcho=2&iColumns=9&sColumns=&iDisplayStart=0&iDisplayLength=60&sSearch=playlistid%3A2&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=true&sSearch_1=&bRegex_1=false&bSearchable_1=true&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=true&sSearch_5=&bRegex_5=false&bSearchable_5=true&sSearch_6=&bRegex_6=false&bSearchable_6=true&sSearch_7=&bRegex_7=false&bSearchable_7=true&sSearch_8=&bRegex_8=false&bSearchable_8=true&iSortingCols=1&iSortCol_0=4&sSortDir_0=desc&bSortable_0=true&bSortable_1=true&bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true&bSortable_7=true&bSortable_8=true')->
  //regex makes sure it no longer exists
  with('response')->matches('/^((?!9qw9dwj9wqdjw9qjqw9jd).)*$/')->
  
  info('5. Delete the test playlist')->
  
  post('/service/deletePlaylist', array('playlist_id' => '2'))->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  
  get('/service/listPlaylists', array( 'alpha' => 't' ))->
  
  with('response')->begin()->
    isStatusCode(200)->
    matches('/\[\]/')->
  end()  
;
