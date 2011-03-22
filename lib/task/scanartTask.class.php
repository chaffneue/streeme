<?php

class scanartTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption( 'application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'client' ),
      new sfCommandOption( 'env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod' ),
      new sfCommandOption( 'connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine' ),
      new sfCommandOption( 'source', null, sfCommandOption::PARAMETER_REQUIRED, 'The source to scan'),
      new sfCommandOption( 'resync', null, sfCommandOption::PARAMETER_REQUIRED, 'Resync your art after a database wipe'),
    ));

    $this->namespace        = '';
    $this->name             = 'scan-art';
    $this->briefDescription = 'Scan various sources for album art';
    $this->detailedDescription = <<<EOF
The [scan-art|INFO] will initiate a scan of your albums for art from sources
like Amazon PAS, your music files and artwork from the commons. Stores found
images in sfproject/web/images/album_art


Sources:
  --source=amazon       - Use Amazon PAS (see cloudfusion config)
  --source=meta         - Read artwork embedded in your media
  --source=folders      - Read artwork from the media folders
  --source=service      - Read from future service (unused)

Resync:
  Use this command to resync your art after a database wipe or any situation
  where your databas ehas been damaged for some reason, but your artwork
  cache remains intact.
  
  --resync=true         - Use this command to resync the art cache

Call it with:

  [php symfony scan-art --source="..."|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    // load the scanner
    switch ( $options[ 'source' ] )
    {
      case 'amazon':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanAmazon.php' );
        break;
        
      case 'meta':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanMeta.php' );
        break;
      
      case 'folders':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanFolders.php' );
        break;
      
      case 'service':
        //reserved for future album art services: unused at the moment
        throw new Exception( 'Not Ready: This is a reserved block for future album art sources.' );
        break;
    }
    
    // resync the database's has_art flag with the users art cache
    if( $options[ 'resync' ] )
    {
      require_once( dirname( __FILE__ ) . '/scanners/artworkScanResync.php' );
    }
    
    echo "\r\n";
  }
}
?>
