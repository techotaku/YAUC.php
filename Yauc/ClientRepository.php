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
    // @fake
    $this->clients = array(
      'demo1' => array(
        'secret' => '6e5ed3be',
        'login' => 'http://demo1.techotaku.net:8080/demo1/auth/{ticket}',
        'logout' => 'http://demo1.techotaku.net:8080/demo1/index'),
      'demo2' => array(
        'secret' => '9fc7ccb0',
        'login' => 'http://demo2.techotaku.net:8080/demo2/auth/{ticket}',
        'logout' => 'http://demo2.techotaku.net:8080/demo2/index')
      );
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
