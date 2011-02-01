<?php
#
# This is the art proxy redirect - it may not be part of the regular controller because of conflicts
# with HTTP_Download setting its own headers. the routing for this resides in the .htaccess file
#
require_once( dirname(__FILE__) . '/../apps/client/lib/ProxyBootstrap.class.php' ); 
$art_proxy = new ArtProxy( $_REQUEST[ 'hash' ],  $_REQUEST[ 'size' ], dirname( __FILE__ ) . '/../data/album_art' );

//shut down the symfony context so it doesn't lock up symfony while streaming
global $context;
$context->shutdown();
unset( $context);

$art_proxy->getImage();
exit;