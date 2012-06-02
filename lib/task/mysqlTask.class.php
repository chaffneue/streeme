<?php

class mysqlTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('intialize', sfCommandArgument::REQUIRED, 'Initialize the mysql indexer'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'proceed without prompts')
    ));

    $this->namespace        = '';
    $this->name             = 'mysql';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [mysql|INFO] task adds the indexer schema to a mysql database to use mysql's
native indexing functionality. Define your mysql database in databases.yml.

Call it with:

  [symfony mysql initialize|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    if (
      !$options['no-confirmation']
      &&
      !$this->askConfirmation(array_merge(
        array(sprintf('This command will add fulltext keys to the "%s" connection(s) database:', $options['env']), ''),
        array('', 'Are you sure you want to proceed? (y/N)')
      ), 'QUESTION_LARGE', false)
    )
    {
      exit;
    }
    
    $dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    
    //create a term pool index i to reduce the problem set for mysql
    $dbh->exec('DROP TABLE IF EXISTS indexer');
    $dbh->exec('CREATE TABLE indexer (sfl_guid varchar(50), i text) ENGINE=MyISAM');
    $dbh->exec('ALTER TABLE indexer ADD FULLTEXT(i)');

    echo "Finished!\r\n";
  }
}