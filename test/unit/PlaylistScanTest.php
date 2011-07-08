<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 26, new lime_output_color() );

Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/80_PlaylistScan');
$playlist_scan = new PlaylistScan('itunes');
$playlist = PlaylistTable::getInstance();
$playlist_files = PlaylistFilesTable::getInstance();
$itunes_parser = new StreemeItunesPlaylistParser(  dirname(__FILE__) . '/../files/iTunes Music Library.xml' );

$t->comment('->construct()');
$playlist_scan = new PlaylistScan('itunes');

$t->comment('->get_last_scan_id()');
$t->is($playlist_scan->get_last_scan_id(), 2, 'Got valid playlist scan id');

$t->comment('->get_service_name()');
$t->is($playlist_scan->get_service_name(), 'itunes', 'got valid source name');

$t->comment('->is_scanned()');
$playlist_id = $playlist_scan->is_scanned( $playlist_scan->get_service_name(), '90\'s Rock', 'B16E9C5DFFC4695D');
$t->is($playlist_id, '1', 'Targeted the correct playlist');
$t->is($playlist_scan->get_total_playlists(), 1, 'Playlist count incremented');

$t->comment('->add_playlist()');
$playlist_scan = new PlaylistScan('itunes');
$t->comment('Adding New...');
$new_stuff_files = array(array('filename'=>'file://localhost/home/music/new1.mp3'));
$playlist_id = $playlist_scan->add_playlist('New Stuff', $new_stuff_files, 0, $playlist_scan->get_last_scan_id(), 'itunes', 'AED0293002lECFFC');
$t->is($playlist_scan->get_added_playlists(), '1', 'Added Playlist');
$result = $playlist->find($playlist_id);
$t->is($result->name, 'New Stuff', 'Correct playlist name added');
$t->is($result->service_name, 'itunes', 'Correct service name added');
$t->is($result->service_unique_id, 'AED0293002lECFFC', 'Correct scan id added');
$t->is($result->scan_id, 3, 'Correct scan id added');

$t->comment('Updating Existing Playlist with new playlist name and an extra file');
$rock_files = array(
            array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/The%20King%20Of%20Limbs/01%20Bloom.MP3'),
            array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/some%20rock.MP3')
            );
$update_playlist_id = $playlist_scan->is_scanned( $playlist_scan->get_service_name(), 'Nineties Rock', 'B16E9C5DFFC4695D');
$playlist_id = $playlist_scan->add_playlist('Nineties Rock', $rock_files, $update_playlist_id, $playlist_scan->get_last_scan_id(), 'itunes', 'B16E9C5DFFC4695D');
$t->is($playlist_scan->get_added_playlists(), '2', 'Added Updated Name Playlist');
$t->is($playlist_id,5,'added new playlist entry');
$result = $playlist->find($playlist_id);
$t->is($result->name, 'Nineties Rock', 'Updated the title');
$t->is($result->scan_id, 3, 'expected scan id');
$result = $playlist_files->createQuery()->where('playlist_id=?',$result->id)->fetchArray();
$t->is(count($result), '2', 'Playlist files updated');

$t->comment('Updating Existing Playlist with an extra song');
$rock_files = array(
            array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/The%20King%20Of%20Limbs/01%20Bloom.MP3'),
            array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/some%20rock.MP3')
            );
$update_playlist_id = $playlist_scan->is_scanned( $playlist_scan->get_service_name(), '90\'s Rock', 'B16E9C5DFFC4695D');
$playlist_id = $playlist_scan->add_playlist('90\'s Rock', $rock_files, $update_playlist_id, $playlist_scan->get_last_scan_id(), 'itunes', 'B16E9C5DFFC4695D');
$t->is($playlist_scan->get_updated_playlists(), '1', 'Updated 90\'s Rock Playlist');
$t->is($playlist_id,1,'updated playlist entry 1');
$result = $playlist->find($playlist_id);
$t->is($result->name, '90\'s Rock', 'Targeted correct title');
$t->is($result->scan_id, 3, 'expected scan id');
$result = $playlist_files->createQuery()->where('playlist_id=?',$result->id)->fetchArray();
$t->is(count($result), '2', 'Playlist files updated');

$t->comment('Find reasons to skip scanning');
$playlist_id = $playlist_scan->add_playlist('', $rock_files, $update_playlist_id, $playlist_scan->get_last_scan_id(), 'itunes', 'B16E9C5DFFC4695D');
$t->is($playlist_scan->get_skipped_playlists(),1,'Skipped due to empty filename');
$playlist_id = $playlist_scan->add_playlist('Soft Jazz Mix', array(), $update_playlist_id, $playlist_scan->get_last_scan_id(), 'itunes', 'B16E9C5DFFC4695D');
$t->is($playlist_scan->get_skipped_playlists(),2,'Skipped due to empty file set');
$playlist_id = $playlist_scan->add_playlist('Soft Jazz Mix', '', $update_playlist_id, $playlist_scan->get_last_scan_id(), 'itunes', 'B16E9C5DFFC4695D');
$t->is($playlist_scan->get_skipped_playlists(),3,'Skipped due to wrong file set type');

$t->comment('->finalize_scan()');
$result = $playlist_files->createQuery()->where('playlist_id=?',3)->fetchArray();
$t->is(count($result), 1, 'files still exist for playlist 3');
$count = $playlist_scan->finalize_scan($playlist_files);
$t->is($count, 1, 'Removed out of date record 3');
$result = $playlist_files->createQuery()->where('playlist_id=?',3)->fetchArray();
$t->is(count($result), 0, 'Playlist files removed');

$t->comment( '->get_summary()' );
$t->ok( $playlist_scan->get_summary(), 'got summary detail string' );