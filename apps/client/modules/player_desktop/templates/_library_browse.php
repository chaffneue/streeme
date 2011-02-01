<?php
#
# Layout wrapper for lists 
#
if ( get_slot( 'browsefirstitem' ) != true ) 
{
  slot( 'browsefirstitem', true );
  $html = 'browsefirstitem';
} 
else
{
  $html = '';
}
?>
<div class="browse <?php echo $html ?>" id="ctbrowse<?php echo strtolower( $title ); ?>">
   <div class="label">
   <?php 
    switch( strtolower( $title ) )
    {
      case 'artists':
        echo __( 'Browse Artists' );
        break;
      case 'albums':
        echo __( 'Browse Albums' );
        break; 
    }
   ?>
   </div>
   <div class="letterbarcontainer lightgradient">
      <?php
         $prefix = null;
         switch ( $element_id )
         {
            case( 'browseartist' ) :
               $prefix = 'ar';
               break;
            case( 'browsealbum' ) :
               $prefix = 'ab';
               break;
         }
      ?>
      <?php include_partial( 'letterbar', array( 'element_id'=> $element_id, 'prefix' => $prefix ) ); ?>
   </div>
   <div class="listcontainer" id="lc<?php echo $element_id; ?>">
      <?php include_partial( $list_template, array( 'element_id' => $element_id, 'prefix' => $prefix, 'list' => $list ) ); ?>
   </div>     
</div>