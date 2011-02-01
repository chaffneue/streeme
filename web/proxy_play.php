<?php
#
# This is the media player proxy redirect - it may not be part of the regular controller because of conflicts
# with HTTP_Download setting its own headers. the routing for this resides in the .htaccess file
#
//configure app and validate the user
require_once( dirname(__FILE__) . '/../apps/client/lib/ProxyBootstrap.class.php' ); 

//create a new media player object
$mediaplayer = new MediaProxy( $_REQUEST[ 'unique_song_id' ] );

//add options 
if( isset( $_REQUEST[ 'target_bitrate' ] ) && !empty( $_REQUEST[ 'target_bitrate' ] ) )
{
  $mediaplayer->setTargetBitrate( $_REQUEST[ 'target_bitrate' ] );
}
if( isset( $_REQUEST[ 'target_format' ] ) && !empty( $_REQUEST[ 'target_format' ] ) )
{
  $mediaplayer->setTargetFormat( $_REQUEST[ 'target_format' ] );
}
if( isset( $_REQUEST[ 'is_icy_response' ] ) && !empty(  $_REQUEST[ 'is_icy_response' ] ) )
{
  $mediaplayer->setIsIcyResponse( $_REQUEST[ 'is_icy_response' ] );
}

//shut down the symfony context so it doesn't lock up symfony while streaming
global $context;
$context->shutdown();
unset( $context);

//play the media file
$mediaplayer->play();