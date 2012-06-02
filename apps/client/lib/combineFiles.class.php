<?php
/*
* combines and minifies javascript and css assets
*
* @package    streeme
* @subpackage combineFiles
* @author     Richard Hoar
*/
class combineFiles
{
  /**
   * Combine multiple text assets into a single file for better http performance this
   * method generates a new cache file with every symfony cc you can override the cache
   * by adding ?clearassetcache=1 to the page request.
   *
   * @param type      string css or js
   * @param namespace string the combined file namespace (eg. module+action names)
   * @param response  object the sfWebResponse instance
   * @return          string the url for the combiner service
   */
  public function combine( $type, $namespace, sfWebResponse $response )
  {
    //configure the combiner
    $type = ( $type === 'css' ) ? 'css' : 'js';
    $fullname = ( $type === 'css' ) ? 'Stylesheets' : 'Javascripts';
    $response_getter = 'get' . $fullname;
    $namespace = StreemeUtil::slugify( $namespace );
    
    //integrate into symfony's asset globals
    sfConfig::set( sprintf( 'symfony.asset.%s_included', strtolower( $fullname ) ), true);
  
    //build the cache filename - this file will be regenerated on a symfony cc
    $path     = sprintf('%s/combine/%s/',
                         sfConfig::get('sf_cache_dir'),
                         $type
                        );
    $filename = sprintf( '%s.%s',
                         $namespace,
                         $type
                        );
                        
    // you can force a cache clear by passing ?clearassetcache=1 to any template
    if( !is_readable( $path . $filename ) || @$_GET['clearassetcache'] == 1 )
    {
      //build one file of all of the css or js files
      $file_content = '';
      
      //load vendor libraries for minifying assets
      require_once( sfConfig::get( 'sf_lib_dir' ) . '/vendor/jsmin/jsmin.php' );
      require_once( sfConfig::get( 'sf_lib_dir' ) . '/vendor/cssmin/cssmin.php' );
      
      foreach ($response->$response_getter() as $file => $options)
      {
        if( $type === 'css' )
        {
          $file_content .= CSSMin::minify( file_get_contents( sfConfig::get('sf_web_dir') . $file ) );
        }
        else
        {
          $file_content .= JSMin::minify( file_get_contents( sfConfig::get('sf_web_dir') . $file ) );
        }
      }
      
      //this file resides in the cache and requires wide permissions for both cli and apache users
      @umask( 0000 );
      @mkdir( $path, 0777, true );
      file_put_contents( $path . $filename , $file_content );
    }
    
    return sprintf( '/service/combine/%s/%s', $type, str_replace( '-', '_', $namespace ) );
  }
  
  /**
   * Get a combined file from the cache dir
   *
   * @param type      string css or js
   * @param namespace string the combined file namespace (eg. module+action names)
   * @return          string filesystem path to combined file
   */
  public function getFileName( $type, $namespace )
  {
    $type = ( $type === 'css' ) ? 'css' : 'js';
    $namespace = StreemeUtil::slugify( $namespace );
    
    return sprintf('%s/combine/%s/%s.%s',
                     sfConfig::get('sf_cache_dir'),
                     $type,
                     $namespace,
                     $type
                    );
  }
}