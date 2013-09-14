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
      $this->assertEquals(
               array(
                 'name' => array('class' => 'Meaningless', 'parameters' => array())), 
               $this->getRegistered($locator));
      $this->assertFalse($locator->registerService('name', 'Meaningless'));
      $this->assertTrue($locator->registerService('new', 'Meaningless'));
      $this->assertEquals(
               array(
                 'name' => array('class' => 'Meaningless', 'parameters' => array()), 
                 'new' => array('class' => 'Meaningless', 'parameters' => array())), 
               $this->getRegistered($locator));
    }

    public function testGet()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', '\\Exception');
      $ex = $locator->getService('name');
      $this->assertTrue($ex instanceof \Exception);
      $this->assertSame($ex, $locator->getService('name', '\\Exception'));
    }

    public function testGetReused()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', '\\Exception');
      $ex1 = $locator->getService('name');
      $this->assertTrue($ex1 instanceof \Exception);
      $ex2 = $locator->getService('name');
      $this->assertTrue($ex2 instanceof \Exception);
      $this->assertSame($ex1, $ex2);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Cannot locate specified service: name. Service has not been registered or failed to initialize.
     */
    public function testGetException1()
    {
      $locator = new Yauc\ServiceLocator;
      $ex = $locator->getService('name');
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Cannot locate specified service: name. Service has not been registered or failed to initialize.
     */
    public function testGetException2()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', 'Yauc\\ServiceLocator');
      $locator->getService('name', 'Yauc\\ConfigManager');
    }

    /**
     * @expectedException        ReflectionException
     * @expectedExceptionMessage Class NotExists does not exist
     */
    public function testGetException3()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', 'NotExists');
      $ex = $locator->getService('name');
    }

    public function testSet()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', '\\Exception');
      $ex = new \Exception();
      $locator->setService('name', $ex);
      $this->assertEquals(
               array('name' => $ex), 
               $this->getInstance($locator));
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Cannot locate specified service: name. Service must be registered before inserted.
     */
    public function testSetException1()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->setService('name', NULL);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Specified object is not a valid instance of class / interface Yauc\ServiceLocator.
     */
    public function testSetException2()
    {
      $locator = new Yauc\ServiceLocator;
      $locator->registerService('name', 'Yauc\\ServiceLocator');
      $content = new Yauc\ConfigManager();
      $locator->setService('name', $content);
    }

  }
?>