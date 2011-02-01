<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 10, new lime_output_color() );

$artist_table = Doctrine_Core::getTable('Artist');

$t->comment( '->addArtist' );
$first_insert_id = $artist_table->addArtist( 'Sigur Rós' );
$t->like( $first_insert_id, '/\d+/', 'Successfully added an artist entry.' );
$second_insert_id = $artist_table->addArtist( 'Sigur Rós' );
$t->is( $first_insert_id, $second_insert_id, 'Updated an identical artist entry.');
$third_insert_id = $artist_table->addArtist( 'Gorillaz' );
$t->like( $third_insert_id, '/\d+/', 'Successfully another artist entry.' );
$fourth_insert_id = $artist_table->addArtist( 'Gorillaz' );
$t->is( $third_insert_id, $fourth_insert_id, 'Updated an identical artist entry for second artist entry.');

$t->comment( '->getList' );
$list = $artist_table->getList( 'all' );
$count = count( $list );
$t->is( $count, 2, 'Successfully listed all artists' );
$list2 = $artist_table->getList( 'S' );
$count2 = count( $list2 );
$t->is( $list2[0]['name'], 'Sigur Rós', 'Successfully selected Letter S in alpha grouping' );
$t->is( $count2, 1, 'List narrowed to 1 result by alpha group');
$list2 = $artist_table->getList( 's' );
$count2 = count( $list2 );
$t->is( $list2[0]['name'], 'Sigur Rós', 'Successfully selected Letter S in alpha grouping' );
$t->is( $count2, 1, 'Alpha char is case insensitive');

$t->comment( '->finalizeScan' );
$t->is( $artist_table->finalizeScan(), 2, 'finalized scan successfully' );