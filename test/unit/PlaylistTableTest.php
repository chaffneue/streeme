<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 3, new lime_output_color() );

$playlist_table = Doctrine_Core::getTable('Playlist');

$t->comment( '->addPlaylist' );
$first_insert_id  = $playlist_table->addPlaylist('A Playlist');
$t->like( $first_insert_id, '/\d+/', 'Successfully added a playlist entry.' );
$t->comment( '->deletePlaylist' );
$deleted_row_count = $playlist_table->deletePlaylist( $first_insert_id );
$t->is( $deleted_row_count, 1, 'Successfully deleted a playlist entry' );
$t->comment( '->getList' );
$list  = $playlist_table->getList();
$count = count( $list );
$t->is( $count, 1, 'Correct list size' );