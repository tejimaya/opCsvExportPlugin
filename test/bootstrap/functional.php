<?php
// guess current application
if (!isset($app))
{
  $traces = debug_backtrace();
  $caller = $traces[0];

  $dirPieces = explode(DIRECTORY_SEPARATOR, dirname($caller['file']));
  $app = array_pop($dirPieces);
}

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

if (!isset($executeLoader) || $executeLoader)
{
  require_once dirname(__FILE__).'/database.php';
}

// remove all cache
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

$conn = Doctrine_Manager::getInstance()->getCurrentConnection();
$conn->clear();
