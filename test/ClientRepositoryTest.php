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

  /**
   * ClientRepository Test
   */
  class ClientRepositoryTest extends \PHPUnit_Framework_TestCase  {
    protected $clientscfg = array(
      'demodz' => array(
        'secret' => 'j0lbtf',
        'login' => 'http://demodz.techotaku.net/sso.php?action=login_callback&ticket={ticket}',
        'logout' => 'http://demodz.techotaku.net/sso.php?action=logout_callback',
        'sync' => 'http://demodz.techotaku.net/api/uc.php',
        'type' => 'discuz'),
      'dz2' => array(
        'secret' => 'd8p4Ue',
        'login' => 'http://dz2.techotaku.net/sso.php?action=login_callback&ticket={ticket}',
        'logout' => 'http://dz2.techotaku.net/sso.php?action=logout_callback',
        'sync' => 'http://dz2.techotaku.net/api/uc.php',
        'type' => 'discuz')
      );
    protected $clients;

    protected function setUp()
    {
      parent::setUp();

      $locator = Yauc\ServiceLocator::instance();
      $locator->registerService('discuz', "Yauc\\DiscuzConnector");
      $locator->registerService('clients', "Yauc\\ClientRepository");

      // 准备mock的ConfigManager
      $locator->registerService('config', "Yauc\\ConfigManager");
      $config = $this->getMock("Yauc\\ConfigManager");

      // 使得ClientRepository在调用ConfigManager时得到我们准备的测试数据
      $config->expects($this->any())
             ->method('load')
             ->will($this->returnValue($this->clientscfg));
      $locator->setService('config', $config);

      $this->clients = $locator->getService('clients');
    }

    public function testGetClients() {
      $this->assertEquals($this->clientscfg, $this->clients->getClients());
    }

    public function testClientValid()
    {
      $this->assertTrue($this->clients->clientValid('demodz'));
      $this->assertTrue($this->clients->clientValid('dz2'));
      $this->assertFalse($this->clients->clientValid('whatever else'));
    }

    public function testGetSecret() {
      $this->assertEquals('j0lbtf', $this->clients->getSecret('demodz'));
      $this->assertEquals('d8p4Ue', $this->clients->getSecret('dz2'));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Specified client serivce "whatever else" not found.
     */
    public function testGetSecretException() {
      $this->assertEquals('meaningless', $this->clients->getSecret('whatever else'));
    }

    public function testGetLoginCallbackUrl() {
      $this->assertEquals('http://demodz.techotaku.net/sso.php?action=login_callback&ticket=you-are-ticket', 
               $this->clients->getLoginCallbackUrl('demodz', 'you-are-ticket'));
      $this->assertEquals('http://dz2.techotaku.net/sso.php?action=login_callback&ticket=you-are-the-ticket', 
               $this->clients->getLoginCallbackUrl('dz2', 'you-are-the-ticket'));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Specified client serivce "whatever else" not found.
     */
    public function testGetLoginCallbackUrlException() {
      $this->assertEquals('meaningless', $this->clients->getLoginCallbackUrl('whatever else', 'you-are-the-fucking-ticket'));
    }

    public function testGetLogoutCallbackUrl() {
      $this->assertEquals('http://demodz.techotaku.net/sso.php?action=logout_callback', 
               $this->clients->getLogoutCallbackUrl('demodz'));
      $this->assertEquals('http://dz2.techotaku.net/sso.php?action=logout_callback', 
               $this->clients->getLogoutCallbackUrl('dz2'));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Specified client serivce "whatever else" not found.
     */
    public function testGetLogoutCallbackUrlException() {
      $this->assertEquals('meaningless', $this->clients->getLogoutCallbackUrl('whatever else'));
    }

  }
?>