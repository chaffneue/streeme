<?php 
/**
 * Parse an iTunes xml file for tracks using XPAT to avoid exceptional memory overhead of an array based plist parser
 *
 * @package    streeme
 * @subpackage StreemeItunesTrackParser
 * @author     Richard Hoar
 */
class StreemeItunesTrackParser
{
  //we'll need to watch the state to stream all this stuff out cleanly
  protected $track_cursor = 0;
  
  //the song array should be of the array type and ready for data 
  protected $songs = array();
  
  /**
   * construct the parser class
   * @param file str: the itunes music library.xml file
   * @see: http://developer.apple.com/internet/opensource/php.html
   */
  public function __construct( $file )
  {
    //create the parser 
    $this->xml_parser = xml_parser_create( "UTF-8" );
    xml_parser_set_option( $this->xml_parser, XML_OPTION_CASE_FOLDING, 1 );
    xml_set_element_handler( $this->xml_parser, array( 'StreemeItunesTrackParser', 'startElement' ), array( 'StreemeItunesTrackParser', 'endElement' ) );
    xml_set_character_data_handler( $this->xml_parser, array( 'StreemeItunesTrackParser', 'charData' ) );
    
    if ( !( $this->fp = @fopen( $file, "r" ) ) )
    {
      throw new Exception( 'Could not open iTunes File' );
    }
  }
  
  /**
   * Iterate over the tracks in the itunes.xml file - return the entire track record to flush
   * out to the database 
   * @return           array: the track information for a single song 
   */
  public function getTrack()
  {
    $this->songs = array();
    
    while ($this->data = fgets($this->fp))
    {
      //read and parse another line from the itunes file
      if ( !xml_parse( $this->xml_parser, $this->data ) )
      {
        throw new Exception( sprintf( "XML error: %s at line %d",
          xml_error_string(xml_get_error_code($this->xml_parser)),
          xml_get_current_line_number($this->xml_parser)));
      }
      
      //is the array ready yet?
      if ( $this->pull )
      {
        $this->pull = 0;
        return $this->songs;
      }
    }
  }

  /**
   * Free the xml parser
   */
  public function free()
  {
    xml_parser_free($this->xml_parser);
  }
  
  /**
   * This callback indicates that the start of a dict element has 
   * occured and manages the outer track loop when the loop is complete
   * we fire indicate that the array is ready to be flushed to the application
   * in getTrack. 
   * @param parser  res: the SAX parser handle
   * @param name    str: the element nam
   * @param         arr: attributes for the callback
   */
  private function startElement( $parser, $name, $attribs )
  {
    if( $name == "DICT" )
    {
      $this->number_dicts++;
    }
    if ($this->number_dicts > 3 && $name == "DICT" )
    {
      $this->pull = true;
    }
    if ($this->number_dicts > 2)
    {
      $this->current_element = $name;
    }
  }
  
  /**
   * Looks for the end of the Track list 
   * @param parser  res: the SAX parser handle
   * @param data    str: the string to stream through
   */
  private function charData( $parser, $data )
  {
    if( $data === "Playlists" )
    {
      $this->end_of_songs = TRUE;
    }
    if($this->current_element === "KEY")
    {
      $this->current_data = $data;
    }
    else
    {
      $this->current_data .= $data;
    }
  }
  
  /**
   * Populates the pull array 
   * @param parser  res: the SAX parser handle
   * @param name    str: the element name
   */
  private function endElement( $parser, $name )
  {
    if($this->end_of_songs)
    {
      return;
    }
    if(!empty($this->current_element))
    {
      if($this->current_element === "KEY")
      {
        $this->array_key = trim( $this->current_data );
        $this->current_data = null;
      }
      else if( trim($this->current_data) )
      { 
        $this->songs[ $this->array_key ] = trim( $this->current_data );
        $this->current_data = null;
      }
    }
  }
}