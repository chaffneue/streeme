<?php
#
# Loading an appropriate HTML5 player will change as the standard gets better, add cases here
#
if( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'Android' ))
{
  echo '<video preload="none" id="musicplayer" class="android" height="30" width="360"></video>' . "\r\n";
}
else
{
  echo '<audio preload="none" controls="" id="musicplayer"></audio>' . "\r\n";
}