<?php
class scanplaylistTask extends sfBaseTask
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
    $this->name             = 'scan-playlists';
    $this->briefDescription = 'Scan and update your media library';
    $this->detailedDescription = <<<EOF
The [scan-playlists|INFO] task will scan your deskto media player playlists
and append them into your streeme playlists. This task should be run
whenever a playlist changes. Data from this task will supercede data gathered
in previous tests.


Types:
  --type=itunes         - Read playlists from iTunes

Call it with:

  [php symfony scan-playlists --type="..."|INFO]
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
        
        require_once( dirname( __FILE__ ) . '/scanners/playlistScanItunes.php' );
        
        break;
    }
    echo "\r\n";
  }
}
