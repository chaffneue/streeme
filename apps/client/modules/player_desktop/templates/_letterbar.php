<?php
#
# Displays a letter based navigation component in an unordered list
# Non English: TODO - Make a similar nav for non-latin/non-english
#
	$list = array();

   // Go 123 
	$list[] = '#';

	// Go A-Z
	for($code = ord('A'); $code <= ord('Z'); $code++) {
		$list[] = chr($code);
	}
	
  echo '<table class="letterbar" id="letterbar' . $element_id . '">';
  echo ' <tr>';
  foreach ( $list as $alpha => $label )
  {
    echo '<td onclick="streeme.uiLetterbarScroll( \'' . $element_id . '\', \'' . $prefix . strtolower( ( ( $label == '#' ) ? '123' : $label ) ) . '\' ); return false;">' . strtoupper( $label ) . '</td>';
  }
  echo ' </tr>';
  echo '</table>';
?>