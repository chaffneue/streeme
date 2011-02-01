<?php
#
# Delete Content from the user's playlists 
#
class deletePlaylistContentAction extends sfAction
{
  public function execute($request)
  {
		//validate required fields
    if ( $request->getParameter( 'playlist_id' ) == 'false' ) $this->forward404();

    //delete playlist content
		Doctrine_Core::getTable('PlaylistFiles')->deletePlaylistFile( $request->getParameter( 'playlist_id' ), $request->getParameter( 'id' ) );
		exit;
  }
}