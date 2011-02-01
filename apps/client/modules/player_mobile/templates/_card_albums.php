<?php
  $left  = '<div class="buttonwrapper">';
  $left .= '  <div id="artisttomenu" class="headerbutton" onclick="streeme.chooseState( \'card_albums\', \'card_artists\');">&#9668;&nbsp;' . __( 'Artists' ) . '</div>';
  $left .= '</div>';
  
  $right  = '<div class="buttonwrapper" style="float: left; margin: 8pt 0 8pt 5pt;">';
  $right .= '  <div id="artistsearch" class="headerbutton" onclick="streeme.search(\'albums\');"><img src="' . public_path( 'images/player/mobile/mb-button-search.png' ) . '" /></div>';
  $right .= '</div>';
  $right .= '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_albums\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';

  include_partial( 'header', array(
                                'title'=> __( 'Albums' ),
                                'left' => $left, 
                                'right' =>$right, 
             ) );
?>
<div class="letterbarcontainer">
  <?php include_partial( 'letterbar', array( 'element' => 'album' ) )?> 
</div>
<div id="albumlistcontainer"><?php /* list rendered in streeme.js */ ?></div>