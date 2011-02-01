<?php
  $left  = '<div class="buttonwrapper">';
  $left .= '  <div id="artisttomenu" class="headerbutton" onclick="streeme.chooseState( \'card_artists\', \'card_welcome\');">&#9668;&nbsp;' . __( 'Menu' ) . '</div>';
  $left .= '</div>';
  
  $right  = '<div class="buttonwrapper" style="float: left; margin: 8pt 0 8pt 5pt;">';
  $right .= '  <div id="artistsearch" class="headerbutton" onclick="streeme.search(\'artists\');"><img src="' . public_path( 'images/player/mobile/mb-button-search.png' ) . '" /></div>';
  $right .= '</div>';
  $right .= '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_artists\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';
 
  include_partial( 'header', array(
                                'title'=>__( 'Artists' ),
                                'left' => $left, 
                                'right' => $right, 
              ) );
?>
<div class="letterbarcontainer">
  <?php include_partial( 'letterbar', array( 'element' => 'artist' ) )?> 
</div>
<div id="artistlistcontainer"><?php /* list rendered in streeme.js */ ?></div>