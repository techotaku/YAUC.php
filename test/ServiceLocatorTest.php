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
   * ServiceLocator Test
   */
  class ServiceLocatorTest extends \PHPUnit_Framework_TestCase  {
    protected $registeredServices;
    protected $services;

    protected function setUp()
    {
      // 通过反射读取受保护的类成员
      $this->registeredServices = new \ReflectionProperty('Yauc\\ServiceLocator', 'registeredServices');
      $this->registeredServices->setAccessible(TRUE);
      $this->services = new \ReflectionProperty('Yauc\\ServiceLocator', 'services');
      $this->services->setAccessible(TRUE);
    }

    private function getRegistered($instance = NULL)
    {
      if ($instance == NULL)
      {
        $instance = Yauc\ServiceLocator::instance();
      }
      return $this->registeredServices->getValue($instance);
    }

    private function getInstance($instance = NULL)
    {
      if ($instance == NULL)
      {
        $instance = Yauc\ServiceLocator::instance();
      }
      return $this->services->getValue($instance);
    }

    public function testEmpty() {
      $locator = new Yauc\ServiceLocator;
      $this->assertEquals(array(), $this->getRegistered($locator));
      $this->assertEquals(array(), $this->getInstance($locator));
    }

    public function testInstance() {
      $locator = Yauc\ServiceLocator::instance();
      $this->assertSame($locator, Yauc\ServiceLocator::instance());
    }

    public function testRegister() {
      $locator = new Yauc\ServiceLocator;
      $this->assertTrue($locator->registerService('name', 'Meaningless'));
      $this->assertFalse($locator->registerService('name', 'Meaningless'));
      $this->assertTrue($locator->registerService('new', 'Meaningless'));
    }

  }
?>