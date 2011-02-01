<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 4, new lime_output_color() );

$playlist_files_table = Doctrine_Core::getTable('PlaylistFiles');

//add the required fixtures
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/00_PlaylistFilesTable');

$t->comment( 'addPlaylistFile' );
$result = $playlist_files_table->addPlaylistFiles( 1, '9ewf9ewjfa0jew90fejf9fje' , 'song' );
$t->is( $result, true, 'added a new song to the default playlist' );
$result2 = $playlist_files_table->addPlaylistFiles( 1, 2, 'album' );
$t->is( $result2, true, 'added a new album to the default playlist' );
$result3 = $playlist_files_table->addPlaylistFiles( 1, 1, 'artist' );
$t->is( $result3, true, 'added a new artist to the default playlist' );

$t->comment( 'deletePlaylistFile' );
$result4 =  $playlist_files_table->deletePlaylistFile( 1, '9ewf9ewjfa0jew90fejf9fje' );
$t->is( $result4, 1, 'removed a song from the playlist' );