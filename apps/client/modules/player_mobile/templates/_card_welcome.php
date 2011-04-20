<?php
   include_partial( 'header', array(
                                        'title'=>'Streeme',
                                        'left' =>false,
                                        'right' =>'<a href="' . url_for( '@player_default' ) .'" class="logout buttonradius" style="z-index: 5" id="logout" title="' . __( 'Back to Player Selection' ) . '"></a>',
                     ));
?>
<ul id="welcomescreen">
   <li onclick="streeme.chooseState( 'card_welcome', 'card_artists' );"><?php echo __('Artists') ?></li>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_albums' );"><?php echo __('Albums') ?></li>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_songs' );"><?php echo __('Songs') ?></li>
   <?php if( sfConfig::get( 'app_allow_ffmpeg_transcoding' ) && @$_COOKIE['resume_mobile'] ): ?>
   <li onclick="streeme.choose( 'resume' );"><?php echo __('Resume') ?></li>
   <?php endif ?>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_player' );"><?php echo __('Playing') ?></li>
   <li onclick="streeme.choose( 'newest' );"><?php echo __('Newest Songs') ?></li>
   <li onclick="streeme.choose( 'shuffle' );"><?php echo __('Shuffle All Songs') ?></li>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_genres' );"><?php echo __('Genres') ?></li>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_playlists' );"><?php echo __('Playlists') ?></li>
   <?php if( sfConfig::get( 'app_allow_ffmpeg_transcoding' )): ?>
   <li onclick="streeme.chooseState( 'card_welcome', 'card_settings' );"><?php echo __('Settings') ?></li>
   <?php endif ?>
</ul>