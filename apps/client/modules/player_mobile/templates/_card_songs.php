<?php
  $left  = '<div class="buttonwrapper" id="songbuttonwrapper">';
  //$left .= '  <div id="songsmenu" class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_albums\');">&#9668;&nbsp;' . __( 'Albums' ) . '</div>';
  $left .= '</div>';
  
  $right  = '<div class="buttonwrapper" style="float: left; margin: 8pt 0 8pt 5pt;">';
  $right .= '  <div id="artistsearch" class="headerbutton" onclick="streeme.search(\'songs\');"><img src="' . public_path( 'images/player/mobile/mb-button-search.png' ) . '" /></div>';
  $right .= '</div>';
  $right .= '<div class="buttonwrapper" style="float: right;">';
  $right .= '  <div class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_welcome\');"><img src="' . public_path( 'images/player/mobile/mb-button-menu.png' ) . '" /></div>';
  $right .= '</div>';

  include_partial( 'header', array(
                                'title'=> __( 'Songs' ),
                                'left' => $left,
                                'right' =>$right,
             ) );
?>
<div class="letterbarcontainer">
  <?php include_partial( 'letterbar', array( 'element' => 'song' ) )?>
</div>

<?php
#
# This listing is dynamically populated by streeme.js
#
?>
<table class="songlist" id="songlist">
   <thead>
      <tr>
         <th class="hidden">id</th>
         <th class="hidden"><?php echo __( 'Track' ) ?></th>
         <th class="hidden"><?php echo __( 'Album' ) ?></th>
         <th class="hidden"><?php echo __( 'Artist' ) ?></th>
         <th class="hidden"><?php echo __( 'Modified' ) ?></th>
         <th class="hidden"><?php echo __( 'Year' ) ?></th>
         <th class="hidden"><?php echo __( 'Length' ) ?></th>
         <th class="hidden"><?php echo __( 'TK#' ) ?></th>
         <th class="hidden"><?php echo __( 'Extension' ) ?></th>
      </tr>
   </thead>
   <tbody>
   </tbody>
</table>
<div style="height: 200px;"></div>