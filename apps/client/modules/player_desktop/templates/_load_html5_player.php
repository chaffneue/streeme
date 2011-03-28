<?php
#
# Loading an appropriate HTML5 player will change as the standard gets better, add cases here
#

//Chrome 10 has serious vido playback bugs, go back to the audio tag for now 
//chromium bug report: http://code.google.com/p/chromium/issues/detail?id=73458 
if( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Chrome' ))
{
  echo '<audio preload="none" controls="" id="musicplayer" class="chrome"></audio>' . "\r\n";
}

//Safari can use audio just fine on the mac..the player looks quite different
else if( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Safari' ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Macintosh' ))
{
  echo '<audio preload="none" controls="" id="musicplayer" class="safari-mac"></audio>' . "\r\n";
}

//iPad can use audio just fine as well..the player looks quite different again
else if( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Safari' ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'iPad; U' ))
{
  echo '<audio preload="none" controls="" id="musicplayer" class="safari-ipad"></audio>' . "\r\n";
}

//firefox and opera lack support for HTML5 MP3 codec playback,
//so we'll use jPlayer and get support from flash (sigh)
else if(
         strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Firefox' )
         ||
         strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'pera/' )
        )
{
  use_stylesheet( '/css/jPlayer.Skin/jplayer.blue.monday.css');
  use_javascript( '/js/jQuery.jPlayer.2.0.0/jquery.jplayer.min.js' );
  
  //macs have slightly different font variants. add template tag to adjust line height.
  $if_mac = strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Mac OS X' ) ? 'style="top:8px"' : '';
  $if_operawindows = ( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'pera/' ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Windows' )   ) ? 'style="top:5px"' : '';
  
  echo <<<EOL
<div id="jquery_jplayer_1" class="jp-jplayer"></div>
<div class="jp-audio">
  <div class="jp-type-single">
    <div id="jp_interface_1" class="jp-interface">
      <ul class="jp-controls">
        <li><a href="#" class="jp-play" tabindex="1">play</a></li>
        <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
      </ul>
      <div class="jp-bumper-left"></div>
      <div class="jp-seekcontainer">
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
      </div>
      <div class="jp-bumper-right"></div>
      <div class="jp-current-time" {$if_mac}{$if_operawindows}></div>
      <ul class="jp-controls">
        <li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
        <li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
      </ul>
      <div class="jp-volume-bar">
        <div class="jp-volume-bar-value"></div>
      </div>
    </div>
  </div>
</div>
EOL;

$jplayer_player_loader = <<<EOL
$("#jquery_jplayer_1").jPlayer({
                                swfPath: "/js/jQuery.jPlayer.2.0.0",
                                volume: 1
                              });
EOL;

slot( 'javascript_player_loader', $jplayer_player_loader );
}

//internet explorer has it's own interesting quirks with ogg support - same goes for Safari Windows
else if(
         strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Trident' )
         ||
         strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Safari' ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Windows' )
       )
{
  use_stylesheet( '/css/jPlayer.Skin/jplayer.blue.monday.css');
  use_javascript( '/js/jQuery.jPlayer.2.0.0/jquery.jplayer.min.js' );
  echo <<<EOL
<div id="jquery_jplayer_1" class="jp-jplayer"></div>
<div class="jp-audio">
  <div class="jp-type-single">
    <div id="jp_interface_1" class="jp-interface">
      <ul class="jp-controls">
        <li><a href="#" class="jp-play" tabindex="1">play</a></li>
        <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
      </ul>
      <div class="jp-bumper-left"></div>
      <div class="jp-seekcontainer">
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
      </div>
      <div class="jp-bumper-right"></div>
      <div class="jp-current-time" style="top: 6px;"></div>
      <ul class="jp-controls">
        <li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
        <li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
      </ul>
      <div class="jp-volume-bar">
        <div class="jp-volume-bar-value"></div>
      </div>
    </div>
  </div>
</div>
EOL;

$jplayer_player_loader = <<<EOL
$("#jquery_jplayer_1").jPlayer({
                                swfPath: "/js/jQuery.jPlayer.2.0.0",
                                volume: 1
                              });
EOL;

slot( 'javascript_player_loader', $jplayer_player_loader );
slot( 'disable_ogg_transcoding', true );
}

//audio for all other HTML5 implementations
else
{
  echo '<audio preload="none" controls="" id="musicplayer"></audio>';
}
?>