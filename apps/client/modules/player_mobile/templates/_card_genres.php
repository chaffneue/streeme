<?php
  $left  = '<div class="buttonwrapper">';
  $left .= '  <div id="genretomenu" class="headerbutton" onclick="streeme.chooseState( \'card_genres\', \'card_welcome\');">&#9668;&nbsp;' . __( 'Menu' ) . '</div>';
  $left .= '</div>';
  
  $right  = '<div class="buttonwrapper" style="float: left; margin: 8pt 0 8pt 5pt;">';
  $right .= '  <div id="artistsearch" class="headerbutton" onclick="streeme.search(\'genres\');"><img src="' . public_path( 'images/player/mobile/mb-button-search.png' ) . '" /></div>';
  $right .= '</div>';
  $right .= '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_genres\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';
 
  include_partial( 'header', array(
                                'title'=>__( 'Genres' ),
                                'left' => $left,
                                'right' => $right,
              ) );
?>
<div id="genrelistcontainer"><?php /* list rendered in streeme.js */ ?></div>