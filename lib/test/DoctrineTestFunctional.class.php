<?php
class DoctrineTestFunctional extends sfTestFunctional
{
  /**
   * Set up the functional test to use a database (doctrine)
   * Loads test Data for functional tests - model database and sets up a dummy user
   *
   * @return object the context
   */
  public function loadData()
  {
    exec( dirname(__FILE__) . '/../../symfony doctrine:build --all --and-load --env=test --no-confirmation');
    exec( dirname(__FILE__) . '/../../symfony guard:create-user apptest abc123 --env=test');
    Doctrine_Core::loadData(sfConfig::get('sf_test_dir').'/fixtures/50_FunctionalAll/table.yml');
    exec( dirname(__FILE__) . '/../../symfony cc --env=test');
    return $this;
  }
  
   /**
    * Authenticates a user with a given username and password. to help test with sfguard
    *
    * @param string $username username of the user
    * @param string $password password of the user
    * @param string $click value of the link or button to submit the login form
    * @param string $nameFormat name format of the form
    *
    * @return sfTestBrowser The current sfTestBrowser instance
    */
   public function authenticate( $username='apptest', $password='abc123', $click = 'Sign in', $nameFormat = 'signin' )
   {
      return $this->
        info( sprintf( 'Signing in user using username "%s" and password "%s"', $username, $password ) )->
        get('/login')->
        click( $click, array( $nameFormat => array( 'username' => $username, 'password' => $password ) ), array( '_with_csrf' => true ) )
      ;
   }
}