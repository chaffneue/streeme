<?php
class StreemeUtil
{
  /**
  * Encode a filesystem name into an iTunes compatible format
  * @param filename str: the input filename to encode
  * @return       str: the iTunes formatted semi-urlencoded file or false
  */
  public static function itunes_format_encode( $filename )
  {
    if ( !isset( $filename ) || empty( $filename ) ) return false;
    
    //explode filename into parts by directory separator
    $file_parts = explode( '/', $filename );
    
    $accumulator = array();
    if( count( $file_parts ) > 0 )
    {
      //urlencode each part
      foreach( $file_parts as $part )
      {
      	//encode windows drive letters a bit differently, like itunes does?
      	if ( strpos( $part, ':' ) )
      	{
      	  $accumulator[] = $part;
      	  continue;
      	}
        $accumulator[] = rawurlencode( $part );
      }
      
      $url_prefix = ( self::is_windows() ) ? 'file://localhost/' : 'file://localhost';
      
      //recombine file into URI and prepend protocol info
      return $url_prefix . join( '/', $accumulator );
    }
    return false;
  }

  /**
  * Decode an iTunes formatted URL string into an OS filesystem name
  * @param itunes_url str: the input iTunes URL to decode
  * @return           str: the OS style filename to pass to php functions or false
  */
  public static function itunes_format_decode( $itunes_url )
  {
    //build the iTunes URL prefix
    $url_prefix = ( self::is_windows() ) ? 'file://localhost/' : 'file://localhost';
      
    //strip the prepended protocol information
    $filename = rawurldecode( str_replace( $url_prefix, '', $itunes_url ) );
    
    //url decode the result
    return $filename;
  }

  /**
   * Is php running on a windows machine?
   * @return    bool: true if windows platform
   */
  public static function is_windows()
  {
    return ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) ? true : false;
  }
  
  /**
   * Modifies a string to remove al non ASCII characters and spaces.
   * From Snipplr http://snipplr.com/view.php?codeview&id=22741
   * @param text string: the string to slugify
   * @return the slugified string
   */
  public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
 
    // lowercase
    $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text))
    {
        return false;
    }
 
    return $text;
  }
}