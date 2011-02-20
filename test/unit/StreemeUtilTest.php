<?php
include( dirname(__FILE__) . '/../bootstrap/unit.php' );
include( dirname(__FILE__) . '/../../apps/client/lib/StreemeUtil.class.php' );

// Initialize the test object
$t = new lime_test( 14, new lime_output_color() );



$t->comment( '->itunes_format_encode()' );
$t->is( StreemeUtil::itunes_format_encode( '/home/foo/bar' ), 'file://localhost/home/foo/bar', 'Formatted a *nix path in the itunes format' );

$t->comment( '->itunes_format_decode()' );
$t->is( StreemeUtil::itunes_format_decode( 'file://localhost/home/foo/bar%20man' ), '/home/foo/bar man', 'Decoded an itunes formatted path' );

$t->comment( '->slugify()' );
$t->is( StreemeUtil::slugify('stuff & thing fox\'s Name'), 'stuff-thing-fox-s-name', 'Processed sting pattern into valid url' );

$t->comment( '->in_array_ci()' );
$needle = 'ABC';
$haystack = array( 'abc', 'def' );
$t->is( StreemeUtil::in_array_ci( $needle, $haystack ), true, 'upper needle lower haystack' );
$needle = 'AbC';
$haystack = array( 'ABC', 'def' );
$t->is( StreemeUtil::in_array_ci( $needle, $haystack ), true, 'mixed case needle upper haystack' );
$needle = 'abc';
$haystack = array( 'ABC', 'def' );
$t->is( StreemeUtil::in_array_ci( $needle, $haystack ), true, 'lower case needle upper haystack' );
$needle = 'abc';
$haystack = array( 'AbC', 'def' );
$t->is( StreemeUtil::in_array_ci( $needle, $haystack ), true, 'lower case needle mixed haystack' );
$needle = 'xyz';
$haystack = array( 'AbC', 'def' );
$t->is( StreemeUtil::in_array_ci( $needle, $haystack ), false, 'out of range needle' );

$t->comment( '->xmlize_uf8_string()');
$t->is( StreemeUtil::xmlize_uf8_string( join( range( chr(0),chr(127) ) ) ), join( range( chr(1),chr(127) ) ), 'passes printable US-ascii chars' );
$t->is( StreemeUtil::xmlize_uf8_string( ' 小低胡' . chr(0) ), '小低胡', 'passes printable UTF-8 chars' );
$t->is( StreemeUtil::xmlize_uf8_string( 'äöüæøy' ), 'äöüæøy', 'passes printable UTF-8 tremas' );
$t->is( StreemeUtil::xmlize_uf8_string( 'm̥mn̥nɲ̊ɲŋ̊ŋðóíáþ' ), 'm̥mn̥nɲ̊ɲŋ̊ŋðóíáþ', 'passes printable UTF-8 icelandic chars' );
$t->is( StreemeUtil::xmlize_uf8_string( 'YÿþAÿþ#ÿþÿþ' ), '', 'removes id3 signalling leak' );

$t->comment( '->replace_url_nonfs_chars()' );
$t->is( StreemeUtil::replace_url_nonfs_chars( '%E2%80%93' . '%E2%80%A6' . '%E2%80%BA' ), '%96' . '%85' . '%9B' , 'change mb_strings to single byte latin' );