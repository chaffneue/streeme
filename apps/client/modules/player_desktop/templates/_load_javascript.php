<?php
  if( strtolower( $namespace ) != 'sfguardauthsignin')
  {
    $combiner = new combineFiles();
    echo sprintf( '<script src="%s" type="text/javascript"></script>',
                  $combiner->combine( 'js', $namespace, sfContext::getInstance()->getResponse()
                 ) );
    unset( $combiner );
  }
  else
  {
    include_javascripts();
  }
?>
<script type="text/javascript" id="loadjavascript">
<!--
/* Add Functions that Need to Load after Ready Here */
$(document).ready(function()
{
<?php
	//the load_html5_player partial may load jplayer javascripts using this slot if required
	echo get_slot( 'javascript_player_loader' );
	
  $protocol = ( !empty( $_SERVER['HTTPS'] ) ) ? 'https://' : 'http://';
  $hostname = str_replace( $_SERVER[ 'SERVER_PORT' ], '', $_SERVER['HTTP_HOST'] );
  if ( !empty( $_SERVER['HTTPS'] ) )
  {
	  $port = '';
  }
  else
  {
	  if( substr( $hostname, -1 ) === ':' )
	  {
		  $port = $music_proxy_port;
	  }
	  else
	  {
		  $port = ':' . $music_proxy_port;
	  }
  }
  //music url
  echo 'mediaurl = "' . $protocol . $hostname . $port . '";' . "\r\n";
  
  //artwork and asset url
  echo 'rooturl = "' . rtrim( url_for( '@javascript_base', true ), '/' ) . '";' . "\r\n";
  
  //javascript service endpoint
  echo 'javascript_base = "' . rtrim( url_for( '@javascript_base', true ), '/' ) . '";' . "\r\n";
?>
  results_per_page = "<?php echo sfConfig::get( 'app_results_per_page' ) ?>";
  send_session_cookies = <?php echo ( sfConfig::get( 'app_send_cookies_with_request' ) ) ? 'true' : 'false' ?>;
  send_cookie_name = "<?php echo sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember') ?>";

  //i18n language tokens
  sFirst = "<?php echo __( 'First' ) ?>";
  sLast = "<?php echo __( 'Last' ) ?>";
  sNext = "<?php echo __( 'Next' ) ?>";
  sPrevious = "<?php echo __( 'Previous' ) ?>";
  sEmptyTable = "<?php echo __( 'No data available in table' ) ?>";
  sInfo = "<?php echo __( 'Showing _START_ to _END_ of _TOTAL_ entries' ) ?>";
  sInfoEmpty = "<?php echo __( 'No entries to show' ) ?>";
  sInfoFiltered = "<?php echo __( '(filtered from _MAX_ total entries)' ) ?>";
  sLengthMenu = "<?php echo __( 'Show _MENU_ songs' ) ?>";
  sProcessing = "<?php echo __( 'Loading...' ) ?>";
  sSearch = "<?php echo __( 'Search:' ) ?>";
  sZeroRecords = "<?php echo __( 'No songs to display' ) ?>";
  sAlbumArtImageAlt = "<?php echo __( 'Album Art Image for: ' ) ?>";
  appExitMessage = "<?php echo __( 'Are you sure you want to quit the Streeme desktop application?' ) ?>";
  addtoplaylist = "<?php echo __( 'Click to add to current playlist' ) ?>";
  deletefromplaylist = "<?php echo __( 'Click to remove this song from the playlist' ) ?>";
  addItemSuccess = "<?php echo __( 'Item(s) added to playlist' ) ?>";
  addItemError = "<?php echo __( 'Error! Did you select a playlist first?' ) ?>";
  deleteItemSuccess = "<?php echo __( 'Item deleted from the active playlist' ) ?>";
  deleteItemError = "<?php echo __( 'Error! Streeme reported an error. Song not deleted.' ) ?>";
  playlistNameInput = "<?php echo __( 'Please specify a name for the new playlist' ) ?>";
  addPlaylistSuccess = "<?php echo __( 'Added New Playlist' ) ?>";
  addPlaylistError = "<?php echo __( 'Error! Playlist not created.' ) ?>";
  deletePlaylistSuccess = "<?php echo __( 'Playlist Deleted' ) ?>";
  deletePlaylistError = "<?php echo __( 'Error! Streeme reported an error. Playlist not deleted.' ) ?>";
  confirmDelete = "<?php echo __( 'Are you sure you want to delete this item?' ) ?>";
  playsongbutton = "<?php echo __( 'Play this track' ) ?>";
         
  //Load the Application
  streeme.__initialize( results_per_page );
});
-->
</script>