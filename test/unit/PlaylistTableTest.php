<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 17, new lime_output_color() );

$playlist_table = Doctrine_Core::getTable('Playlist');
$playlist_files_table = Doctrine_Core::getTable('PlaylistFiles');
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/70_PlaylistTable');

if(Doctrine_Manager::getInstance()->getCurrentConnection()->getDriverName() === 'Pgsql')
{
    $dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    $query = 'SELECT setval(\'playlist_id_seq\', 4)';
    $dbh->query( $query );
}

$t->comment( '->addPlaylist' );
$t->comment('Adding a streeme Playlist');
$first_insert_id = $playlist_table->addPlaylist('A Playlist');
$t->is( $first_insert_id, 5, 'Successfully added a playlist entry.' );
$t->comment('Adding a playlist from a service like itunes');
$result = $playlist_table->find(5);
$t->is($result->name, 'A Playlist', 'Correct Name');
$t->is($result->scan_id, 0, 'Correct Scan ID');
$t->is($result->service_name, null, 'Correct Service Name');
$t->is($result->service_unique_id, null, 'correct service id' );

$second_insert_id  = $playlist_table->addPlaylist('Nineties Rock', 2, 'itunes', 'B16E9C5DFFC4695D');
$t->is( $second_insert_id, 6, 'Successfully added a playlist entry.' );
$result = $playlist_table->find(6);
$t->is($result->name, 'Nineties Rock', 'Correct Name');
$t->is($result->scan_id, 2, 'Correct Scan ID');
$t->is($result->service_name, 'itunes', 'Correct Service Name');
$t->is($result->service_unique_id, 'B16E9C5DFFC4695D', 'correct service id' );

$t->comment( '->deletePlaylist' );
$deleted_row_count = $playlist_table->deletePlaylist( PlaylistFilesTable::getInstance(), $first_insert_id );
$t->is( $deleted_row_count, 1, 'Successfully deleted a playlist entry' );

$t->comment( '->getList' );
$list  = $playlist_table->getList();
$count = count( $list );
$t->is( $count, 3, 'Correct list size' );

$t->comment( '->updateScanId' );
$playlist_table->updateScanId('itunes', 'Itunes 90\'s Playlist', 'B16E9C5DFFC4695D', 2);
$updated_record = $playlist_table->find(2);
$t->is($updated_record->scan_id, 2, 'Record updated to correct scan id');
$id = $playlist_table->updateScanId('itunes', 'Itunes Don\'t Exist', 'AC29CC9100DF56F', 2);
$t->is(id, 0, 'Correct Id for missing playlist');
$playlist_table->updateScanId('wjukebox', 'WJukebox Retro Playlist', null, 3);
$updated_record = $playlist_table->find(4);
$t->is($updated_record->scan_id, 3, 'Record updated to correct scan id');

$t->comment( '->finalizeScan' );
$removed_count = $playlist_table->finalizeScan($playlist_files_table, 3, 'itunes');
$t->is($removed_count, 2, 'successfully cleaned old playlist entries');
$q = $playlist_files_table->createQuery();
$result = $q->execute();
$count = count($result);
$t->is($count, 1, "successfully removed playlist files");