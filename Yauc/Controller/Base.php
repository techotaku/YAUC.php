<?php
namespace Yauc\Controller;

/**
 * Base controller
 */
class Base
{
  protected $route;
  protected $serviceLocator;

  public static function getController($route, $serviceLocator = null)
  {
    $controller ="\\Yauc\\Controller\\" . $route->controller;
    if (class_exists($controller)) {
      return new $controller($route, $serviceLocator);
    } else {
      exit('Specified controller <b><i>'.$route->controller.'</i></b> not found.');
    }
  }

  public function __construct($route, $serviceLocator = null)
  {
    $this->route = $route;
    $this->serviceLocator = $serviceLocator;
  }

  public function run()
  {
    $this->before();
    $method = $this->route->action;
    if (method_exists($this, $method)) {
      $this->$method();
    } else {
      exit('Specified action <b><i>'.$this->route->action.'</i></b> not found.');
    }
    $this->after();
  }

  protected function getService($service, $interface = '')
  {
    if (!isset($this->dependencies[$service])) {
      return FALSE;
    }
    if ($interface != '' && !($this->dependencies[$service] instanceof $interface)) {
      return FALSE;
    }

    return $this->dependencies[$service];
  }

  protected function before()
  {
  }

  protected function after()
  {
  }

  protected function redirect($url)
  {
    if (strtolower(substr($url, 0, 7)) != 'http://' && strtolower(substr($url, 0, 8)) != 'https://') {
      $url = $this->route->base . $url;
    }
    header('Location: ' . $url);
  }
}
