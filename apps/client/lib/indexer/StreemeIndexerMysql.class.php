<?php
/**
 * Mysql fulltext search index provider connector.
 *
 * @author Richard Hoar
 * @package Streeme
 * @depends mysql 5.1+, sfDoctrinePlugin
 */
class StreemeIndexerMysql extends StreemeIndexerBase
{
  protected $dbh;
  
  public function __construct()
  {
    $this->dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
  }
  
  /**
   * Attempt to raise the service
   *
   * @return bol: true on success
   */
  public function bootstrapService()
  {
    return true;
  }

  /**
   * Prepare the database before the index update process begins
   *
   * @return            bol: true on success
   */
  public function prepare()
  {
    $this->dbh->exec('TRUNCATE TABLE indexer');
    return true;
  }
  
  /**
   * Add a document to the index
   *
   * @param unique_id   str: the song's unique id
   * @param song_name   str: song name
   * @param artist_name str: artist name
   * @param artist_name str: album name
   * @param genre_name  str: genre name
   * @param tags        str: any other tags/words to describe the track
   * @return            bol: true on success
   */
  public function doAddDocument($unique_id, $song_name, $artist_name, $album_name, $genre_name, $tags=null)
  {
    $query = 'INSERT INTO indexer set sfl_guid=:id, i=:terms';
    $parameters['id'] = $unique_id;
    $parameters['terms'] = sprintf('%s %s %s %s %s', $song_name, $artist_name, $album_name, $genre_name, $tags);

    $stmt = $this->dbh->prepare( $query );
    $success = $stmt->execute( $parameters );
    if( $success )
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Pre transaction script
   *
   * @return          bol: true on success
   */
  public function preTransaction()
  {
    //non transactional batch entry
    return true;
  }
  
  /**
   * Post transaction script
   *
   * @return          bol: true on success
   */
  public function postTransaction()
  {
    //non transactional batch entry
    return true;
  }
  
  /**
   * Abort transaction
   *
   * @return          bol: true on success
   */
  public function rollbackTransaction()
  {
    //non transactional batch entry
    return true;
  }
    
  /**
   * destroy session / clean up stray data commits
   *
   * @return          bol: true on success
   */
  public function flush()
  {
    //handled by the music search service
    return true;
  }
  
  /**
   * get unique key list by keywords
   *
   * @param keywords    str: a user's search terms
   * @param limit       int: limit the resultset to the number specified
   * @param idFieldName str: the canonical id fieldname for the song's unique id
   * @return            arr: a list of matching keys found by fulltext search
   */
  public function getKeys($keywords, $limit = 100, $idFieldName = 'sfl_guid')
  {
    $parameters = array();
    $keywords = sprintf('*%s*', $keywords);
    $query  = 'SELECT ';
    $query .= sprintf(' %s ', $idFieldName);
    $query .= 'FROM ';
    $query .= ' indexer ';
    $query .= 'WHERE ';
    $query .= ' MATCH(i) AGAINST(:query_term IN BOOLEAN MODE) ';
    $query .= 'LIMIT ';
    $query .= (int) $limit;
   
    $parameters['query_term'] = $keywords;

    $stmt = $this->dbh->prepare( $query );
    $success = $stmt->execute( $parameters );
    if( $success )
    {
      return array_map(array($this, 'valuesMap'), $stmt->fetchAll(Doctrine::FETCH_ASSOC));
    }
    else
    {
      return array();
    }
  }
  
  public function valuesMap($arr)
  {
    $t = array_values($arr);
    return $t[0];
  }
}