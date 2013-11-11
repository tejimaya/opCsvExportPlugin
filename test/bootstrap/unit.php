<?php

$_test_dir = realpath(dirname(__FILE__).'/..');
if (!isset($app))
{
  $app = 'pc_backend';
}
if (!isset($debug))
{
  $debug = true;
}

// chdir to the symfony(OpenPNE) project directory
chdir(dirname(__FILE__).'/../../../..');

require_once 'config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', $debug, realpath($_test_dir.'/../../../'));
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';
