<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 8, new lime_output_color() );

Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/60_SongGenresTable');

$song_genres_table = Doctrine_Core::getTable('SongGenres');

$t->comment( '->addSongGenres' );
$add_single_results = $song_genres_table->addSongGenres(1, 'Testing a new kind of Rock');
$t->is_deeply($add_single_results, array('127'), 'Added New Genres - "Testing a new kind of Rock"');
$add_single_results = $song_genres_table->addSongGenres(2, 'Rock');
$t->is_deeply($add_single_results, array('18'), 'Added New Genres - "Rock"');
$add_multiple_results = $song_genres_table->addSongGenres(3, 'Rock;Funk;Something Awesome');
$t->is_deeply($add_multiple_results, array('18','6','128'), 'Added New Genres - "Rock;Funk;Something Awesome"');
$add_multiple_results = $song_genres_table->addSongGenres(4, 'Rock; Funk ; Another Genre');
$t->is_deeply($add_multiple_results, array('18','6','129'), 'Added New Genres with whitespace- "Rock; Funk ; Another Genre"');

$t->comment( '->getList' );
$genre_list = $song_genres_table->getList();
$t->is(array_keys($genre_list), array(0,1,2,3,4), 'Correct listing of Genres.');
$delete_song = SongTable::getInstance()->find(4);
$delete_song->delete();
$genre_list = $song_genres_table->getList();
$t->is(array_keys($genre_list), array(0,1,2,3), 'Correct listing of Genres with genre 4 deleted.');
$genre_list = $song_genres_table->getList('S');
$t->is(array_keys($genre_list), array(0), 'Correct listing of Genres by alpha S');
$delete_song = SongTable::getInstance()->find(1);
$delete_song->delete();

$t->comment( '->finalizeScan' );
$result = $song_genres_table->finalizeScan();
$t->is($result, 4, 'removed missing genre records.');
