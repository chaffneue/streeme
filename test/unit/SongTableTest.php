<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 24, new lime_output_color() );

$song_table = Doctrine_Core::getTable('Song');

//Test Data Sets
//Valid Song with all data present
$valid_test_song = array(
                          'last_scan_id' => 1, //int: scan primary key
                          'artist_name' => 'Gorillaz', //string
                          'album_name' => 'Gorillaz Compilation', //string
                          'genre_name' => 'Electronic', //string
                          'song_name' => 'Clint Eastwood', //string
                          'song_length' => '2:05', //min:sec
                          'accurate_length' => '125000', //milliseconds
                          'filesize' => 3000024, //int: bytes
                          'bitrate' => 128, //int: bitrate in estimated kilobits CBR
                          'yearpublished' => 2010, //int: 4 digit  calendar year
                          'tracknumber' => 7, //int: the track number as it appears on disc eg.1
                          'label' => 'EMI', //str: the name of the label the album is on
                          'mtime' => 1293300000, //int:unix time
                          'atime' => 1293300011, //int:unix time
                          'filename' => 'file://localhost/home/notroot/music/test.mp3', //txt: protocol file style
                        );
                        
//Simulate an updated id3 tag
$valid_test_song_updated_tag = array(
                          'last_scan_id' => 2, //int: scan primary key
                          'artist_name' => 'Gorillas', //string
                          'album_name' => 'You Love Electro', //string
                          'genre_name' => 'Easy Listening', //string
                          'song_name' => 'Clint Eastwood (Mellow Sax Edition)', //string
                          'song_length' => '2:06', //min:sec
                          'accurate_length' => '126000', //milliseconds
                          'filesize' => 6000027, //int: bytes
                          'bitrate' => 192, //int: bitrate in estimated kilobits CBR
                          'yearpublished' => 2011, //int: 4 digit  calendar year
                          'tracknumber' => 10, //int: the track number as it appears on disc eg.1
                          'label' => 'Warner', //str: the name of the label the album is on
                          'mtime' => 1299800000, //int:unix time
                          'atime' => 1299800011, //int:unix time
                          'filename' => 'file://localhost/home/notroot/music/test.mp3', //txt: protocol file style
                        );
//Simulate a malformed song record - missing/empty data
$empty_test_song = array(
                          'last_scan_id' => null, //int: scan primary key
                          'artist_name' => '', //string
                          'album_name' => '', //string
                          'genre_name' => '', //string
                          'song_name' => '', //string
                          'song_length' => '', //min:sec
                          'accurate_length' => '', //milliseconds
                          'filesize' => null, //int: bytes
                          'bitrate' => null, //int: bitrate in estimated kilobits CBR
                          'yearpublished' => null, //int: 4 digit  calendar year
                          'tracknumber' => null, //int: the track number as it appears on disc eg.1
                          'label' => '', //str: the name of the label the album is on
                          'mtime' => null, //int:unix time
                          'atime' => null, //int:unix time
                          'filename' => '', //txt: protocol file style
                         );

//simulate a UTF-8 entry
$utf8_test_song = array(
                          'last_scan_id' => 1, //int: scan primary key
                          'artist_name' => 'Sigur Rós', //string
                          'album_name' => 'með suð í eyrum við spilum endalaust', //string
                          'genre_name' => 'Русский', //string
                          'song_name' => 'dót widget', //string
                          'song_length' => '3:05', //min:sec
                          'accurate_length' => '185000', //milliseconds
                          'filesize' => 3002332, //int: bytes
                          'bitrate' => 128, //int: bitrate in estimated kilobits CBR
                          'yearpublished' => 2005, //int: 4 digit  calendar year
                          'tracknumber' => 1, //int: the track number as it appears on disc eg.1
                          'label' => 'ンスの映像を世界に先がけて', //str: the name of the label the album is on
                          'mtime' => 1293300023, //int:unix time
                          'atime' => 1293300011, //int:unix time
                          'filename' => 'file://localhost/home/notroot/music/Fließgewässer.mp3', //txt: protocol file style
                        );

//tests
$t->comment( '->addSong' );
$first_insert_id = $song_table->addSong( 1, 1, 1, 1, $valid_test_song );
$t->like( $first_insert_id, '/\d+/', 'Successfully added a song entry.' );
$second_insert_id = $song_table->addSong( 1, 1, 1, 2, $valid_test_song_updated_tag );
$t->like( $second_insert_id, '/\d+/', 'Successfully updated a song entry with new scan id.' );
$third_insert_id = $song_table->addSong( null, null, null, null, $empty_test_song );
$t->is( $third_insert_id ,null, 'Ignore Entry of an incomplete song record');
$fourth_insert_id = $song_table->addSong( 2, 2, 1, 1, $utf8_test_song );
$t->like( $fourth_insert_id, '/\d+/', 'Successfully added a UTF-8 encoded entry' );

$t->comment( '->findByFilenameAndMtime' );
$song = $song_table->findByFilenameAndMtime( 'file://localhost/home/notroot/music/test.mp3', '1299800000' );
$t->is( $song->id, 2, 'Fetch Record by filename and mtime');

$t->comment( '->getSongByUniqueId');
$unique_song = $song_table->getSongByUniqueId( $song->unique_id );
$t->is( $unique_song->id, 2, 'Fetch record by unique id');

$t->comment( '->updateScanId()');
$affected_rows = $song_table->updateScanId( 'file://localhost/home/notroot/music/Fließgewässer.mp3', 1293300023, 2 );
$t->is( $affected_rows, 1, 'Updated a song with a scan id');

$t->comment( '->getUnscannedArtList' );
//add the required fixtures
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures/10_SongTable');
$list = $song_table->getUnscannedArtList( 'amazon' );
$count1 = count( $list );
$t->is( $count1, 2, 'Got a list of unscanned art' );

$t->comment( '->getFileList' );
$result = $song_table->getFileList( '1', 'artist' );
$count2 = count( $result );
$t->is( $count2, 2, 'Got a file listing by artist');
$result = $song_table->getFileList( '2', 'album' );
$count3 = count( $result );
$t->is( $count3, 1, 'Got a file listing by album');
$result = $song_table->getFileList( $song->unique_id, 'song' );
$count4 = count( $result );
$t->is( $count4, 1, 'Got a file listing by song unique_id');

$t->comment( '->getTotalSongCount' );
$t->is( $song_table->getTotalSongCount(), 3, 'correct total song count' );

$t->comment( '->getList' );
$result_count = $result_list = null;
$result = $song_table->getList( array(), $result_count, $result_list );
$t->is( $result_count, 3, 'List length is correct for default settings' );
$result_count = $result_list = $array_count = null;
$result = $song_table->getList( array( 'limit' => 2 ), $result_count, $result_list );
$array_count = count( $result_list );
$t->is( $result_count, 3, 'List length is correct for a limited result set' );
$t->is( $array_count, 2, 'Results list is the correct length for a limited set' );
$result_count = $result_list = $array_count = null;
$result = $song_table->getList( array( 'offset' => 1 ), $result_count, $result_list );
$array_count = count( $result_list );
$t->is( $result_count, 3, 'List length is correct for an offset result set' );
$t->is( $array_count, 2, 'Results list is the correct length for an offset set' );
$result_count = $result_list = null;
$result = $song_table->getList( array( 'search' => 'dót' ), $result_count, $result_list );
$t->is( $result_count, 1, 'List length is correct for a search result set' );
$t->is( $result_list[0]['name'], 'dót widget', 'Keyword search narrowed results correctly' );
$result_count = $result_list = null;
$result = $song_table->getList( array( 'artist_id' => '1' ), $result_count, $result_list );
$t->is( $result_list[0]['artist_name'], 'Gorillas', 'Narrowed list by artist id' );
$result_count = $result_list = null;
$result = $song_table->getList( array( 'album_id' => '2' ), $result_count, $result_list );
$t->is( $result_list[0]['album_name'], 'með suð í eyrum við spilum endalaust', 'Narrowed list by album id' );
$result_count = $result_list = null;
$result = $song_table->getList( array( 'genre_id' => '1' ), $result_count, $result_list );
$t->is( $result_count, 3, 'Narrowed list by genre id' );
$result_count = $result_list = null;
$result = $song_table->getList( array( 'playlist_id' => '1' ), $result_count, $result_list );
$t->is( $result_count, 0, 'Narrowed list by playlist id' );

$t->comment( '->finalizeScan' );
$t->is( $song_table->finalizeScan( 1 ), 2, 'finalized scan successfully' );