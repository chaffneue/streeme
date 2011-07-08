<?php
include( dirname(__FILE__) . '/../bootstrap/unit.php' );
include( dirname(__FILE__) . '/../../apps/client/lib/StreemeItunesPlaylistParser.class.php' );

// Initialize the test object
$t = new lime_test( 7, new lime_output_color() );

$t->comment( '->construct()' );
try
{
  $missing_file = new StreemeItunesPlaylistParser( dirname(__FILE__) . '/../files/nonexistent xml file.xml' );
  $t->fail('This should halt execution until the user fixes the file');
}
catch( Exception $e )
{
  if( $e->getMessage() === 'Could not open iTunes File' )
    $t->pass( 'File Does not Exist Exception thrown properly' );
  else
    $t->fail( 'Unexpected exception thrown...' );
}

$parser = new StreemeItunesPlaylistParser(  dirname(__FILE__) . '/../files/iTunes Music Library.xml');
$playlist_name = $itunes_playlist_id = null;
$playlist_songs = array();
$parser->getPlaylist($playlist_name, $itunes_playlist_id, $playlist_songs);
$t->is($playlist_name, 'Library', 'Correct name for first library entry');
$t->is($itunes_playlist_id, 'E913B5CC1E293488', 'Correct playlist permanent id' );
$t->is($playlist_songs, array(
  0 => array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/The%20King%20Of%20Limbs/01%20Bloom.MP3'),
  1 => array('filename'=>'file://localhost/E:/music/03%20Hoppipolla.mp3'),
  2 => array('filename'=>'file://localhost/E:/music/1Xtra%20D&B%20Show%20%5B2010-08-04%5D%20%E2%80%93%20Bailey%20&%20DJ%20Fresh/1Xtra%20D&B%20Show%20%5B2010-08-04%5D%20%E2%80%93%20Bailey%20&%20DJ%20Fresh.mp3')
), 'playlist songs are correct');
$playlist_name = $itunes_playlist_id = null;
$playlist_songs = array();
$parser->getPlaylist($playlist_name, $itunes_playlist_id, $playlist_songs);
$t->is($playlist_name, '90\'s Rock', 'Correct name for first library entry');
$t->is($itunes_playlist_id, 'B16E9C5DFFC4695D', 'Correct playlist permanent id' );
$t->is($playlist_songs, array(
  0 => array('filename'=>'file://localhost/E:/music/TheKingOfLimbs-MP3/The%20King%20Of%20Limbs/01%20Bloom.MP3'),
  1 => array('filename'=>'file://localhost/E:/music/03%20Hoppipolla.mp3'),
), 'playlist songs are correct');

$parser->free();