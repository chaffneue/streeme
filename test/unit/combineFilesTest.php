<?php
include( dirname(__FILE__) . '/../bootstrap/unit.php' );
include( dirname(__FILE__) . '/../../apps/client/lib/combineFiles.class.php' );
include( dirname(__FILE__) . '/../mock/sfWebResponseMock.class.php' );
include( dirname(__FILE__) . '/../../apps/client/lib/StreemeUtil.class.php' );

// Initialize the test object
$t = new lime_test( 6, new lime_output_color() );
$combiner = new combineFiles();

$t->comment( '->combine()' );
// force the asset cache to regenerate
$_GET['clearassetcache'] = 1;
$t->comment( 'Javascripts' );
$js_location = $combiner->combine('js', 'testnamespace', new sfWebResponseMock(new sfEventDispatcher) );
$t->is( $js_location, '/service/combine/js/testnamespace', 'File stored correctly');
$t->comment( 'Stylesheets' );
$css_location = $combiner->combine('css', 'testnamespace', new sfWebResponseMock(new sfEventDispatcher) );
$t->is( $css_location, '/service/combine/css/testnamespace', 'File stored correctly');

$t->comment( '->getFileName()' );
$t->comment( 'Javascripts' );
$js_filename = $combiner->getFileName('js', 'testnamespace' );
$t->is( $js_filename,  sfConfig::get('sf_cache_dir').'/combine/js/testnamespace.js', 'File retrieved correctly');
$js_content_expected = file_get_contents(sfConfig::get('sf_test_dir') . '/files/testnamespaceexample.js');
$js_content = file_get_contents($js_filename);
$t->is( $js_content, $js_content_expected, 'File was minified as expected');
$t->comment( 'Stylesheets' );
$css_filename = $combiner->getFileName('css', 'testnamespace');
$t->is( $css_filename, sfConfig::get('sf_cache_dir').'/combine/css/testnamespace.css', 'File retrieved correctly');
$css_content_expected = file_get_contents(sfConfig::get('sf_test_dir') . '/files/testnamespaceexample.css');
$css_content = file_get_contents($css_filename);
$t->is( $css_content, $css_content_expected, 'File was minified as expected');