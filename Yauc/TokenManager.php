<?php
namespace Yauc;
use ORM;

/**
 * Token Manager
 */
class TokenManager
{
  protected $key;
  protected $user;
  protected $expire;
  protected $path;
  protected $domain;
  protected $secure;
  protected $httponly;

  public function __construct($key = 'sso', $expire = 1800, $path = NULL, $domain = NULL, $secure = false, $httponly = false)
  {
    $this->key = $key;
    $this->user = NULL;
    $this->expire = $expire;
    $this->path = $path;
    $this->domain = $domain;
    $this->secure = $secure;
    $this->httponly = $httponly;

    $config = ServiceLocator::instance()->getService('config');
    $config->db();
  }

  public function isValidUser()
  {
    if (($user = $this->getUserFromCookies()) != FALSE)
    {
      if (isset($user['sid']))
      {
        return TRUE;
      }
    }

    return FALSE;
  }

  public function getUserFromCookies()
  {
    // Tip: Token Manager 返回的user数组是带有sid和expire的，User Repository返回的user不带。

    if ($this->user == NULL)
    {
      if (isset($_COOKIE[$this->key]))
      {
        $cookie = $_COOKIE[$this->key];
        $user = json_decode($cookie);
        if (json_last_error() != JSON_ERROR_NONE)
        {
          $this->user = FALSE;
        } else {
          $this->user = (array) $user;
        }
      } else {
        $this->user = FALSE;
      }
    }

    return $this->user;
  }

  public function initUser($sid, $uid, $username, $loginname, $expire = 0)
  {
    if ($expire == 0)
    {
      $expire = time() + $this->expire;
    }
    $this->user = array(
      'sid' => $sid,
      'uid' => $uid,
      'username' => $username,
      'loginname' => $loginname,
      'expire' => $expire);
  }

  public function saveCurrentSession($user = NULL)
  {
    if ($user == NULL)
    {
      $user = $this->user;
    }
    if ($user == NULL)
    {
      throw new \Exception('Cannot write unknown (NULL) user to browser.');
    }

    $cookie = json_encode($user);
    if (json_last_error() != JSON_ERROR_NONE)
    {
      return FALSE;
    }
    $session = ORM::for_table('sessions')->create();
    $session->set('sid', $user['sid']);
    $session->set('uid', $user['uid']);
    $session->set('expire', time() + $this->expire);
    $session->save();

    return setcookie($this->key, $cookie, time() + $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
  }

  public function clearCurrentSession()
  {
    if ($this->user != NULL)
    {
      $session = ORM::for_table('sessions')->find_one($this->user['sid']);
      $session->delete();
    }

    return setcookie($this->key, '', time() - 3600, $this->path, $this->domain, $this->secure, $this->httponly);
  }

  public function makeTicket($client, $user)
  {
    return hash('sha256', $client.'-'.time().'-'.$_SERVER['REMOTE_ADDR'].'-'.substr(uniqid(rand()), -8).'-'.$user['uid']);
  }

  public function saveTicket($client, $user, $ticket)
  {
    $t = ORM::for_table('tickets')->create();
    $t->set('ticket', $ticket);
    $t->set('client', $client);
    $t->set('uid', $user['uid']);
    // Client Ticket 五分钟内有效
    $t->set('expire', time() + 300);
    $t->save();
  }

  public function isValidTicket($ticket)
  {
    $t = ORM::for_table('tickets')->find_one($ticket);
    if ($t === FALSE)
    {
      // Ticket不存在
      return FALSE;
    } elseif (time() >= intval($t->expire)) {
      // Ticket已过期
      $t->delete();
      return FALSE;
    } else {
      // Ticket存在且仍然有效
      $t->delete();
      return $t->uid;
    }
  }
}
