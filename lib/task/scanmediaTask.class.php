<?php
class scanmediaTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption( 'application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'client' ),
      new sfCommandOption( 'env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod' ),
      new sfCommandOption( 'connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine' ),
      new sfCommandOption( 'type', null, sfCommandOption::PARAMETER_REQUIRED, 'The type of scan to run - see opts'),
    ));

    $this->namespace        = '';
    $this->name             = 'scan-media';
    $this->briefDescription = 'Scan and update your media library';
    $this->detailedDescription = <<<EOF
The [scan-media|INFO] task will scan your watched folders for new media and
import them into your streeme library. This task should be run
periodically, but note the time a scan normally takes your machine before
adding it to a cron/scheduled task.


Types:
  --type=itunes         - Read an Itunes.xml file for media
  --type=filesystem     - Scan the filesystem for media

Call it with:

  [php symfony scan-media --type="..."|INFO]
EOF;
  }

  protected function execute( $arguments = array(), $options = array() )
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    // load the scanner
    switch ( $options[ 'type' ] )
    {
      case 'itunes':
        //check that an itunes file exists and is readable
        $itunes_file = sfConfig::get( 'app_itunes_xml_location' );
        if ( !isset( $itunes_file ) || empty(  $itunes_file  ) )
        {
          throw new Exception( 'You must set up a link to your itunes XML file in sfproject/client/config/app.yml' );
        }
        if( !is_readable( $itunes_file ) )
        {
          throw new Exception( 'Invalid Path to Itunes XML File.' );
        }
        
        require_once( dirname( __FILE__ ) . '/scanners/mediaScanItunes.php' );
        
        break;
        
      case 'filesystem':
        //check that there is at least 1 watched folder
        $watched_folders = sfConfig::get( 'app_wf_watched_folders' );
        if ( count( $watched_folders ) < 1 )
        {
          throw new Exception( 'You must set up at least 1 watched folder in sfproject/client/config/app.yml' );
        }
        
        require_once( dirname( __FILE__ ) . '/scanners/mediaScanFilesystem.php' );
        
        break;

      default:
        echo 'No Valid Type Specified - Aborting';
        break;
    }
    echo "\r\n";

    if(sfConfig::get('app_indexer_use_indexer'))
    {
      $mediaIndexTask = new mediaindexTask($this->dispatcher, $this->formatter);
      $mediaIndexTask->run(array(), array('application' => $arguments['application'], 'env' => $options['env'], 'connection' => $options['connection']));
    }
  }
}
