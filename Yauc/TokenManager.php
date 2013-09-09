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
    $dbcfg = $config->load('database');
    ORM::configure('mysql:host='.$dbcfg['server'].';dbname='.$dbcfg['database'].';charset=utf8');
    ORM::configure('username', $dbcfg['username']);
    ORM::configure('password', $dbcfg['password']);
    ORM::configure('return_result_sets', true);
    ORM::configure('id_column_overrides', array(
      'sessions' => 'sid',
      'users' => 'uid',
      'identity_basic' => 'uid',
      'tickets' => 'ticket'
    ));
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

  public function initUser($sid, $uid, $username, $displayedName, $expire = 0)
  {
    if ($expire == 0)
    {
      $expire = time() + $this->expire;
    }
    $this->user = array(
      'sid' => $sid,
      'uid' => $uid,
      'username' => $username,
      'display' => $displayedName,
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
}
