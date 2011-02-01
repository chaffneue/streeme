<?php
/**
 * Send a File from the filesystem to the end user. This class will 
 *
 * @package    streeme
 * @subpackage StreemeSendFile
 * @author     Richard Hoar
 */
class StreemeSendFile
{
  public $filename;
  public $filetype;
  public $basename; 
  public $buffersize = 32000;
  
  /**
   * Constructor
   * @param settings array: vales to pass in for settings see class var list
   */
  public function construct( $settings )
  {
    foreach( $settings as $name => $value )
    {
      $this->$name = $value;
    }
  }
  
  /**
   * Set the filename
   * @param filename str: full path to file
   */
  public function setFilename( $filename )
  {
    $this->filename = $filename;
  }
  
  /**
   * Set the filetype 
   * @param filetype str: The content type for this download
   */
  public function setFiletype( $filetype )
  {
    $this->filetype = $filetype; 
  }
  
  /
  public function setBasename( $basename )
  {
    $this->basename = $basename;
  }
  
  public function setBuffersize( $buffersize )
  {
    $this->buffersize = $buffersize;  
  }
  
  public function send()
  {    
    $params = array(
      'File'                => $this->filename,
      'ContentType'         => $this->filetype,
      'BufferSize'          => $this->buffersize,
      'ContentDisposition'  => array( HTTP_DOWNLOAD_INLINE, $this->basename ),
    );
    
    $error = HTTP_Download::staticSend( $params, false );
  }
}