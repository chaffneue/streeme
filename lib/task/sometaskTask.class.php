<?php

class sometaskTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption( 'application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'client' ),
      new sfCommandOption( 'env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'test' ),
      new sfCommandOption( 'connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine' ),        
    ));

    $this->namespace        = '';
    $this->name             = 'sometask';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [sometask|INFO] task does things.
Call it with:

  [php symfony sometask|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $media_scan = new MediaScan();
    for( $i = 1; $i < 30000; $i++ )
    {
      if( $i === 1 || $count_group == 9 )
      {
        $artist = uniqid(); 
        $album = uniqid();
        $genre = uniqid();
        $label = uniqid();
        $count_group = 0;
      }
      $count_group++;
      
      if( $count_display == 250 )
      {
        echo '.';
        $count_display = 0;
      }
      $count_display++;
      
      $song = array();
      $song['artist_name'] = $artist;    
      $song['album_name'] = $album;   
      $song['genre_name'] = $genre;     
      $song['song_name']  = uniqid();     
      $song['song_length'] = mt_rand( 0, 99 ) . ':' . mt_rand( 0, 99 );    
      $song['accurate_length']  = mt_rand( 0, 1023004);
      $song['size' ] = mt_rand( 0, 10230104);       
      $song['bitrate'] = mt_rand( 48, 500 );         
      $song['year'] = mt_rand( 1910, 2038 );          
      $song['track_number'] = floor( 9/$i );  
      $song['label'] = $label;        
      $song['mtime'] = mt_rand( 0, 1023004);         
      $song['atime'] = mt_rand( 0, 1023004);           
      $song['filename'] = uniqid() . uniqid() . uniqid() . uniqid();
       
      $media_scan->add_song( $song );
    }   
  }
}
