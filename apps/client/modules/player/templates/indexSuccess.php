<?php 
  slot( 'title', __( 'Streeme: Welcome - Control Panel' ) );
  slot( 'description', __( 'Continue to a player or change global application settings' ) );
  slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.80, maximum-scale=1.0, user-scalable=no">' );
  use_stylesheet( '/css/player/stylesheet.css' );
  use_javascript( '/js/jquery-1.4.2.min.js' );
?>
<div class="title"><?php echo __( 'Welcome to Streeme' ); ?></div>
<div class="buttoncontainer">
  <ul class="buttons">
    <li><a href="<?php echo url_for( '@player_desktop_default' ) ?>" class="button desktop" id="desktop" ><?php echo __( 'Desktop Player' ) ?></a></li>
    <li><a href="<?php echo url_for( '@player_mobile_default' ) ?>" class="button mobile" id="mobile" ><?php echo __( 'Mobile Player' ) ?></a></li>
    <li><a href="<?php echo url_for( '@player_settings_default' ) ?>" class="button settings" id="settings" ><?php echo __( 'Global Settings' ) ?></a></li>
    <li><a href="<?php echo url_for( '@sf_guard_signout' ) ?>" class="button logout" id="logout" ><?php echo __( 'Sign Off' ) ?></a></li>
  </ul>
  <div class="text" id="button_text"></div>
</div>
<!--
<div class="remember">
  <input type="checkbox">
  <span class="remember_player"><?php echo __( 'Remember my player choice' )?></span>
</div>
-->
<script type="text/javascript">
  $( document ).ready
  (
    function()
    {
      $( '#desktop' ).mouseover( function(){ set_text( '<?php echo __( 'Desktop' ) ?>' ) } );
      $( '#mobile' ).mouseover( function(){ set_text( '<?php echo __( 'Mobile' ) ?>' ) } );
      $( '#settings' ).mouseover( function(){ set_text( '<?php echo __( 'Settings' ) ?>' ) } );
      $( '#logout' ).mouseover( function(){ set_text( '<?php echo __( 'Sign Off' ) ?>' ) } );     
      $( '#desktop' ).mouseout( function(){ unset_text() } );
      $( '#mobile' ).mouseout( function(){ unset_text() } );
      $( '#settings' ).mouseout( function(){ unset_text() } );
      $( '#logout' ).mouseout( function(){ unset_text() } );      
    }
  );
  function set_text( text )
  { 
    $( '#button_text' ).text( text );
    $( '#button_text' ).stop(true, true).show(120);
  }
  function unset_text( text )
  { 
    $( '#button_text' ).stop(true, true).hide(50);
  }
</script>