<?php
#
# Gets and displays a list of genres in a select input
#
?>
<div class="browse" id="ctbrowse<?php echo strtolower( $title ); ?>">
  <div class="label"><?php  echo __( 'Browse Genres' ) ?></div>
  <div class="listcontainer_noscroll">
    <select name="genreselector" id="genreselector">
      <option value="none"><?php echo __( '-- Choose a Genre --' ) ?></option>
      <?php
      foreach( $list as $row )
      {
        echo '<option value="' . $row[ 'genre_id' ] . '">' . __( $row[ 'Genre' ][ 'name' ] ) . '</option>';
      }
      ?>
    </select>
  </div>
</div>