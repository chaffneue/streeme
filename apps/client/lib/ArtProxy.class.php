<?php
/*
* The proxy for getting album_artwork from streeme. This song exposes image files in the data folder
* usage: $art_proxy = new ArtProxy( 'some_md5_string', 'size' );
*        $art_proxy->getImage();
* @package    streeme
* @subpackage art
* @author     Richard Hoar
*/
error_reporting( 0 ); //HTTP download is extremely noisy
require_once( 'HTTP/Download.php' );

class ArtProxy
{
  protected
    $hash,
    $size,
    $art_dir;
  
  /**
  * Constructor
  * @param hash     str: the identifier for the album art - created by md5( artist_name + album_name )
  * @param size     str: the size to respond with large (500x500) | medium (200x200) | small (100x100)
  * @param art_dir  str: the directory where the album art is stored
  */
  public function __construct( $hash = null, $size = null, $art_dir = null )
  {
    $this->art_dir = $art_dir;
    $this->hash    = $hash;
    $this->size    = strtolower( $size );
  }
  
  /**
  * Get the image
  * @return        str: hash name / image size (useful for tests)
  */
  public function getImage()
  {
    $sizes = array( 'small', 'medium', 'large' );
    
    if ( is_null( $this->size ) || !StreemeUtil::in_array_ci( $this->size, $sizes ) )
    {
      $this->size = 'medium';
    }
     
    $image_download = new HTTP_Download();
    $image_download->setContentType( 'image/jpeg' );
    $image_download->setBufferSize( 8192 );
    $image_download->setContentDisposition( HTTP_DOWNLOAD_INLINE, $this->size . '.jpg' );
    
    //if art exists stream it to the user, otherwise stream the default placeholder graphics
    if ( is_readable( $this->art_dir . '/' . $this->hash . '/' . $this->size . '.jpg' ) === false )
    {
      $image_download->setFile( $this->art_dir . '/placeholder/' . $this->size . '.jpg' );
      $this->hash = 'placeholder';
    }
    else
    {
      $image_download->setFile( $this->art_dir . '/' . $this->hash . '/' . $this->size . '.jpg' );
    }
    
    $this->log( sprintf( 'Sending Artwork: %s - Size: %s - Filename: %s',
                         $this->hash,
                         $this->size,
                         $this->art_dir . '/' . $this->hash . '/' . $this->size . '.jpg'
                       )
              );
    
    $image_download->send();
    
    return $this->hash . '/' . $this->size . '.jpg';
  }

  /**
   * Log Media Proxy Activity
   *
   * @param string  $message
   */
  public function log( $message )
  {
    file_put_contents( dirname(__FILE__) . '/../../../log/proxy.log', date('Y-m-d h:i:s' ) . ' - {StreemeArtProxy} ' . $message . "\r\n", FILE_APPEND);
  }
}
?>