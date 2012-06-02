<?php
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('client', 'test', true);
sfContext::createInstance($configuration)->dispatch();