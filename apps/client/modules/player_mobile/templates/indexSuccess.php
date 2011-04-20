<?php
  slot( 'title', __( 'Streeme - Portable Music' ) );
  slot( 'description', __( 'Streeme Player for Mobile Webkit Enabled Phones and devices' ) );
  slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">' );
  use_stylesheet( '/css/player/mobile/stylesheet.css' );
  use_javascript( '/js/jquery-1.4.2.min.js', 'first' );
  use_javascript( '/js/jquery.dataTables.min.js' );
  use_javascript( '/js/jquery.cookie.min.js' );
  use_javascript( '/js/jquery.md5.min.js' );
  use_javascript( '/js/player/mobile/streeme.js' );
?>
<div id="container">
  <div class="card_default card_welcome" id="card_welcome">
    <?php include_partial( 'card_welcome' ) ?>
  </div>
  <div class="card_default card_artists" id="card_artists">
    <?php include_partial( 'card_artists' ) ?>
  </div>
  <div class="card_default card_albums" id="card_albums">
    <?php include_partial( 'card_albums' ) ?>
  </div>
  <div class="card_default card_songs" id="card_songs">
    <?php include_partial( 'card_songs' ) ?>
  </div>
  <div class="card_default card_player" id="card_player">
    <?php include_partial( 'card_player' ) ?>
  </div>
  <div class="card_default card_playlists" id="card_playlists">
    <?php include_partial( 'card_playlists' ) ?>
  </div>
  <div class="card_default card_genres" id="card_genres">
    <?php include_partial( 'card_genres' ) ?>
  </div>
  <div class="card_default card_settings" id="card_settings">
    <?php include_partial( 'card_settings' )?>
  </div>
</div>
<?php include_partial( 'load_javascript', array( 'music_proxy_port' => $music_proxy_port, 'namespace' => $this->getModuleName() . $this->getActionName() ) ); ?>