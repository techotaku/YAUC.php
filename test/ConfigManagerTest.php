<?php
/**
 * YAUC.php
 *
 * @author     Ian Li <i@techotaku.net>
 * @copyright  Ian Li <i@techotaku.net>, All rights reserved.
 * @link       https://github.com/techotaku/YAUC.php
 */

  namespace Yauc\Test;
  use Yauc;
  use ORM;

  /**
   * ConfigManager Test
   */
  class ConfigManagerTest extends \PHPUnit_Framework_TestCase
  {
    protected $config;

    protected function setUp()
    {
      parent::setUp();

      if ($this->config == NULL) {
        $this->config = new Yauc\ConfigManager();
      }

      $content = "<?php
return array(
  'server' => 'localhost',
  'database' => 'techotaku_test',
  'username' => 'techotaku_user',
  'password' => 'techotaku_password');";
      file_put_contents(CFG_DIR.'database.cfg.php', $content);
    }

    public function testLoad()
    {
      $this->assertEquals(array(
                           'server' => 'localhost',
                           'database' => 'techotaku_test',
                           'username' => 'techotaku_user',
                           'password' => 'techotaku_password'),
              $this->config->load('database'));
    }

    public function testDbConfig()
    {
      $this->config->db();
      $this->assertEquals('mysql:host=localhost;dbname=techotaku_test;charset=utf8', ORM::get_config('connection_string'));
      $this->assertEquals('techotaku_user', ORM::get_config('username'));
      $this->assertEquals('techotaku_password', ORM::get_config('password'));
      $this->assertEquals(true, ORM::get_config('return_result_sets'));
      $this->assertEquals(array(
                           'sessions' => 'sid',
                           'users' => 'uid',
                           'identity_basic' => 'uid',
                           'tickets' => 'ticket'),
                          ORM::get_config('id_column_overrides'));
    }

  }
