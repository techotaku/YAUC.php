<?php
namespace Yauc;
use ORM;

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

  public function db()
  {
    $dbcfg = $this->load('database');
    ORM::configure('mysql:host='.$dbcfg['server'].';dbname='.$dbcfg['database'].';charset=utf8');
    ORM::configure('username', $dbcfg['username']);
    ORM::configure('password', $dbcfg['password']);
    ORM::configure('return_result_sets', true);
    ORM::configure('id_column_overrides', array(
      'sessions' => 'sid',
      'users' => 'uid',
      'identity_basic' => 'uid',
      'tickets' => 'ticket'
    ));
  }

}
