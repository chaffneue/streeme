<?php

/**
 * setup actions.
 *
 * @package    streeme
 * @subpackage setup
 * @author     Richard Hoar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class setupActions extends sfActions
{
  /**
   * Always check for local or whitelisted user before running these actions
   */
  public function preExecute()
  {
    //you may add other ips to the whitelist if you wish to configure remotely using the config param below
    $allowed_ips = array_merge(array('127.0.0.1', '::1'), sfConfig::get('app_setup_acl_allowed_ips', array()));
    if (!in_array(@$_SERVER['REMOTE_ADDR'], $allowed_ips))
    {
     die('You are not allowed to access this file.'. $_SERVER['REMOTE_ADDR']);
    }
  }
  
   /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
  
 /**
  * Executes user setup form action
  *
  * @param sfRequest $request A request object
  */
  public function executeSetupUser(sfWebRequest $request)
  {
    $this->form = new UserSetupForm();
    $this->user_add_error = false;
    $this->user_del_error = false;
    
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('setup'));
      if ($this->form->isValid())
      {
        $options = $this->form->getValues();
        $generator = new UserSetupGenerator();
        switch($options['user_action'])
        {
          case 'add':
            if($generator->addUser($options['username'], $options['password']))
            {
              $this->redirect('@setup_application');
            }
            else
            {
              $this->user_add_error = true;
            }
          break;
          
          case 'del':
            if($generator->deleteUser($options['username']))
            {
              $this->redirect('@setup_user');
            }
            else
            {
              $this->user_del_error = true;
            }
          break;
        }
      }
    }
  }
  
  /**
   * Execute the database setup form action
   *
   * @param sfRequest $request A request object
   */
  public function executeSetupDb(sfWebRequest $request)
  {
    $this->form = new DatabaseSetupForm();
    $this->config_write_error = false;
    $this->db_setup_error = false;
    
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('setup'));
      if ($this->form->isValid())
      {
        $options = $this->form->getValues();
        $generator = new DbSetupGenerator(new sfYamlDumper);
        if($generator->create(sfConfig::get('sf_root_dir') . '/config/databases.yml', $options['database_type'], $options))
        {
          if($generator->reloadDb())
          {
            $this->redirect('@setup_user');
          }
          else
          {
            $this->db_setup_error = true;
          }
        }
        else
        {
          $this->config_write_error = true;
        }
      }
    }
  }
   
  /**
   * Execute the application setup form action
   *
   * @param sfRequest $request A request object
   */
  public function executeSetupApplication(sfWebRequest $request)
  {
    $this->form = new ApplicationSetupForm();
    $this->isMysql = (Doctrine_Manager::getInstance()->getCurrentConnection()->getDriverName() === 'Mysql') ? true : false;
    $this->create_error = false;
    $this->bootstrap_error = false;
    $this->clear_error = false;
    
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('setup'));
      if ($this->form->isValid())
      {
        $options = $this->form->getValues();
        $generator = new ApplicationSetupGenerator(new sfYamlDumper);
        if($generator->create(sfConfig::get('sf_root_dir') . '/apps/client/config/app.yml', $options))
        {
          if($generator->reloadApplication())
          {
            if($this->isMysql && $options['database_indexing'])
            {
              if(!$generator->bootstrapIndexer())
              {
                $this->bootstrap_error = true;
              }
            }
          }
          else
          {
            $this->clear_error = true;
          }
        }
        else
        {
          $this->create_error = true;
        }
        
        if(!$this->create_error && !$this->bootstrap_error && !$this->clear_error)
        {
          $this->redirect('@setup_scan');
        }
      }
    }
  }
  
  /**
   * Execute the scan media page
   *
   * @param sfRequest $request A request object
   */
  public function executeSetupScanMedia(sfWebRequest $request)
  {
    $this->scanSuccess = false;
    $this->scanError = false;
    
    if ($request->isMethod('post'))
    {
      $generator = new ApplicationSetupGenerator(new sfYamlDumper);
      if($generator->runMediaScan())
      {
        $this->scanSuccess = true;
      }
      else
      {
        $this->scanError = true;
      }
    }
  }
}
