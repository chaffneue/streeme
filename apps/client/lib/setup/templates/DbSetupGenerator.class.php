<?php
/**
 * Create the databases.yml file from a web setup form
 *
 * @author Richard Hoar
 * @package streeme
 */
class DbSetupGenerator
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
   * @param file    str: the fully qualified databases.yml file path
   * @param type    str: the database type to use [sqlite, mysql, postgres]
   * @param options arr: the form values posted on the db setup form
   */
  public function create($file, $type, array $options)
  {
    $data = array();
    switch($type)
    {
      case 'sqlite':
        $this->log('Generating config file for SQLite DB');
        $data = $this->createSqliteSchema($options);
      break;
      
      case 'postgres':
        $this->log('Generating config file for Postgresql DB');
        $data = $this->createPostgresSchema($options);
      break;
      
      case 'mysql':
        $this->log('Generating config file for Mysql DB');
        $data = $this->createMysqlSchema($options);
      break;
    }
    
    $yml_text = $this->yaml->dump($data,20,0);
    umask(0000);
    return((file_put_contents($file, $yml_text, 0666)) ? true : false);
  }
  
  /**
   * Reload the Database after changing configs
   *
   * @return bol: true on success
   */
  public function reloadDb()
  {
    $results_clear = $results_dbsetup = array();
    exec(sprintf('cd %s && %s cc', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony')), $results_clear);
    exec(sprintf('cd %s && %s doctrine:build --all --and-load --no-confirmation', escapeshellarg(sfConfig::get('sf_root_dir')), escapeshellcmd(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony')), $results_dbsetup);
    $results = array_merge($results_clear, $results_dbsetup);
    foreach ($results as $value)
    {
      $this->log($value);
    }
    $val = array_pop($results);
    return (strpos($val, 'Data was successfully loaded')) ? true : false;
  }
  
  /**
   * Create a sqlite databases yml file represented as a valid php array
   *
   * @param options arr: the options from the user's form
   * @return        arr: an output array that is compatible with the databases.yml format
   */
  private function createSqliteSchema($options)
  {
    return array(
      'test' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'sqlite:'. $options['database_path'] . DIRECTORY_SEPARATOR . $options['database_name'] . 'test.db',
            'encoding' => 'utf8',
          )
        )
      ),
 
      'all' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'sqlite:'. $options['database_path'] . DIRECTORY_SEPARATOR . $options['database_name'] . '.db',
            'encoding' => 'utf8',
            'profiler' => false,
          )
        )
      )
    );
  }
  
  /**
   * Create a sqlite databases yml file represented as a valid php array
   *
   * @param options arr: the options from the user's form
   * @return        arr: an output array that is compatible with the databases.yml format
   */
  private function createMysqlSchema($options)
  {
    return array(
      'test' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'mysql:host='.$options['database_host'].';port='.$options['database_port'].';dbname='. $options['database_name'] .'test',
            'username' => $options['database_username'],
            'password' => $options['database_password'],
            'encoding' => 'utf8',
            'attributes' => array(
              'default_table_collate' => 'utf8_general_ci',
              'default_table_charset' => 'utf8'
            )
          )
        )
      ),
 
      'all' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'mysql:host='.$options['database_host'].';port='.$options['database_port'].';dbname='. $options['database_name'],
            'username' => $options['database_username'],
            'password' => $options['database_password'],
            'encoding' => 'utf8',
            'profiler' => false,
            'attributes' => array(
              'default_table_collate' => 'utf8_general_ci',
              'default_table_charset' => 'utf8'
            )
          )
        )
      )
    );
  }
  
  /**
   * Create a postgres databases yml file represented as a valid php array
   *
   * @param options arr: the options from the user's form
   * @return        arr: an output array that is compatible with the databases.yml format
   */
  private function createPostgresSchema($options)
  {
    return array(
      'test' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'pgsql:host='.$options['database_host'].';port='.$options['database_pg_port'].';dbname='. $options['database_name'] .'test',
            'username' => $options['database_username'],
            'password' => $options['database_password'],
            'encoding' => 'utf8',
            'attributes' => array(
              'default_table_collate' => 'utf8_general_ci',
              'default_table_charset' => 'utf8'
            )
          )
        )
      ),
 
      'all' => array(
        'doctrine' => array(
          'class' => 'sfDoctrineDatabase',
          'param' => array(
            'dsn' => 'pgsql:host='.$options['database_host'].';port='.$options['database_pg_port'].';dbname='. $options['database_name'],
            'username' => $options['database_username'],
            'password' => $options['database_password'],
            'encoding' => 'utf8',
            'profiler' => false,
            'attributes' => array(
              'default_table_collate' => 'utf8_general_ci',
              'default_table_charset' => 'utf8'
            )
          )
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
    sfContext::getInstance()->getLogger()->log(sprintf('{WebDatabaseSetupForm} %s', $message), $level);
  }
}