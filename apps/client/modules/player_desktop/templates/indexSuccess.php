<?php
  slot( 'title', __( 'Streeme - Portable Music' ) );
  slot( 'description', __( 'Streeme Player for the Desktop' ) );
  use_stylesheet( '/css/colorbox/colorbox.css' );
  use_stylesheet( '/css/player/desktop/stylesheet.css' );
  use_javascript( '/js/jquery-1.4.2.min.js' );
  use_javascript( '/js/jquery.dataTables.min.js' );
  use_javascript( '/js/jquery.scrollTo.min.js' );
  use_javascript( '/js/jquery.cookie.min.js' );
  use_javascript( '/js/jquery.md5.min.js' );
  use_javascript( '/js/jquery.colorbox.min.js' );
  use_javascript( '/js/player/desktop/streeme.js' );
?>
<div id="container">
  <div class="header" id="header">
    <div class="songcontrols lightgradient" id="songcontrols">
      <div class="dropzone ui-droppable buttonradius" id="dropzone"><?php echo __( 'Added item to playlist' ) ?></div>
 
      <div class="playlists buttonradius" id="playlists" title="<?php echo __( 'View playlists' ) ?>"></div>
 
      <?php if( sfConfig::get( 'app_allow_ffmpeg_transcoding' )): ?>
        <div class="settings buttonradius" id="settings" title="<?php echo __( 'Change settings' ) ?>"></div>
      <?php endif; ?>
      
      <?php if( sfConfig::get( 'app_allow_ffmpeg_transcoding' ) && $_COOKIE['resume_desktop'] ): ?>
        <div class="resume buttonradius" id="resume" title="<?php echo __('Resume') ?>"></div>
      <?php endif ?>
      
      <a href="<?php echo url_for( '@player_default' ) ?>" class="logout buttonradius" id="logout" title="<?php echo __( 'Back to Player Selection' ) ?>"></a>

      <div id="albumart" onmouseover="$( '#magnify_art' ).show()" onmouseout="$( '#magnify_art' ).hide()">
        <img src="<?php echo public_path( 'images/player/common/streeme-intro-album-art-medium.jpg', true ); ?>" class="albumimg" alt="<?php echo __( 'Welcome to Streeme' ) ?>" title="<?php echo __( 'Welcome to Streeme' ) ?>" />
      </div>
      <div id="songtitle"><?php echo __( 'Double click any song to play' ) ?></div>
      <div id="transport" class="transport">
        <div class="videocontainer buttonradius">
          <?php include_partial( 'load_html5_player', array() )?>
        </div>
        <div class="previoussongdisabled textindent" id="previous" title="<?php echo __( 'Previous Track' ) ?>"></div>
        <div class="nextsongdisabled textindent" id="next" title="<?php echo __( 'Next Track' ) ?>"></div>
        <div class="randomsong textindent" id="random" title="<?php echo __( 'Random Play On/Off' ) ?>"></div>
      </div>
    </div>
  </div>
  <div class="content clearfix" id="content">
    <div class="columnleft" id="columnleft">
      <div class="browsecontainer"> 
        <?php include_partial( 'genre_browse', array( 'title'=>'Genres', 'element_id' => 'browsegenre', 'list' => $genre_list ) ) ?>
        <?php include_partial( 'library_browse', array( 'title'=>'Artists', 'element_id' => 'browseartist', 'list_template' => 'list_artists', 'list' => $artist_list ) ) ?>
        <?php include_partial( 'library_browse', array( 'title'=>'Albums', 'element_id'=>'browsealbum', 'list_template' => 'list_albums', 'list' => $album_list ) ) ?>
      </div>
    </div>
    <div class="columnright" id="columnright">
      <div class="songlistcontainer" id="songlistcontainer">
        <?php include_partial( 'list_songs' ); ?>
      </div>
    </div>
  </div>
</div>

<div class="playlistsmodalwindow wideradius" id="playlistsmodalwindow">
  <div class="browse">
    <div class="listcontainer" id="playlistcontainer">
      <?php include_partial( 'list_playlists', array( 'element_id' => 'browseplaylist', 'list' => $playlist_list ) ) ?>
    </div>
  </div>
  <div class="addplaylist"><button id="addplaylist" name="addplaylist"><?php echo __( 'Add New Playlist&hellip;' ) ?></button></div>
</div>

<?php if( sfConfig::get( 'app_allow_ffmpeg_transcoding' )): ?>
<div class="settingsmodalwindow wideradius" id="settingsmodalwindow">
  <?php include_partial( 'settings' )?>
</div>
<?php endif ?>

<div class="magnify_art" id="magnify_art" title="<?php echo __( 'Magnify album art' ) ?>"></div>

<?php include_partial( 'load_javascript', array( 'music_proxy_port' => $music_proxy_port ) ) ?>
