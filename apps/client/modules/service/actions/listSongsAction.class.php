<?php
class listSongsAction extends sfAction
{
  /**
   * Modelled on Datatables API. See their API docs for the entire request docs
   * @see http://datatables.net/usage/server-side
   */
  protected
    $iDisplayStart,  // int Display start point
    $iDisplayLength, // int Number of records to display
    $iColumns,       // int Number of columns being displayed (useful for getting individual column search info)
    $sSearch,	       // str Global search field
    $bEscapeRegex,	 // bool Global search is regex or not
    $bSortable,      // Indicator for if a column is flagged as sortable or not on the client-side
    $iSortingCols,   // Number of columns to sort on
    $sEcho,          // Information for DataTables to use for rendering
    $output_format;  // output format json|xml|php array

  public function execute($request)
  {
    //default output format is JSON
    $this->output_format = 'json';
    
    //hydrate class variables
    foreach( $request->getParameterHolder()->getAll() as $k => $v )
		{
			  $this->$k = $v;
		}
		
		//stream the data
		$this->create_response();
  }
  
  /**
   * Create the data set and stream it to the user in the requested format
   */
  private function create_response()
  {
    $args = array(
                   'disable_limiting' => false,
                   'search'           => $this->sSearch,
                   'limit'            => $this->iDisplayLength,
                   'offset'           => $this->iDisplayStart,
                   'sortcolumn'       => $this->iSortCol_0,
                   'sortdirection'    => $this->sSortDir_0
             );
    $result_count = $result_list = null;
    $result = Doctrine_Core::getTable('Song')->getList( $args, $result_count, $result_list );

    $song_array    = $result_list;
    $found_rows    = $result_count;
    $total_library = Doctrine_Core::getTable('Song')->getTotalSongCount();
    
    switch( $this->output_format )
    {
      case 'json':
        echo $this->to_json_dataTable( $song_array, $found_rows, $total_library );
        break;
    }
    exit;
  }
  
  /**
   * JSON Conversion with a touch of post processing for presentation
   * @param song_array     array: the data set to encode
   * @param found_rows     int: total number of found rows
   * @param total_library  int: the complete library song count
   * @return               str: JSON encoded serialized array
   */
  private function to_json_dataTable( $song_array = array(), $found_rows, $total_library )
  {
    $count = 0;
    $empty_resultset[] = array( "", "No Matches Found...", "", "", "", "", "", "", "" );
    if ( is_array( $song_array ) )
    {
       foreach ( $song_array as $k => $v)
       {
          $string = null;
          $unique_id = null;
          $jplayer_types = array (
                                    '.mp3' => 'mp3',
                                    '.m4a' => 'm4a',
                                    '.aac' => 'm4a',
                                    '.mp4' => 'm4a',
                                    '.ogg' => 'ogg',
                                    'webm' => 'webma',
                                    '.wav' => 'wav',
                                  );
          foreach ( $v as $key => $value)
          {
             $addtoplaylistbutton = null;
             $playsongbutton = null;
             if( $key == 'total_matches' )
             {
                continue;
             }
             if( $key == 'unique_id' )
             {
               $unique_id = $value;
             }
             if( $key == 'name' && strpos(  $this->sSearch, 'playlistid:' ) === false )
             {
                $addtoplaylistbutton = '<div class="ap" onclick="streeme.addpls( \'song\', \'' . $unique_id . '\'  )"></div>';
             }
             if( $key == 'name' && !( strpos(  $this->sSearch, 'playlistid:' ) === false ) )
             {
                $addtoplaylistbutton = '<div class="dp" onclick="streeme.delpls( \'' . $unique_id . '\'  )"></div>';
             }
             if( $key == 'name' && strpos(  $this->sSearch, 'playlistid:' ) === false )
             {
               $playsongbutton = '<div class="ps"></div>';
             }
             if( $key == 'date_modified')
             {
               $value = ( $value ) ? date( 'Y-m-d', $value ) : '--';
             }
             if( $key == 'yearpublished' )
             {
               $value = ( $value ) ? $value : '--';
             }
             if( $key == 'tracknumber' )
             {
               $value = ( $value ) ? $value : '--';
             }
             if( $key == 'length' )
             {
               $value = ( $value ) ? $value : '--';
             }
             if( $key == 'filename' )
             {
               //we actually only want the file extension for jPlayer
               $value = ( $value ) ? @$jplayer_types[ strtolower( substr( $value, -4 ) ) ] : '';
             }
             if( $key == 'album_mtime' )
             {
              continue;
             }
             $string .= ( ( $value ) ?  $addtoplaylistbutton . $playsongbutton . $value : '0' ) . '%*=*=*%';
          }
          $convert = explode( '%*=*=*%', rtrim( $string, '%*=*=*%' ) );
          $flattened[ $count ] = $convert;
          $count++;
       }
    }
    else
    {
       $flattened = array();
    }
    
    $aadata[ 'sEcho' ] = (int) $this->sEcho;
    $aadata[ 'iTotalRecords' ] = (int) Doctrine_Core::getTable('Song')->getTotalSongCount();
    $aadata[ 'iTotalDisplayRecords' ] = (int) $found_rows;
    
    //Sort Tracks in new entries
    if( count( $flattened ) > 0 )
    {
      $aadata[ 'aaData' ] = $flattened;
    }
    else
    {
      $aadata[ 'aaData' ] = $empty_resultset;
    }
    
    return json_encode( $aadata );
  }
}
?>