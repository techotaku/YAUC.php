<?php
/**
 * YAUC.php
 *
 * @author     Ian Li <i@techotaku.net>
 * @copyright  Ian Li <i@techotaku.net>, All rights reserved.
 * @link       https://github.com/techotaku/YAUC.php
 */
  namespace Yauc\Test;
  use Yauc\Router;

  /**
   * Router Test
   */
  class RouterTest extends \PHPUnit_Framework_TestCase  {

    public function testFull() {
      $_SERVER['REQUEST_URI'] = '/mycon/myact/var?qstring';
      $route = new Router();

      $this->assertEquals('Mycon', $route->controller);
      $this->assertEquals('myact', $route->action);
      $this->assertEquals('var', $route->params);
      $this->assertEquals('qstring', $route->querystring);
    }

    public function testFullWithBase() {
      $_SERVER['REQUEST_URI'] = '/default.php/mycon/myact/var?qstring';
      $route = new Router('/default.php');

      $this->assertEquals('Mycon', $route->controller);
      $this->assertEquals('myact', $route->action);
      $this->assertEquals('var', $route->params);
      $this->assertEquals('qstring', $route->querystring);
    }

    public function testDefault() {
      $_SERVER['REQUEST_URI'] = '/';
      $route = new Router();

      $this->assertEquals(Router::defaultController, $route->controller);
      $this->assertEquals(Router::defaultAction, $route->action);
      $this->assertEquals('', $route->params);
      $this->assertEquals('', $route->querystring);
    }

    public function testDefaultWithBase() {
      $_SERVER['REQUEST_URI'] = '/default.php';
      $route = new Router('/default.php');

      $this->assertEquals(Router::defaultController, $route->controller);
      $this->assertEquals(Router::defaultAction, $route->action);
      $this->assertEquals('', $route->params);
      $this->assertEquals('', $route->querystring);
    }

  }
?>