<?php
class StreemeUtil
{
  /**
  * Format and encode a filesystem name into an iTunes style format
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
        if ( self::is_windows() )
        {
          $accumulator[] = rawurlencode( utf8_encode( $part ) );
        }
        else
        {
          $accumulator[] = rawurlencode( $part );
        }
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
    //build the iTunes URL prefix and allow for single byte latin chars
    if ( self::is_windows() )
    {
      $url_prefix = 'file://localhost/';
      $itunes_url = self::replace_url_nonfs_chars( $itunes_url );
      $filename = utf8_decode( str_replace( $url_prefix, '', rawurldecode( $itunes_url ) ) );
    }
    else
    {
      $url_prefix = 'file://localhost';
      $filename = str_replace( $url_prefix, '', rawurldecode( $itunes_url ) );
    }

    //url decode the result
    return $filename;
  }

  /**
   * Is php running on a windows machine?
   * @return    bool: true if windows platform
   */
  public static function is_windows()
  {
    if( sfConfig::get('sf_environment') === 'test' )
    {
      return false;
    }
    else
    {
      return ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) ? true : false;
    }
  }
  
  /**
   * Modifies a string to remove al non ASCII characters and spaces.
   *
   * @param text string: the string to slugify
   * @return the slugified string
   * @see http://snipplr.com/view.php?codeview&id=22741
   */
  public static function slugify($text)
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
  
  /**
   * Remove null terminations and whitespace from a string (UTF8 friendly)
   *
   * @param text     str: the dirty string
   * @return         str: non printable sanitized string
   */
  public function xmlize_uf8_string( $text )
  {
    $blacklist = array( chr(0), '\0', '\t', '\r', '\n', 'ÿþ' );
    foreach( range( chr(0),chr(127) ) as $alpha ) array_unshift( $blacklist, sprintf( '%sÿþ', $alpha ) );
    return  trim( str_replace( $blacklist, '', $text ) );
  }
  
  /**
   * Convert itunes mbchars to single byte latin for windows
   * @param text str: the dirty string
   * @return     str: sanitized str
   */
  public function replace_url_nonfs_chars( $text )
  {
    $search = array(
                     '%E2%80%93',
                     '%E2%80%A6',
                     '%E2%80%BA'
                    );
    $replace = array(
                      '%96',
                      '%85',
                      '%9B'
                    );
    
    return str_replace( $search, $replace, $text );
  }
}