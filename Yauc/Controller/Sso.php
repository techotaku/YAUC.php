<?php
namespace Yauc\Controller;

/**
 * Welcome sample controller
 */
class Sso extends Base
{
  protected $client;

  protected function before()
  {
    parent::before();

    if ($this->route->action != 'auth')
    {
      $this->redirect('/');
    }

    $this->client = $this->route->params;
  }

  protected function auth()
  {
    $clientRepository = $this->serviceLocator->getService('clients');
    if ($clientRepository->clientValid($this->client))
    {
      echo ucfirst($this->client).'<br />'."\n";
      echo $clientRepository->getCallbackUrl($this->client, 'Fake-Ticket-05646131654681561-0013').'<br />'."\n";
      echo '['.__CLASS__.'] <i>Need more work...</i>';
    } else {
      echo 'Invalid client.';
    }
  }

}
