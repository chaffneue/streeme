<?php
/**
 * The base search index provider API. 
 * 
 * @author Richard Hoar
 * @package Streeme
 */
abstract class StreemeIndexerBase
{
  /**
   * enforce method signature on construction
   */ 
  abstract public function __construct();
  
  /**
   * Attempt to raise the service 
   * 
   * @return bol: true on success 
   */
  abstract public function bootstrapService();

  /**
   * Prepare the database before the index update process begins
   * 
   * @return            bol: true on success
   */
  abstract public function prepare(); 
  
  /**
   * Add/Overwrite a document to the index
   * 
   * @param unique_id   str: the song's unique id
   * @param song_name   str: song name
   * @param artist_name str: artist name 
   * @param artist_name str: album name 
   * @param genre_name  str: genre name
   * @param tags        str: any other tags/words to describe the track
   * @return            bol: true on success
   */
  abstract public function doAddDocument($unique_id, $song_name, $artist_name, $album_name, $genre_name, $tags=null);

  /**
   * Pre transaction script
   * 
   * @return          bol: true on success
   */
  abstract public function preTransaction();
  
  /**
   * Post transaction script 
   * 
   * @return          bol: true on success
   */
  abstract public function postTransaction();
  
  /**
   * Abort transaction 
   * 
   * @return          bol: true on success
   */
  abstract public function rollbackTransaction();
  
  /**
   * destroy session / clean up stray data commits
   * 
   * @return          bol: true on success
   */
  abstract public function flush();
  
  /**
   * get unique key list by keywords
   * 
   * @param keywords    str: a user's search terms
   * @param limit       int: limit the resultset to the number specified
   * @param idFieldName str: the canonical id fieldname 
   * @return            arr: a list of matching keys found by fulltext search
   */
  abstract public function getKeys($keywords, $limit = 100, $idFieldName = 'sfl_guid');
  
  /**
   * Get the relevant song unique keys from the index
   * 
   * @param unique_id   str: the song's unique id
   * @param song_name   str: song name
   * @param artist_name str: artist name 
   * @param artist_name str: album name 
   * @param genre_name  str: genre name
   * @param tags        str: any other tags/words to describe the track (default null)
   * @return            bol: true on success
   */
  public function addDocument($unique_id, $song_name, $artist_name, $album_name, $genre_name, $tags=null)
  {
    if($this->preTransaction())
    {
      if($this->doAddDocument($unique_id, $song_name, $artist_name, $album_name, $genre_name, $tags=null))
      {
        if($this->postTransaction())
        {
          return true;
        }
      }
    }
    
    $this->rollbackTransaction();
    return false;
  }
}