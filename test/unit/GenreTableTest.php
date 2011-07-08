<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 6, new lime_output_color() );

$genre_table = Doctrine_Core::getTable('Genre');

$t->comment( '->addGenre' );
$first_insert_id = $genre_table->addGenre( 'Electronic' );
$t->is( $first_insert_id, '53', 'Successfully selected an existing genre.' );
$second_insert_id = $genre_table->addGenre( 'Electronic' );
$t->is( $first_insert_id, $second_insert_id, 'Got the same genre fixture.');
$third_insert_id = $genre_table->addGenre( 'Some Awesome Custom Genre! Woo!' );
$t->like( $third_insert_id, '/\d+/', 'Successfully added a new genre entry.' );
$fourth_insert_id = $genre_table->addGenre( ' Some Awesome Custom Genre! Woo! ' );
$t->is( $third_insert_id, $fourth_insert_id, 'Selected retargeted the second genre entry.');
$fifth_insert_id = $genre_table->addGenre( 'Русский' );
$t->like( $fifth_insert_id, '/\d+/', 'Successfully added a new UTF-8 entry' );

$t->comment( '->finalizeScan' );
$genre_table->addGenre( 'This should be deleted on next finalize' );
$t->is( $genre_table->finalizeScan(), 3, 'finalized scan successfully' );