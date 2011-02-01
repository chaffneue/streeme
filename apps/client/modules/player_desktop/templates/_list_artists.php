<?php
#
# Gets and displays a list of artists in an ordered list
#
  echo '<ol id="' . $element_id . '">';
  $current_alpha = null;
  foreach ( $list as $k => $v )
  {
    $alpha = strtolower( substr( $v['name'], 0 , 1 ) );
    if ( $alpha != $current_alpha )
    {
      $alpha_id = 'id="' . $prefix . ( (is_numeric($alpha) ) ? '123' : $alpha ) . '"'; 
      $current_alpha = $alpha;
    } 
    else 
    {  
      $alpha_id = '';
    }
    echo '<li ' . $alpha_id . '>';
    echo '  <div class="ap" onclick="streeme.addpls( \'artist\', \'' . $v['id'] . '\'  )"></div>';
    echo '  <a href="#" onclick="streeme.chooseArtist(' . $v['id'] .'); return false;">' . $v['name'] . '</a>';
    echo '</li>';
  }
  echo '</ol>';
?>