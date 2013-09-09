<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Logout controller
 */
class Logout extends Base
{

  protected $client;

  protected function before()
  {
    parent::before();

    $this->client = $this->route->action;
    $this->route->action = 'auth'; 
  }

  protected function auth()
  {
    $clients = ServiceLocator::instance()->getService('clients');
    if ($clients->clientValid($this->client))
    {
      $tokenMgr = ServiceLocator::instance()->getService('token');
      if ($tokenMgr->isValidUser())
      {
        $tokenMgr->clearCurrentSession();
      }
      $this->redirect($clients->getLogoutCallbackUrl($this->client));
    } else {
      // Client无效 返回首页
      $this->redirect('/');
    }
  }


}
