<?php
#
# Gets and displays a list of playlists
#
echo '<ol class="' . $element_id . '">';
foreach ( $list as $k => $v )
{
  echo '  <li id="plli' . $v['id'] . '">';
  echo '    <a href="#" onclick="streeme.setActivePlaylist(' . $v['id'] .'); return false;" ondblclick="streeme.choosePlaylist(' . $v['id'] .'); return false;">' . $v['name'] . '</a>';
  echo '    <div class="playplaylistbutton" onclick="streeme.choosePlaylist(' . $v['id'] .')" title="' . __( 'Play this playlist' ) . '"></div>';
  echo '    <div class="ejectplaylistbutton" onclick="streeme.clearSearch()" title="' . __( 'Unload this playlist' ) . '"></div>';
  echo '    <div class="deleteplaylistbutton" onclick="streeme.deletePlaylist(' . $v['id'] .')" title="' . __( 'Delete this playlist' ) . '"></div>';
  echo '  </li>';
}
echo '</ol>';
?>