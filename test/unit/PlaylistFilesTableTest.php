<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 11, new lime_output_color() );

$playlist_files_table = Doctrine_Core::getTable('PlaylistFiles');

//add the required fixtures
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/00_PlaylistFilesTable');

$t->comment( '->addPlaylistFile()' );
$result = $playlist_files_table->addPlaylistFiles( 1, '9ewf9ewjfa0jew90fejf9fje' , 'song' );
$t->is( $result, true, 'added a new song to the default playlist' );
$result2 = $playlist_files_table->addPlaylistFiles( 1, 2, 'album' );
$t->is( $result2, true, 'added a new album to the default playlist' );
$result3 = $playlist_files_table->addPlaylistFiles( 1, 1, 'artist' );
$t->is( $result3, true, 'added a new artist to the default playlist' );

$t->comment( '->addFiles()' );
$result = $playlist_files_table->addFiles(1, array());
$t->is($result, false, 'Array with no items is rejected');
$result = $playlist_files_table->addFiles(1, '');
$t->is($result, false, 'String is rejected');
$result = $playlist_files_table->addFiles(1,array(
                          array('filename'=>'file://localhost/home/music/song.mp3'),
                          array('filename'=>'file://localhost/home/music/song3.mp3'))
                          );
$t->is($result, true, 'Multiple valid items in array');

$t->comment('->isFileInPlaylist( $playlist_id, $filename )');
$t->ok($playlist_files_table->isFileInPlaylist( 1, 'file://localhost/home/music/song.mp3' ), 'song is already in playlist 1');
$t->ok($playlist_files_table->isFileInPlaylist( 1, 'file://localhost/home/music/song3.mp3' ), 'song3 is already in playlist 1');
$t->is($playlist_files_table->isFileInPlaylist( 2, 'file://localhost/home/music/song3.mp3' ), false ,'song3 is not in playlist 2');

$t->comment( '->deletePlaylistFile()' );
$result4 =  $playlist_files_table->deletePlaylistFile( 1, '9ewf9ewjfa0jew90fejf9fje' );
$t->is( $result4, 1, 'removed a song from the playlist' );

$t->comment( '->deleteAllPlaylistFiles()');
$result=$playlist_files_table->deleteAllPlaylistFiles( 1 );
$t->is($result, 5, 'Removed all playlist files from playlist 1');
