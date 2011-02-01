<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 12, new lime_output_color() );

//add the required fixtures
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/40_ArtworkScan');

$artwork_scan = new ArtworkScan( 'meta' );

$t->comment( '->construct() ->get_last_scan_id() ->get_source()');
$t->like( $artwork_scan->get_last_scan_id(), '/\d+/', 'Entered a new scan id successfully.' );
$t->is( $artwork_scan->get_source(), 'meta', 'Source identifier passed' );

$t->comment( '->get_unscanned_artwork_list()' );
$list = $artwork_scan->get_unscanned_artwork_list();
$count = count( $list );
$t->is( $count, 2, 'Got the unscanned album list for ID3 metadata');

$t->comment( '->flag_as_skipped()' );
$t->is( $artwork_scan->flag_as_skipped( 1 ), true, 'flagged album 1 as skipped for meta' );
$list = $artwork_scan->get_unscanned_artwork_list();
$count = count( $list );
$t->is( $count, 1, 'Listing is up to date');

$t->comment( '->flag_as_added()' );
$t->is( $artwork_scan->flag_as_added( 2 ), true, 'flagged album 2 as added for meta' );
$list = $artwork_scan->get_unscanned_artwork_list();
$count = count( $list );
$t->is( $count, 0, 'Listing is up to date');

//test integrity
$album_table = Doctrine_Core::getTable('Album');
$album = $album_table->find( 1 );
$t->is( $album->meta_flagged, 1, 'Album 1 was scanned' );
$t->is( $album->has_art, 0, 'Album 1 does not have art' );
$album = $album_table->find( 2 );
$t->is( $album->meta_flagged, 1, 'Album 2 was scanned' );
$t->is( $album->has_art, 1, 'Album 2 has art' );

$t->comment( '->get_summary()' );
$t->is( is_string( $artwork_scan->get_summary() ), true, 'returned string' );