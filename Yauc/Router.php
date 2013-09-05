<?php
namespace Yauc;

/**
 * Router
 */
class Router
{
  public $base = '';
  public $controller = '';
  public $action = '';
  public $params = '';
  public $querystring = '';

  const defaultController = 'Welcome';
  const defaultAction = 'index';

  public function __construct($base = '')
  {
    $url = $_SERVER['REQUEST_URI'];
    if ($base != '' && strncmp($base, $url, strlen($base)) == 0) {
      $url = str_replace($base, '', $url);
    }

    $uri = explode('?', ltrim($url, '/'), 2);
    $route_uri = explode('/', $uri[0], 3);

    $this->base = $base;
    $this->controller = ucfirst(strtolower($route_uri[0]));
    if (isset($route_uri[1])) $this->action = strtolower($route_uri[1]);
    if (isset($route_uri[2])) $this->params = $route_uri[2];
    if (isset($uri[1])) $this->querystring = $uri[1];

    if ($this->controller == '') $this->controller = self::defaultController;
    if ($this->action == '') $this->action = self::defaultAction;
  }
}
