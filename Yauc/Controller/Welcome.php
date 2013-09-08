<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Welcome sample controller
 */
class Welcome extends Base
{
  protected function index()
  {
    echo file_get_contents(VIEW_DIR.'Welcome'.DIRECTORY_SEPARATOR.'index.html');
  }

  protected function register()
  {
    echo file_get_contents(VIEW_DIR.'Welcome'.DIRECTORY_SEPARATOR.'new.html');
  }

  protected function save()
  {
    // 唯一性验证暂未处理。
    // TODO: 此处为简化处理。表单密码提交之前应加密。
    $username = $_POST['username'];
    $password = $_POST['password'];
    $display = $_POST['display'];
    $email = $_POST['email'];

    $users = ServiceLocator::instance()->getService('users');
    $id = $users->newUser($display, $email);
    $users->newIdentityBasic($id, $username, $password);

    echo '
<html>
  <head>
    <title>注册 - Yet Another User Center</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>
  注册成功。
  </body>
</html>
';

  }
}
