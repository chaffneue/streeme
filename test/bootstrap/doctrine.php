<?php
include(dirname(__FILE__).'/unit.php');
$configuration = ProjectConfiguration::getApplicationConfiguration( 'client', 'test', true);
new sfDatabaseManager($configuration);
exec( dirname(__FILE__) . '/../../symfony doctrine:build --all --and-load --env=test --no-confirmation');