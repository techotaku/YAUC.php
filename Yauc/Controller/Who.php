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
        $ticket = $clients->makeTicket($this->client, $tokenMgr->getUserFromCookies());
        $this->redirect($clients->getCallbackUrl($this->client, $ticket));
      } else {
        $this->redirect('/Login/form/'.$this->client);
      }

    } else {
      // Client无效 返回首页
      $this->redirect('/');
    }
  }

}
