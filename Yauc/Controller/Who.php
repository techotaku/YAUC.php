<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Who controller
 */
class Who extends Base
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
        $user = $tokenMgr->getUserFromCookies();
        $ticket = $clients->makeTicket($this->client, $user);
        $clients->saveTicket($this->client, $user, $ticket);

        $this->redirect($clients->getLoginCallbackUrl($this->client, $ticket));
      } else {
        $this->redirect('/Login/form/'.$this->client);
      }

    } else {
      // Client无效 返回首页
      $this->redirect('/');
    }
  }

}
