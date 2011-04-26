<?php
class mediafillTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption( 'application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'client' ),
      new sfCommandOption( 'env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'test' ),
      new sfCommandOption( 'connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine' ),
      new sfCommandOption( 'count', null, sfCommandOption::PARAMETER_REQUIRED, 'The number of fake records to add', 7500),
      new sfCommandOption( 'max_album_size', null, sfCommandOption::PARAMETER_REQUIRED, 'The largest number of songs to add to an album', 24),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'proceed without prompts')
    ));

    $this->namespace        = '';
    $this->name             = 'media-fill';
    $this->briefDescription = 'Fill the library with records';
    $this->detailedDescription = <<<EOF
The [media-fill|INFO] task will add a specified number of records in a semi
random order to help benchmark Streeme's song list performance on small and
large music libraries.

Types:
  --count=[int]         - The number of records to add
  --max_album_size=[int]- the maximum number of member song in an album

Call it with:

  [php symfony media-fill --count="..."|INFO]
EOF;
  }

  protected function execute( $arguments = array(), $options = array() )
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : 'all';
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $media_scanner = new MediaScan();
    
    if (
      !$options['no-confirmation']
      &&
      !$this->askConfirmation(array_merge(
        array(sprintf('This command will append data in the following "%s" connection(s):', $environment), ''),
        array('', 'Are you sure you want to proceed? (y/N)')
      ), 'QUESTION_LARGE', false)
    )
    {
      exit;
    }
    
    $counter=1;
    for( $i=0; $i < (int) $options['count']; $i++ )
    {
      if($counter >= $group_size)
      {
        $artist_name = substr( sha1(uniqid().mt_rand(29098,209120392093029)), 0, mt_rand(4, 12));
        $album_name = substr( sha1(uniqid().mt_rand(3048,209120393)), 0, mt_rand(6,16));
        $genre_name = substr( sha1(uniqid().mt_rand(30203,2091029309213)), 0, mt_rand(10,12));
        $label_name = substr( sha1(uniqid().mt_rand(30203,2091029309213)), 0, mt_rand(10,12));
        $group_size = mt_rand(1,(int) $options['max_album_size']);
        $bitrate = mt_rand(48,512);
        $yearpublished = mt_rand(1900,2069);
        $counter = 1;
      }
      
      $song_name = substr( sha1(uniqid().mt_rand(360,20912039209213)), 0, mt_rand(10,20));
      $song_array = array();
      @$song_array[ 'artist_name' ]      = $artist_name;
      @$song_array[ 'album_name' ]       = $album_name;
      @$song_array[ 'song_name' ]        = $song_name;
      @$song_array[ 'song_length' ]      = mt_rand(0,60) . ':' . mt_rand(11,60);
      @$song_array[ 'accurate_length' ]  = mt_rand(1,30409092);
      @$song_array[ 'genre_name' ]       = $genre_name;
      @$song_array[ 'filesize' ]         = mt_rand(256, 3141592653);
      @$song_array[ 'bitrate' ]          = $bitrate;
      @$song_array[ 'yearpublished' ]    = $yearpublished;
      @$song_array[ 'tracknumber']       = $counter;
      @$song_array[ 'label' ]            = $label_name;
      @$song_array[ 'mtime' ]            = mt_rand(1, 3141592653);
      @$song_array[ 'atime' ]            = mt_rand(1, 3141592653);
      @$song_array[ 'filename' ]         = 'file://localhost/home/user/' . $artist_name . '/' . $album_name . '/' . $song_name . '.mp3';
      
      $media_scanner->add_song( $song_array );
      
      $counter++;
    }
    echo sprintf('Filled Database %s with %d record%s', $options['env'], $i, ($i==1) ? '' : 's' );
    echo "\r\n";
  }
}
