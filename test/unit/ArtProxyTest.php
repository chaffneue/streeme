<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 3, new lime_output_color() );

$art_proxy = new ArtProxy( '13212ABCD', 'medium', dirname(__FILE__) . '/../files/album_art' );
$t->is( $art_proxy->getImage(), 'placeholder/medium.jpg', 'placeholder image showed for broken hash' );
$art_proxy = new ArtProxy( null, null, dirname(__FILE__) . '/../files/album_art' );
$t->is( $art_proxy->getImage(), 'placeholder/medium.jpg', 'placeholder image showed for no hash or size' );
$art_proxy = new ArtProxy( '1BC341AD21124AB11AAC3454', 'large', dirname(__FILE__) . '/../files/album_art' );
$t->is( $art_proxy->getImage(), '1BC341AD21124AB11AAC3454/large.jpg', 'found artwork by unique id' );