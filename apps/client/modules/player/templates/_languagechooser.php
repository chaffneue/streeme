<?php
  //terribly sorry for the non templatey things, just trying to make it self contained 
  $languages = sfConfig::get( 'sf_translations_available', array() );
  $labels    = sfConfig::get( 'sf_translation_labels', array() );
  $user      = sfContext::getInstance()->getUser();
?>
<form method="get" action="" onsubmit="return false">
  <select name="sf_culture" onchange="this.form.submit()">
    <?php
      foreach( $languages as $language_code )
      {
       echo sprintf( '<option value="%s" %s>%s</option>', $language_code, ( $language_code == $user->getCulture() ) ? 'selected="selected"' : '' ,$labels[ $language_code ] );
      }    
    ?>
  </select>
</form>