<?php
#
# Add a Playlist
#
class addPlaylistAction extends sfAction
{
  public function execute($request)
  {
		//validate required fields
		$name = $request->getParameter( 'name' );
    if( !isset( $name ) || empty( $name ) ) $this->forward404();

    //add playlist
    Doctrine_Core::getTable('Playlist')->addPlaylist( $name );
		return sfView::NONE;
  }
}