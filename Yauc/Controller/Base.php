<?php
namespace Yauc\Controller;

/**
 * Base controller
 */
class Base
{
  protected $route;
  protected $smarty;

  public static function getController($route)
  {
    $controller = "Yauc\\Controller\\".$route->controller;
    if (class_exists($controller)) {
      return new $controller($route);
    } else {
      echo 'Specified controller <b><i>'.$route->controller.'</i></b> not found.';
    }
  }

  public function __construct($route)
  {
    $this->route = $route;
  }

  public function run()
  {
    $this->before();
    $method = $this->route->action;
    if (method_exists($this, $method)) {
      $this->$method();
    } else {
      echo 'Specified action <b><i>'.$this->route->action.'</i></b> not found.';
    }
    $this->after();
  }

  protected function before()
  {
  }

  protected function after()
  {
    // 默认渲染layout模板，至少需要content模板文件名和title。
    if (is_object($this->smarty))
    {
      $this->smarty->setTemplateDir(VIEW_DIR);
      $this->smarty->display('layout.tpl');
    }
  }

  protected function redirect($url)
  {
    if (strtolower(substr($url, 0, 7)) != 'http://' && strtolower(substr($url, 0, 8)) != 'https://') {
      $url = $this->route->base . $url;
    }
    header('Location: ' . $url);
  }
}
