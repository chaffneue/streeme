<?php
/**
 * Create the apps/client/config/app.yml file from a web setup form
 *
 * @author Richard Hoar
 * @package streeme
 */
class ApplicationSetupGenerator
{
  /**
   * Constructor.
   *
   * @param yml
   */
  public function __construct(sfYamlDumper $yaml)
  {
    $this->yaml = $yaml;
  }
  
  /**
   * Create a yaml file for a given database in the path specified
   *
   * @param file    str: the fully qualified app.yml file path
   * @param options arr: the form values posted on the db setup form
   * @return        bol: true on success
   */
  public function create($file, array $options)
  {
    $this->log('Generating config file for the applicaiton');
    $data = $this->createApplicationSchema($options);
    $yml_text = $this->yaml->dump($data,20,0);
    
    return((file_put_contents($file, $yml_text)) ? true : false);
  }
  
  /**
   * Do the required cleanup steps once the app.yml is generated
   *
   * @return        bol: true on completion
   */
  public function reloadApplication()
  {
    exec(sprintf('cd %s && %s cc', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony')), $results);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    
    return true;
  }
  
  /**
   * Deploy the indexer tables in mysql
   *
   * @return        bol: true on success
   */
  public function bootstrapIndexer()
  {
    exec(sprintf('cd %s && %s mysql initialize --no-confirmation', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony')), $results);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    $val = array_pop($results);
    
    return (strpos($val, 'Finished!')) ? true : false;
  }
  
  /**
   * Run a media scan to import media into streeme
   *
   * @return        bol: true on completion
   */
  public function runMediaScan()
  {
    exec(sprintf('cd %s && %s schedule-scan', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony')), $results);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    $val = array_pop($results);
    
    return (strpos($val, 'Finished!')) ? true : false;
  }
  
  /**
   * Create an application yml file represented as a valid php array
   *
   * @param options arr: the options from the user's form
   * @return        arr: an output array that is compatible with the app.yml format
   */
  private function createApplicationSchema($options)
  {
    $watched_folders = explode("\r\n", $options['library_paths']);
    $media_scan_plan = array(
      'scan-media --type=' . ((isset($options['library_type']) && $options['library_type'] === '1') ? 'itunes' : 'filesystem'),
      'scan-art --source=meta',
    );
    $allowed_ips = explode("\r\n", $options['allowed_ips']);
    if(isset($options['library_type']) && $options['library_type'] === '1')
    {
      $media_scan_plan[] = 'scan-playlists --type=itunes';
    }
    
    return array(
      'all' => array(
        'setup_library_type' => @$options['library_type'],
        'itunes_xml_location' => @$options['itunes_path'],
        'wf' => array(
          'watched_folders' => $watched_folders,
        ),
        'aft' => array(
          'allowed_file_types' => array(
            'mp3',
          ),
        ),
        'msp' => array(
          'media_scan_plan' => $media_scan_plan,
        ),
        'results_per_page' => (int) @$options['songs_per_page'],
        'allow_ffmpeg_transcoding' => (isset($options['transcoding']) && @$options['transcoding'] === 'yes') ? true : false,
        'ffmpeg_executable' => @$options['ffmpeg_path'],
        'music_proxy_port' => 8096,
        'send_cookies_with_request' => (isset($options['send_cookies']) && @$options['send_cookies'] === 'yes') ? true : false,
        'sf_guard_plugin' => array(
          'remember_key_expiration_age' => 25920000,
        ),
        'indexer' => array(
          'use_indexer' => @$options['database_indexing'],
          'settings' => array(
            'class' => 'StreemeIndexerMysql',
            'auto-start' => true,
          )
        ),
        'setup_acl' => array(
          'allowed_ips' => $allowed_ips,
        )
      )
    );
  }
  
  /**
   * Log a message
   *
   * @param message str: the message text
   * @param level   str: the sfLogger constant associated with the alert level
   */
  private function log($message, $level = sfLogger::DEBUG)
  {
    sfContext::getInstance()->getLogger()->log(sprintf('{WebApplicationSetupForm} %s', $message), $level);
  }
}