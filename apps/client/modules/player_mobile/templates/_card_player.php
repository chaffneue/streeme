<?php
  $left  = '<div class="buttonwrapper">';
  $left .= '  <div id="artisttomenu" class="headerbutton" onclick="streeme.chooseState( \'card_player\', \'card_songs\');">&#9668;&nbsp;' . __( 'Songs' ) . '</div>';
  $left .= '</div>';
  
  $right = '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_player\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';
  
  include_partial( 'header', array(
                                'title'=> __( 'Player' ),
                                'left' => $left,
                                'right' =>$right,
             ) );
?>
<div class="transport">
  <div id="albumart"></div>
  <div id="songtitle"><?php echo __('No Song Selected') ?></div>
  <div class="videocontainer buttonradius">
      <audio preload="none" controls="" id="musicplayer"></audio>
  </div>
  <div class="previoussongdisabled textindent" id="previous" title="<?php echo __('Previous Track')?>"></div>
  <div class="nextsongdisabled textindent" id="next" title="<?php echo __('Next Track')?>"></div>
</div>
<div style="height: 160px;"></div>