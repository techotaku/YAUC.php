<?php
namespace Yauc;

/**
 * SSO Clients Repository
 */
class ClientRepository
{
  protected $clients;

  public function __construct()
  {
    $this->clients = ServiceLocator::instance()->getService('config')->load('clients');
  }

  public function clientValid($client)
  {
    return array_key_exists(strtolower($client), $this->clients);
  }

  public function getSecret($client)
  {
    if (!array_key_exists(strtolower($client), $this->clients))
    {
      throw new \Exception('Specified client serivce "'.$client.'"" not found.');
    }
    return $this->clients[strtolower($client)]['secret'];
  }

  public function getLoginCallbackUrl($client, $ticket)
  {
    if (!array_key_exists(strtolower($client), $this->clients))
    {
      throw new \Exception('Specified client serivce "'.$client.'"" not found.');
    }
    $callback = $this->clients[strtolower($client)]['login'];

    return str_replace('{ticket}', $ticket, $callback);
  }

  public function getLogoutCallbackUrl($client)
  {
    if (!array_key_exists(strtolower($client), $this->clients))
    {
      throw new \Exception('Specified client serivce "'.$client.'"" not found.');
    }

    return $this->clients[strtolower($client)]['logout'];
  }

}
