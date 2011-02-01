<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 1, new lime_output_color() );

$scan_table = Doctrine_Core::getTable('Scan');

$t->comment( '->addScan' );
$first_insert_id = $scan_table->addScan( 'library' );
$t->like( $first_insert_id, '/\d+/', 'Successfully added a new scan.' );