<?php
/**
 * Delete a user manually
 *
 * @package    streeme/sfGuard
 * @subpackage task
 */
class sfGuardDeleteUserTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name to delete'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption( 'connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine' ),
    ));

    $this->namespace = 'guard';
    $this->name = 'delete-user';
    $this->briefDescription = 'Deletes a user';

    $this->detailedDescription = <<<EOF
The [guard:delete-user|INFO] task deletes a user:

  [./symfony guard:delete-user fabien|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : 'all';
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    if (
      !$options['no-confirmation']
      &&
      !$this->askConfirmation(array_merge(
        array(sprintf('Deleting user "%s" on "%s" connection(s):', $arguments['username'], $environment)),
        array('', 'Are you sure you want to proceed? (y/N)')
      ), 'QUESTION_LARGE', false)
    )
    {
      exit;
    }

    $q = Doctrine_Query::create()
        ->delete( 'sfGuardUser sgu' )
        ->where( 'sgu.username = ?', $arguments['username'] )
        ->execute();

    $this->logSection('guard', sprintf('Delete user "%s"', $arguments['username']));
  }
}
