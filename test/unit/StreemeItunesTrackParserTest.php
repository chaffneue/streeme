<?php
include( dirname(__FILE__) . '/../bootstrap/unit.php' );
include( dirname(__FILE__) . '/../../apps/client/lib/StreemeItunesTrackParser.class.php' );

// Initialize the test object
$t = new lime_test( 5, new lime_output_color() );

$t->comment( '->construct()' );
try
{
  $missing_file = new StreemeItunesTrackParser( dirname(__FILE__) . '/../files/nonexistent xml file.xml' );
  $t->fail('This should halt execution until the user fixes the file');
}
catch( Exception $e )
{
  if( $e->getMessage() === 'Could not open iTunes File' )
    $t->pass( 'File Does not Exist Exception thrown properly' );
  else
    $t->fail( 'Unexpected exception thrown...' );
}
try
{
  $broken_file = new StreemeItunesTrackParser( dirname(__FILE__) . '/../files/broken_itunes_file.xml' );
  $broken_file->getTrack();
  $t->fail('This file should not parse');
}
catch( Exception $e )
{
  if( $e->getMessage() == 'XML error: Mismatched tag at line 41' )
    $t->pass( 'Broken File Exception thrown properly' );
  else
    $t->fail( sprintf( 'Unexpected exception thrown: %s', $e->getMessage() ) );
}

$parser = new StreemeItunesTrackParser( dirname(__FILE__) . '/../files/iTunes Music Library.xml' );


$t->comment( '->getTrack()' );
$i = 0;
$rows = array();
while( $row = $parser->getTrack() )
{
  $rows[ $i ] = $row;
  $i++;
}
$expected_row_1 =  array (
                        'Track ID' => '3693',
                        'Name' => 'Bloom',
                        'Artist' => 'Radiohead',
                        'Album Artist' => 'Radiohead',
                        'Album' => 'The King Of Limbs',
                        'Genre' => 'Alternative',
                        'Kind' => 'MPEG audio file',
                        'Size' => '12755634',
                        'Total Time' => '314723',
                        'Disc Number' => '1',
                        'Disc Count' => '1',
                        'Track Number' => '1',
                        'Track Count' => '8',
                        'Year' => '2011',
                        'Date Modified' => '2011-02-17T01:08:14Z',
                        'Date Added' => '2011-02-19T07:47:05Z',
                        'Bit Rate' => '320',
                        'Sample Rate' => '44100',
                        'Play Count' => '2',
                        'Play Date' => '3380978253',
                        'Play Date UTC' => '2011-02-20T00:37:33Z',
                        'Artwork Count' => '1',
                        'Sort Album' => 'King Of Limbs',
                        'Persistent ID' => 'A880CAD83B51BB06',
                        'Track Type' => 'File',
                        'Location' => 'file://localhost/E:/music/TheKingOfLimbs-MP3/The%20King%20Of%20Limbs/01%20Bloom.MP3',
                        'File Folder Count' => '-1',
                        'Library Folder Count' => '-1'
                      );
$expected_row_2 =   array (
                        'Track ID' => '3861',
                        'Name' => 'Hoppípolla',
                        'Artist' => 'Sigur Rós',
                        'Album Artist' => 'Sigur Rós',
                        'Album' => 'Takk',
                        'Genre' => 'Alternative & Punk',
                        'Kind' => 'MPEG audio file',
                        'Size' => '6454134',
                        'Total Time' => '268826',
                        'Track Number' => '3',
                        'Year' => '2005',
                        'Date Modified' => '2007-06-29T07:44:38Z',
                        'Date Added' => '2011-02-19T08:08:00Z',
                        'Bit Rate' => '192',
                        'Sample Rate' => '44100',
                        'Comments' => 'A comment about Hoppípolla',
                        'Persistent ID' => '9DD9B1B615770FDB',
                        'Track Type' => 'File',
                        'Location' => 'file://localhost/E:/music/03%20Hoppipolla.mp3',
                        'File Folder Count' => '-1',
                        'Library Folder Count' => '-1'
                      );
$expected_row_3 =   array (
                        'Track ID' => '4297',
                        'Name' => '1Xtra D&B Show [2010-08-04] – Bailey & DJ Fresh',
                        'Artist' => 'Bailey & DJ Fresh',
                        'Album Artist' => 'Bailey & DJ Fresh',
                        'Album' => '1Xtra D&B Show [2010-08-04] – Bailey & DJ Fresh',
                        'Kind' => 'MPEG audio file',
                        'Size' => '259204386',
                        'Total Time' => '10800120',
                        'Year' => '2010',
                        'Date Modified' => '2010-08-23T06:30:06Z',
                        'Date Added' => '2011-02-19T08:08:03Z',
                        'Bit Rate' => '192',
                        'Sample Rate' => '48000',
                        'Persistent ID' => '7452804205FD1913',
                        'Track Type' => 'File',
                        'Location' => 'file://localhost/E:/music/1Xtra%20D&B%20Show%20%5B2010-08-04%5D%20%E2%80%93%20Bailey%20&%20DJ%20Fresh/1Xtra%20D&B%20Show%20%5B2010-08-04%5D%20%E2%80%93%20Bailey%20&%20DJ%20Fresh.mp3',
                        'File Folder Count' => '-1',
                        'Library Folder Count' => '-1'
                      );


$t->is_deeply( $rows[0], $expected_row_1, 'test file and track array are identical - ASCII' );
$t->is_deeply( $rows[1], $expected_row_2, 'test file and track array are identical - UTF8' );
$t->is_deeply( $rows[2], $expected_row_3, 'test file and track array are identical - Ents' );
$parser->free();