<?php
/**
 * Create/Remove the users in Streeme
 *
 * @author Richard Hoar
 * @package streeme
 */

class UserSetupGenerator
{
  /**
   * Add a user to the database
   *
   * @param username str: the username
   * @param password str: the password
   * @return         bol: true on successful write
   */
  public function addUser($username, $password)
  {
    $results = array();
    exec(sprintf('cd %s && %s guard:create-user %s %s', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony'), escapeshellarg($username), escapeshellarg($password)), $results);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    $val = array_pop($results);
    return (strpos($val, 'Create user')) ? true : false;
  }
  
  /**
   * Delete a user from the database
   *
   * @param username str: the username
   * @return         bol: true on successful delete
   */
  public function deleteUser($username)
  {
    $results = array();
    exec(sprintf('cd %s && %s guard:delete-user %s --no-confirmation=true', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony'), escapeshellarg($username)), $results);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    $val = array_pop($results);
    return (strpos($val, 'Delete user')) ? true : false;
  }
  
  /**
   * Log a message
   *
   * @param message str: the message text
   * @param level   str: the sfLogger constant associated with the alert level
   */
  private function log($message, $level = sfLogger::DEBUG)
  {
    sfContext::getInstance()->getLogger()->log(sprintf('{WebUserSetupForm} %s', $message), $level);
  }
}