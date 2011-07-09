<?php
/**
 * artworkScanMeta
 *
 * Read album art from MP3 id3v2 tags
 *
 * @package    streeme
 * @author     Richard Hoar
 */
require_once( dirname(__FILE__) . '/../../vendor/getid3-1.9.0/getid3/getid3.php' );

$artwork_scanner        = new ArtworkScan( 'meta' );
$id3_scanner            = new getID3();
$id3_scanner->encoding  = 'UTF-8';
$temp_dir               = dirname( __FILE__ ) . '/../../../temp';
$filetypes              = array(
                                  'image/jpeg'  => '.jpg',
                                  'image/jpg'   => '.jpg',
                                  'image/pjpeg' => '.jpg',
                                  'JPG'         => '.jpg',
                                  'image/gif'   => '.gif',
                                  'image/png'   => '.png',
                                  'PNG'         => '.png',
                                );
$artwork_list           = $artwork_scanner->get_unscanned_artwork_list();
$current_album_id       = 0;

if ( !$artwork_list )
{
  echo( "*** All song metas have been scanned for art ***" );
  $artwork_list = array();
}

foreach( $artwork_list as $key => $value )
{
  //this album has copied art, skip to the next album
  if ( $current_album_id == $value[ 'album_id' ] ) continue;
  
  echo 'Scanning: ' .  $value[ 'album_name' ] . ' by: ' .  $value[ 'artist_name' ] . "\r\n";
  
  //setup paths
  $art_dir = dirname( __FILE__ ) . '/../../../data/album_art/' . md5( $value[ 'artist_name' ] . $value[ 'album_name' ] );
  
  //get the metadata from the MP3 file
  $result = $id3_scanner->analyze( iconv( 'UTF-8', sprintf('%s//TRANSLIT', sfConfig::get( app_filesystem_encoding, 'ISO-8859-1' )), $value['song_filename'] ) );
  
  //grab the pic from the id3v2 header -- seems id3v2 is the only real way for MP3
  if ( isset( $result[ 'id3v2' ][ 'APIC' ][0][ 'data' ] ) && strlen($result[ 'id3v2' ][ 'APIC' ][0][ 'data' ]) > 10 )
  {
    $temp_filename = 'temp' . $filetypes[ $result[ 'id3v2' ][ 'APIC' ][0][ 'image_mime' ] ];
    $temp_data = $result[ 'id3v2' ][ 'APIC' ][0][ 'data' ];
  }
  elseif( isset( $result[ 'id3v2' ][ 'PIC' ][0][ 'data' ] ) && strlen( $result[ 'id3v2' ][ 'PIC' ][0][ 'data' ] ) > 10 )
  {
    $temp_filename = 'temp' . $filetypes[ $result[ 'id3v2' ][ 'PIC' ][0][ 'image_mime' ] ];
    $temp_data = $result[ 'id3v2' ][ 'PIC' ][0][ 'data' ];
  }
  elseif( isset( $result['comments']['picture'][0]['data'] ) && strlen( $result['comments']['picture'][0]['data'] ) > 10 )
  {
    $temp_filename = 'temp' . $filetypes[ $result[ 'comments' ][ 'picture' ][ 0 ][ 'image_mime' ] ];
    $temp_data =  $result[ 'comments' ][ 'picture' ][ 0 ][ 'data' ];
  }
  else
  {
    //the first song doesn't have art, go to the next and exhaust all options
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
    continue;
  }

  if ( !strpos( $temp_filename, '.' ) )
  {
    //invalid file type encountered
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
    continue;
  }
  file_put_contents( $temp_dir . '/' . $temp_filename, $temp_data );
  if ( is_readable( $temp_dir . '/' . $temp_filename ) )
  {
    $original = generate_thumbnail( $temp_dir, $temp_filename, 'x', 600, $value );
    $medium = generate_thumbnail( $temp_dir, $temp_filename, 'x', 300, $value );
    $small = generate_thumbnail( $temp_dir, $temp_filename, 'x', 110, $value );
    
    if ( @mkdir( $art_dir, 0777, true ) )
    {
      //copy new art to the album art list
      copy( $temp_dir . '/' . $original, $art_dir . '/' . 'large.jpg' );
      copy( $temp_dir . '/' . $medium, $art_dir . '/' . 'medium.jpg' );
      copy( $temp_dir . '/' . $small, $art_dir . '/' . 'small.jpg' );
      
      unlink( $temp_dir . '/' . $temp_filename );
      
      //don't scan further files in this album
      $current_album_id = $value[ 'album_id' ];
      
      //it's scanned now
      $artwork_scanner->flag_as_added( $value[ 'album_id' ] );
    }
    else
    {
      //if the dir's already there, chances are it has art
      $current_album_id = $value[ 'album_id' ];
      $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
    }
  }
  else
  {
    //temp file isn't readable, probably corrupt, skip to next album and conclude scans
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
  }
}

//summarize the results of the scan
echo "\r\n";
echo $artwork_scanner->get_summary();

/**
* generates a constrained image and returns the data stream
* Good for making thumbnails or just constraining all  uploaded images.
*
* @param path       str: path to temp image (eg. '/home/user/web/temp')
* @param tmp_file   str: the original filename( eg. foo.jpg )
* @param constrain  str: x or y constrain (eg. 'x')
* @param size       int: constrained dimension size (eg. '200')
* @param value      arr: array containing the artist and album name for each loop
* @return           str: the JPEG filename from GD
*/
function generate_thumbnail( $path, $tmp_file, $constrain, $size, $value )
{
  $rights = 0755;
  
  //get the source image size
  if ( $imagesize = getimagesize( $path . '/' . $tmp_file ) )
  {
    //figure out the scaling ratio
    switch( $constrain )
    {
      case 'x':
        $ratio = $size/$imagesize[0];
        break;
      
      case 'y':
        $ratio=$size/$imagesize[1];
        break;
    }
    
    //read source format
    switch( substr( $tmp_file, -3 ) )
    {
      case 'jpg':
        $source = imagecreatefromjpeg( $path . '/' . $tmp_file );
        break;
      case 'gif':
        $source = imagecreatefromgif( $path . '/' . $tmp_file );
        break;
      case 'png':
        $source = imagecreatefrompng( $path . '/' . $tmp_file );
        break;
    }
    
    //open new resource file
    if ( $source )
    {
      //resample the image using the ratio
      $th_xdim=(int) floor($ratio*$imagesize[0]);
      $th_ydim=(int) floor($ratio*$imagesize[1]);
      $tempdest = imagecreatetruecolor($th_xdim, $th_ydim);
    
      //make a copy of the thumbnail image in server memory
      imagecopyresampled( $tempdest, $source, 0, 0, 0, 0, $th_xdim, $th_ydim, imagesx( $source ), imagesy( $source ) );
    
      //we're done with the source, so we'll purge it
      imagedestroy( $source );
    
      //copy the proper JPEG source to the server and chmod it to 644
      imageJPEG( $tempdest, $path . '/' . $size . '-' . $tmp_file );
      chmod( $path . '/' . $size . '-' . $tmp_file, $rights );
    
      //finally, clean up the rest of image memory
      imagedestroy($tempdest);
      
      //return the new filename for moving
      return( $size . '-' . $tmp_file );
    }
    else
    {
      echo 'could not load source image into GD for:' . $value[ 'artist_name' ] . '/' . $value[ 'album_name' ];
    }
  }
  else
  {
    echo 'GD could not get the image dimensions for the media: ' . $value[ 'artist_name' ] . '/' . $value[ 'album_name' ];
  }
}
?>