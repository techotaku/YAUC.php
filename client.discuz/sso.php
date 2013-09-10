<?php
/**
 *      YAUC Discuz client
 */

error_reporting(0);

define('UC_CLIENT_VERSION', '1.6.0');
define('UC_CLIENT_RELEASE', '20110501');

define('IN_API', true);

require './source/class/class_core.php';

$discuz = C::app();
$discuz->init();

require DISCUZ_ROOT.'/config/config_ucenter.php';

// sso系统中定义的client
define('CLIENT', 'demodz');
// 这里直接使用UC_KEY作为secret
define('SECRET', UC_KEY);

$action = strtolower($_GET['action']);
if (!in_array($action, array('login', 'login_callback', 'logout', 'logout_callback')))
{
  echo 'invalid request.';
}

$sso = new SsoClient;
$sso->$action();

class SsoClient
{
  protected $CURL_OPTS = array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'YAUC SSO Discuz! Client',
    );

  public function login()
  {
    $this->redirect('http://sso.techotaku.net/Who/demodz');
  }

  public function login_callback()
  {
    $ticket = $_GET['ticket'];
    $timestamp = (string) time();
    $nonce = substr(uniqid(rand()), -8);
    $signArray = array(SECRET, $timestamp, $nonce);
    sort($signArray);
    $signature = sha1(implode($signArray));

    $opts = $this->CURL_OPTS;
    $opts[CURLOPT_URL] = 'http://sso.techotaku.net/Api/user';
    $opts[CURLOPT_CUSTOMREQUEST] = 'POST';
    $data = array('client' =>'demodz',
              'ticket' => $ticket,
              'timestamp' => $timestamp,
              'nonce' => $nonce,
              'signature' => $signature);

    $opts[CURLOPT_POSTFIELDS] = http_build_query($data);

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    $user = json_decode($result);

    if (json_last_error() != JSON_ERROR_NONE)
    {
      // JSON解析出错，表示API没有返回合法的JSON，基本上可以认定返回了一句错误提示。
      curl_close($ch);
      echo '登陆失败。'.$result;
    } elseif (curl_errno($ch)) {
      echo '登陆失败。CURL error: '.curl_error($ch);
      curl_close($ch);
    } else {
      curl_close($ch);
      $user = (array) $user;
      $cookietime = 31536000;
      $uid = $user['uid'];
      if(($member = getuserbyuid($uid, 1))) {
        dsetcookie('auth', authcode("$member[password]\t$member[uid]", 'ENCODE'), $cookietime);
      }
      $this->redirect('/');
    }
  }

  public function logout()
  {
    dsetcookie('auth', '', -31536000);
    $this->redirect('http://sso.techotaku.net/Logout/demodz');    
  }

  public function logout_callback()
  {
    $this->redirect('/');
  }

  protected function redirect($url)
  {
    header('Location: '.$url);
  }
}
