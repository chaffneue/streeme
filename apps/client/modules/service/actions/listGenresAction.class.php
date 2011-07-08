<?php
class listGenresAction extends sfAction
{
  public function execute($request)
  {
    $alpha = $request->getParameter( 'alpha' );
    $songGenresList = Doctrine_Core::getTable('SongGenres')->getList( ( $alpha ) ? $alpha : 'all' );
    $genreList = array();
    foreach( $songGenresList as $row )
    {
      $genreList[] = $row['Genre'];
    }
    $this->content = json_encode($genreList);
    sfConfig::set('sf_web_debug', false);
    $this->setTemplate('output');
    $this->setLayout(false);
  }
}