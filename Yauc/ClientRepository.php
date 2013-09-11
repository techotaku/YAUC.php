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

  public function getClients()
  {
    return $this->clients;
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

  public function getSyncLoginScripts($currentClient, $uid)
  {
    $discuz = ServiceLocator::instance()->getService('discuz');
    $script = '';

    foreach ($this->clients as $name => $info) {
      if ($name == $currentClient)
      {
        continue;
      }
      if ($info['type'] == 'discuz')
      {
        $time = (string) time();
        $script .= '<script type="text/javascript" src="'.$info['sync'].'?time='.$time.'&code='.urlencode($discuz->uc_authcode('action=synlogin&uid='.$uid.'&time='.$time, 'ENCODE', $info['secret'])).'" reload="1"></script>';
      } else {
        // TODO: 暂缺
        }
    }

    return $script;
  }

  public function getSyncLogoutScripts($currentClient)
  {
    $discuz = ServiceLocator::instance()->getService('discuz');
    $script = '';

    foreach ($this->clients as $name => $info) {
      if ($name == $currentClient)
      {
        continue;
      }
      if ($info['type'] == 'discuz')
      {
        $time = (string) time();
        $script .= '<script type="text/javascript" src="'.$info['sync'].'?time='.$time.'&code='.urlencode($discuz->uc_authcode('action=synlogout&time='.$time, 'ENCODE', $info['secret'])).'" reload="1"></script>';
      } else {
        // TODO: 暂缺
        }
    }

    return $script;
  }

}
