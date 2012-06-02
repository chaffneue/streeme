<?php
/**
 * This is a generic class for hooking Streeme up to LDAP authentication endpoints. You must create a user with
 * the same name before you can autheticate. use:
 *
 * ./symfony guard:create-user full_ldap_username strong_password
 *
 * @author Richard Hoar
 * @package Streeme
 * @depends sfDoctrineGuardPlugin
 */
require_once(sfConfig::get('sf_lib_dir').'/vendor/adLDAP/src/adLDAP.php');

class StreemeLdapUser extends sfGuardSecurityUser
{
  public static function checkLdapCredentials($username, $password)
  {
    $options = sfConfig::get('app_sf_guard_plugin_ldap_settings', array());
    $ldap = new adLDAP($options);
    $authenticated = $ldap->authenticate($username, $password);
    
    return ($authenticated) ? true : false;
  }
}