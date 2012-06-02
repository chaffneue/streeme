<?php
class mediaindexTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'client'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = '';
    $this->name             = 'media-index';
    $this->briefDescription = 'Update the indexer with search terms';
    $this->detailedDescription = <<<EOF
The [media-index|INFO] task adds all scanned songs to the search indexer of your choice
Call it with:

  [php symfony media-index|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    echo "Indexing media - please wait...\r\n";

    if(!sfConfig::get('app_indexer_use_indexer', false))
    {
      throw new Exception('Search indexing is disabled, please configure a search indexing service in app.yml');
    }
    $settings = sfConfig::get('app_indexer_settings');
    $indexer = new $settings['class'];

    $result_list = array();
    if(SongTable::getInstance()->getIndexerList($result_list))
    {
      $indexer->prepare();
      foreach($result_list as $result)
      {
        $indexer->addDocument($result["unique_id"], $result["name"], $result["artist_name"], $result["album_name"], 'genre');
      }
      $indexer->flush();
    }
    else
    {
      throw new Exception('Library is empty. Please scan your media before indexing.');
    }
    
    echo "Finished!\r\n";
  }
}
