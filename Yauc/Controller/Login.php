<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Login controller
 */
class Login extends Base
{

  protected function form()
  {
    $this->smarty = ServiceLocator::instance()->getService('smarty');

    $tokenMgr = ServiceLocator::instance()->getService('token');
    if ($tokenMgr->isValidUser())
    {
      // 已登陆。跳转回首页
      $this->redirect('/');
    }

    $client = $this->route->params;

    $this->smarty->assign('content', 'Login'.DIRECTORY_SEPARATOR.'basic.inc.tpl');
    $this->smarty->assign('title', '登陆 - Yet Another User Center');
    $this->smarty->assign('action', '/Login/basic');
    $this->smarty->assign('client', $client);
  }

  protected function basic()
  {
    // TODO: 此处为简化处理。表单密码提交之前应加密。
    $username = $_POST['username'];
    $password = $_POST['password'];
    $client = $_POST['client'];

    $clients = ServiceLocator::instance()->getService('clients');    
    if ($clients->clientValid($client))
    {
      $users = ServiceLocator::instance()->getService('users');
      if ($users->validateBasic($username, $password))
      {
        $tokenMgr = ServiceLocator::instance()->getService('token');

        // 从数据库读取用户信息
        $user = $users->getUserByBasic($username);

        // 生成用户本次登陆的会话信息并写入Cookies
        // @fake 需要混淆
        $sid = 'session-'.time().'-'.$_SERVER['REMOTE_ADDR'].'-'.rand(10000000, 99999999).'-'.$user['uid'];
        $tokenMgr->initUser($sid, $user['uid'], $username, $user['display']);
        $tokenMgr->writeUser();

        // 生成Client Ticket并跳转回Client
        $ticket = $clients->makeTicket($client, $user);
        $this->redirect($clients->getCallbackUrl($client, $ticket));
      } else {
        // 登陆失败
        $this->redirect('/Login/form/'.$client);
      }

    } else {
      // Client无效 返回首页
      $this->redirect('/');
    }
  }

}
