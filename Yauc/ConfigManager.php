<?php
namespace Yauc;

define('CFG_DIR', APP_ROOT.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR);

/**
 * Config Manager
 */
class ConfigManager
{

  public function __construct()
  {
  }

  public function load($key)
  {
  	return (include CFG_DIR.$key.'.cfg.php');
  }

}
