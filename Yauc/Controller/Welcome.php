<?php
namespace Yauc\Controller;

/**
 * Welcome sample controller
 */
class Welcome extends Base
{
  protected function index()
  {
    echo file_get_contents(VIEW_DIR.'Welcome/index.html');
  }
}
