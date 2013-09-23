<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Welcome sample controller
 */
class Welcome extends Base
{
  protected function before()
  {
    parent::before();

    $this->smarty = ServiceLocator::instance()->getService('smarty');
  }

  protected function index()
  {
    $tokenMgr = ServiceLocator::instance()->getService('token');
    $this->smarty->assign('content', 'Welcome'.DIRECTORY_SEPARATOR.'index.inc.tpl');
    $this->smarty->assign('title', '首页 - Yet Another User Center');
    if ($tokenMgr->isValidUser()) {
      $this->smarty->assign('user', $tokenMgr->getUserFromCookies());
    }
  }

  protected function register()
  {
    $this->smarty->assign('content', 'Welcome'.DIRECTORY_SEPARATOR.'new.inc.tpl');
    $this->smarty->assign('title', '注册 - Yet Another User Center');
  }

  protected function save()
  {
    $this->smarty = ServiceLocator::instance()->getService('smarty');
    $this->smarty->assign('content', 'message.inc.tpl');
    $this->smarty->assign('title', '注册 - Yet Another User Center');

    // TODO: 此处为简化处理。表单密码提交之前应加密。
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $users = ServiceLocator::instance()->getService('users');

    $error = '';
    if (!$users->checkUsernameAvailability($username)) {
       $error .= '用户名不可用。';
    }
    if (!$users->checkEmailAvailability($email)) {
       $error .= '邮件地址不可用。';
    }
    if ($password == '') {
       $error .= '密码不能为空。';
    }

    if ($error == '') {
      $id = $users->newUser($username, $email);
      $users->newIdentityBasic($id, $email, $password);

      $discuz = ServiceLocator::instance()->getService('discuz');
      $user = array(
        'uid' => $id,
        'username' => $username,
        'email' => $email);
      $discuz->register($user);

      $this->smarty->assign('message', '注册成功。');
    } else {
      $this->smarty->assign('message', '注册失败：'.$error);
    }

  }
}
