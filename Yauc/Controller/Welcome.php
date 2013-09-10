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
    if ($tokenMgr->isValidUser())
    {
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
    // 唯一性验证暂未处理。
    // TODO: 此处为简化处理。表单密码提交之前应加密。
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $users = ServiceLocator::instance()->getService('users');
    $id = $users->newUser($username, $email);
    $users->newIdentityBasic($id, $email, $password);

    $this->smarty = ServiceLocator::instance()->getService('smarty');
    $this->smarty->assign('content', 'message.inc.tpl');
    $this->smarty->assign('title', '注册 - Yet Another User Center');
    $this->smarty->assign('message', '注册成功。');
  }
}
