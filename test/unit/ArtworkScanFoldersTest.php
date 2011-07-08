<?php
ob_start();
include( dirname(__FILE__) . '/../bootstrap/doctrine.php' );

// Initialize the test object
$t = new lime_test( 13, new lime_output_color() );

//add the required fixtures
Doctrine::loadData(sfConfig::get( 'sf_test_dir').'/fixtures/90_ArtworkScanMeta/table.yml' );

require_once( dirname( __FILE__ ) . '/../../lib/task/scanners/artworkScanFolders.php' );

$artwork_base_dir = dirname(__FILE__).'/../../data/album_art';
$message = ob_get_contents();
ob_end_clean();

/* Verify Files have been created for fixture mp3 */
if(is_readable($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95'))
{
  $t->pass('created hashed directory for fixture song');
}
else
{
  $t->fail('couldn\'t create the album art directory for fixture song');
}

/* Verify Files have been created for fixture mp3 */
if(is_readable($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/small.jpg'))
{
  $t->pass('created small thumbnail');
}
else
{
  $t->fail('couldn\'t create small thumbnail');
}

/* Verify Files have been created for fixture mp3 */
if(is_readable($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/medium.jpg'))
{
  $t->pass('created medium thumbnail');
}
else
{
  $t->fail('couldn\'t create medium thumbnail');
}

/* Verify Files have been created for fixture mp3 */
if(is_readable($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/large.jpg'))
{
  $t->pass('created large thumbnail');
}
else
{
  $t->fail('couldn\'t create large thumbnail');
}
$t->ok(strpos( $message, 'Total Albums: 1' ),'total albums count message correct');
$t->ok(strpos( $message, 'Total Albums with Art: 1 (100%)' ), 'total albums with art count correct');
$t->ok(strpos( $message, 'Artwork Unavailable this Scan: 0' ), 'total unavailable count correct');
$t->ok(strpos( $message, 'Artwork Added this Scan: 1' ), 'total added artwork count correct');

//clean up files for next run
unlink($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/small.jpg');
unlink($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/medium.jpg');
unlink($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95/large.jpg');
rmdir($artwork_base_dir.'/9c33cc0dca6a07eaaaa0dc0fd23afc95');

$q = AlbumTable::getInstance()->find('1');
$t->is($q->amazon_flagged, false, "correct value for amazon flag");
$t->is($q->folders_flagged, true, "correct value for folders flag");
$t->is($q->meta_flagged, false, "correct value for meta flag");
$t->is($q->scan_id, 1, 'correct scan id');
$t->is($q->has_art, true, 'item now has art');