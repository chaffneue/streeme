<?php
#
# The Settings Flyout
#
?>
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
      <?php 
        $disable_mp3_transcoding = get_slot( 'disable_mp3_transcoding', false );
        if ( !$disable_mp3_transcoding )
        {
          echo '<option value="mp3">' . __( 'MP3' ) . '</option>';
        }
        
        $disable_ogg_transcoding = get_slot( 'disable_ogg_transcoding', false );
        if ( !$disable_ogg_transcoding )
        {
          echo '<option value="ogg">' . __( 'OGG' ) . '</option>';
        }
      ?>
   </select>
</form>