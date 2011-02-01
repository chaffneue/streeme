<?php
#
# This bootstrap provides basic framework functionality and authorization for the media streaming scripts
#
ob_start();

require_once(dirname(__FILE__).'/../../../config/ProxyConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('client', 'proxy', false);
$context = sfContext::createInstance($configuration);

$authenticated = false;

//Try the session value ('symfony' session cookie) - it's the fastest
if( sfContext::hasInstance() )
{
  $authenticated = $context->getUser()->isAuthenticated();
}

//If the session value is not set, try the remember key or the passed in session key
if ( sfContext::hasInstance()
     &&
     $authenticated === false
     &&
     $context->getUser()->isAnonymous()
   )
{
  $cookieName = sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember');
  $cookie = $context->getRequest()->getCookie( $cookieName );
  if( empty( $cookie )
      &&
      sfConfig::get( 'app_send_cookies_with_request' )
      &&
      isset( $_REQUEST[ $cookieName ] )
      &&
      !empty( $_REQUEST[ $cookieName ] )
    )
  {
    $cookie = $_REQUEST[ $cookieName ];
  }
  
  $q = Doctrine::getTable( 'sfGuardRememberKey' )->createQuery( 'r' )
    ->innerJoin( 'r.sfGuardUser u' )
    ->where( 'r.remember_key = ?', $cookie );
  if( sfConfig::get( 'app_send_cookies_with_request' ) )
  {
    $q->andWhere( 'r.ip_address = ?', $_SERVER['REMOTE_ADDR'] );
  }
  if ($q->count())
  {
    $authenticated = true;
  }
  $q->free();
}

ob_end_clean();

if( !$authenticated )
{
  header( "HTTP/1.1 403 Forbidden" );
  exit;
}

//clean up
unset( $configuration, $q, $authenticated );