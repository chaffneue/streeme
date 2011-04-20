<?php
#
# Delete a Playlist
#
class deletePlaylistAction extends sfAction
{
  public function execute($request)
  {
		//validate required fields
		$playlist_id = $request->getParameter( 'playlist_id' );
    if( !isset( $playlist_id ) || empty( $playlist_id ) ) $this->forward404();

    //delete playlist entry and all associated content
    Doctrine_Core::getTable('Playlist')->deletePlaylist( $request->getParameter( 'playlist_id' ) );
		return sfView::NONE;
  }
}