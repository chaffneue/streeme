<?php
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 24, new lime_output_color() );

$valid_test_song = array(
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

$utf8_test_song = array(
                          'artist_name' => 'Sigur Rós', //string
                          'album_name' => 'með suð í eyrum við spilum endalaust', //string
                          'genre_name' => 'Русский', //string
                          'song_name' => 'dót widget', //string
                          'song_length' => '3:05', //min:sec
                          'accurate_length' => 185000, //int:milliseconds
                          'filesize' => 3002332, //int: bytes
                          'bitrate' => 128, //int: bitrate in estimated kilobits CBR
                          'yearpublished' => 2005, //int: 4 digit  calendar year
                          'tracknumber' => 1, //int: the track number as it appears on disc eg.1
                          'label' => 'ンスの映像を世界に先がけて', //str: the name of the label the album is on
                          'mtime' => 1293300023, //int:unix time
                          'atime' => 1293300011, //int:unix time
                          'filename' => 'file://localhost/home/notroot/music/Fließgewässer.mp3', //txt: protocol file style
                        );

$media_scan = new MediaScan();
$t->comment( '->construct()');
$t->like( $media_scan->get_last_scan_id(), '/\d+/', 'Entered a new scan id successfully.' );

$t->comment( '->is_scanned()');
$t->is( $media_scan->is_scanned( 'file://localhost/home/notroot/music/test.mp3', '1293300000' ), false, 'Song should not exist yet' );
$first_insert_id = $media_scan->add_song( $valid_test_song );
$t->like( $first_insert_id, '/\d+/', 'Successfully added a song to the database' );

$t->comment( '->add_song()' );
$media_scan = new MediaScan();
$second_insert_id = $media_scan->add_song( $utf8_test_song );
$t->like( $second_insert_id, '/\d+/', 'Successfully added a UTF-8 Song entry.' );
$t->is( $media_scan->is_scanned( 'file://localhost/home/notroot/music/test.mp3', '1293300000' ), true, 'Updated old record to new scan id number' );
$media_scan = new MediaScan();
$second_insert_id = $media_scan->add_song( $utf8_test_song );

//Test Data Integrity after add
$song_integrity_test = Doctrine_Core::getTable('Song')->find(2);
$artist_integrity_test = Doctrine_Core::getTable('Artist')->find(2);
$album_integrity_test = Doctrine_Core::getTable('Album')->find(2);
$genre_integrity_test = Doctrine_Core::getTable('Genre')->find(127);
$t->is( $song_integrity_test->id, 2, 'integrity: primary id');
$t->is( $song_integrity_test->scan_id, 2, 'integrity: last_scan_id id');
$t->is( $song_integrity_test->artist_id, 2, 'integrity: artist_id');
$t->is( $artist_integrity_test->name, 'Sigur Rós', 'integrity: artist_name');
$t->is( $song_integrity_test->album_id, 2, 'integrity: album_id');
$t->is( $album_integrity_test->name, 'með suð í eyrum við spilum endalaust', 'integrity: album_name');
$t->is( $genre_integrity_test->name, 'Русский', 'integrity: album_name');
$t->is( $song_integrity_test->length, '3:05', 'integrity: song length ');
$t->is( $song_integrity_test->accurate_length, 185000, 'integrity: song length in milliseconds');
$t->is( $song_integrity_test->filesize, 3002332, 'integrity: file size in bytes ');
$t->is( $song_integrity_test->bitrate, 128, 'integrity: bitrate in kbps');
$t->is( $song_integrity_test->yearpublished, 2005, 'integrity: year published ');
$t->is( $song_integrity_test->tracknumber, 1, 'integrity: track number');
$t->is( $song_integrity_test->label, 'ンスの映像を世界に先がけて', 'integrity: label name ');
$t->is( $song_integrity_test->mtime, 1293300023, 'integrity: date modified unix timestamp');
$t->is( $song_integrity_test->atime, 1293300011, 'integrity: last access unix timestamp');
$t->is( $song_integrity_test->filename, 'file://localhost/home/notroot/music/Fließgewässer.mp3', 'integrity: utf8 filename');

$t->comment( '->finalize_scan()' );
$t->is( $media_scan->finalize_scan(), 4, 'Removed Song and Associations' );

$t->comment( '->get_summary()' );
$t->is( is_string( $media_scan->get_summary() ), true, 'returned summary message successfully' );