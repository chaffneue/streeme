<?php
/**
 * artworkScanFolders
 * 
 * Read album art from the folders that contain the songs
 * 
 * @package    streeme
 * @author     Richard Hoar
 */
error_reporting( 0 );
$artwork_scanner        = new ArtworkScan( 'folders' );
$temp_dir               = dirname( __FILE__ ) . '/../../../temp';
$filetypes              = array(
                                  '.jpg',
                                  'jpeg',
                                  '.gif',
                                  '.png',
                                );
$artwork_list           = $artwork_scanner->get_unscanned_artwork_list();
$current_album_id       = 0;

if ( !$artwork_list )
{
  echo( "*** All song folders have been scanned for art ***" );
  $artwork_list = array();
} 


foreach( $artwork_list as $key => $value )
{
  $shortlist = array();
  $file_to_process = null;
  
  //this album has copied art, skip to the next album
  if ( $current_album_id == $value[ 'album_id' ] ) continue;
  
    echo 'Scanning: ' .  $value[ 'album_name' ] . ' by: ' .  $value[ 'artist_name' ] . "\r\n";
  
  //setup paths 
  $art_dir = dirname( __FILE__ ) . '/../../../data/album_art/' . md5( $value[ 'artist_name' ] . $value[ 'album_name' ] );
	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
	{
	  //Windows
	  $streeme_path_name = 'file://localhost/';
	} 
	else
	{
	  //*nix
	  $streeme_path_name = 'file://localhost';
	}
  $song_path_info = pathinfo( str_replace( $streeme_path_name, '', utf8_decode( urldecode( $value['song_filename'] ) ) ) );
  $scan_dir = $song_path_info[ 'dirname' ];
  
  //skip files in the root path 
  $depth = explode( '/' , $scan_dir );
  if ( count( $depth ) <= 1 ) continue;
    
  //scan for allowed files in this song's folder and put the result in an array 
  $dp = opendir( $scan_dir );
	while( $filename = readdir( $dp ) )
	{
	  //create the full pathname
		$full_file_path = $scan_dir . '/' . $filename;
		
    //skip hidden files/folders
		if ($filename{0} === '.')
		{
			continue;
		}
				
		//is it a valid filetype? add to the shortlist of images
		if( in_array( substr( $filename, -4 ), $filetypes ) )
		{
		  $file_info = stat( $full_file_path );
		  $shortlist[] = array( $file_info[ 'size' ], $filename, $full_file_path ); 
		}
	}
	
	//order by filesize biggest to smallest
	usort( $shortlist, 'compare_filesize' );
	
	//look for term 'folder' or 'cover' in the filename - use first available
	foreach( $shortlist as $candidates )
	{
	  if ( ( stristr( $candidates[ 1 ], 'cover' ) ) || ( stristr( $candidates[ 1 ], 'folder' ) ) )
	  {
	    $file_to_process = $candidates[ 2 ];
	    break;
	  }	
	}	
	
	//did we find a match? Let's make the thumbnails
  if ( isset( $file_to_process ) && !empty( $file_to_process ) )
  {
    $temp_pathinfo = pathinfo( $file_to_process ); 
    $temp_filename = 'temp.' . $temp_pathinfo[ 'extension' ];
    copy( $file_to_process, $temp_dir . '/' . $temp_filename );
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
  else
  {
    //this album's directory directory doesn't have art files
    $current_album_id = $value[ 'album_id' ];
    $artwork_scanner->flag_as_skipped( $value[ 'album_id' ] );
  }
} 

//summarize the results of the scan
echo "\r\n";
echo $artwork_scanner->get_summary();
  
/**
* generates a constrained image and returns the data stream 
*	Good for making thumbnails or just constraining all  uploaded images. 
*
* @param path       str: path to temp image (eg. '/home/user/web/temp')
* @param tmp_file   str: the original filename( eg. foo.jpg )
*	@param constrain  str: x or y constrain (eg. 'x')
*	@param size       int: constrained dimension size (eg. '200')
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
	  switch( substr( $tmp_file, -4 ) )
	  {
	    case '.jpg':
	      $source = imagecreatefromjpeg( $path . '/' . $tmp_file );
	      break;
	    case 'jpeg':
	      $source = imagecreatefromjpeg( $path . '/' . $tmp_file );
	      break;
	    case '.gif':
	      $source = imagecreatefromgif( $path . '/' . $tmp_file );
	      break;
	    case '.png':
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

/**
* Compare file sizes for finding album art
* @seekrit usort voodoo params
* @seekrit usort voodoo return
*/
function compare_filesize($a, $b)
{
  return strnatcmp( $b[0], $a[0] );
}

?>