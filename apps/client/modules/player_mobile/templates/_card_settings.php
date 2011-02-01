<?php
  $left  = '<div class="buttonwrapper">';
  $left .= '  <div id="settingstomenu" class="headerbutton" onclick="streeme.chooseState( \'card_settings\', \'card_welcome\');">&#9668;&nbsp;' . __( 'Menu' ) . '</div>';
  $left .= '</div>';
  
  $right  = '<div class="buttonwrapper" style="float: left; margin: 8pt 0 8pt 5pt;">';
  $right .= '  <div id="songsearch" class="headerbutton" onclick="streeme.search(\'songs\');"><img src="' . public_path( 'images/player/mobile/mb-button-search.png' ) . '" /></div>';
  $right .= '</div>';
  $right .= '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_settings\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';
 
  include_partial( 'header', array(
                                'title'=>__( 'Settings' ),
                                'left' => $left, 
                                'right' => $right, 
              ) );
?>
<div id="settingscontainer">
	<div class="formtitle"><?php echo __( 'Streeme Settings' ) ?></div>
	<div class="horizontalrule"></div>
	<form onsubmit="return false">
	   <label for="bitrateselector"><?php echo __( 'Target bitrate:') ?> </label>
	   <select id="bitrateselector" name="bitrateselector">
	      <option value="0"><?php echo __( 'Play original' ) ?></option>
	      <option value="auto"><?php echo __( 'Auto' ) ?></option>
	      <option value="384"><?php echo __( '384kbps' ) ?></option> 
	      <option value="320"><?php echo __( '320kbps' ) ?></option> 
	      <option value="256"><?php echo __( '256kbps' ) ?></option> 
	      <option value="192"><?php echo __( '192kbps' ) ?></option> 
	      <option value="128"><?php echo __( '128kbps' ) ?></option> 
	      <option value="96"><?php echo __( '96kbps' ) ?></option> 
	      <option value="48"><?php echo __( '48kbps' ) ?></option>
	      <option value="32"><?php echo __( '32kbps' ) ?></option>
	   </select>
	   <br/><br/>
	   <label for="formatselector"><?php echo __( 'Target format:') ?> </label>
	   <select id="formatselector" name="formatselector">
	      <option value="0"><?php echo __( 'None' ) ?></option>
	      <option value="mp3"><?php echo __( 'MP3' ) ?></option>
	      <option value="ogg"><?php echo __( 'OGG' ) ?></option>
	   </select>
	</form>
</div>