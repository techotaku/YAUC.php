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
    $this->clients = array(
      'demo1' => array(
        'secret' => '',
        'callback' => 'http://demo1.techotaku.net:8080/auth/callback/{ticket}'),
      'demo2' => array(
        'secret' => '',
        'callback' => 'http://demo2.techotaku.net:8080/auth/callback/{ticket}')
      );
  }

  public function clientValid($client)
  {
    return array_key_exists(strtolower($client), $this->clients);
  }

  public function getSecret($client)
  {
    if (array_key_exists(strtolower($client), $this->clients))
    {
      return $this->clients[strtolower($client)]['secret'];
    }
  }

  public function getCallbackUrl($client, $ticket)
  {
    if (array_key_exists(strtolower($client), $this->clients))
    {
      $callback = $this->clients[strtolower($client)]['callback'];
      return str_replace('{ticket}', $ticket, $callback);
    }
  }
}
