<?php
namespace Yauc;

/**
 * Discuz Connector
 */
class DiscuzConnector
{
  protected $CURL_OPTS = array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'YAUC SSO Server',
    );

  public function getSyncScripts($currentClient, $uid)
  {
    $clients = ServiceLocator::instance()->getService('clients');
    $script = '';

    foreach ($clients->getClients() as $name => $info) {
      if ($name == $currentClient)
      {
        continue;
      }
      if ($info['type'] == 'discuz')
      {
        $time = (string) time();
        $script .= '<script type="text/javascript" src="'.$info['sync'].'?time='.$time.'&code='.urlencode($this->uc_authcode('action=synlogin&uid='.$uid.'&time='.$time, 'ENCODE', $info['secret'])).'" reload="1"></script>';
      } else {
        // TODO: 暂缺
        }
    }

    return $script;
  }

  public function register($user)
  {
    $clients = ServiceLocator::instance()->getService('clients');

    foreach ($clients->getClients() as $name => $info) {
      if ($info['type'] == 'discuz')
      {
        // TODO: 依次请求注册用户，需要优化处理，例如超时1毫秒
        $time = (string) time();
        $user['clientip'] = $_SERVER['REMOTE_ADDR'];
        $url = $info['sync'].'?time='.$time.'&code='.urlencode($this->uc_authcode('action=registeruser&uid='.$user['uid'].'&username='.$user['username'].'&email='.$user['email'].'&clientip='.$user['clientip'].'&time='.$time, 'ENCODE', $info['secret']));

        $opts = $this->CURL_OPTS;
        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_CUSTOMREQUEST] = 'GET';
        $ch = curl_init();
        curl_setopt_array($ch, $opts);        
        curl_exec($ch);
      } else {
        // TODO: 暂缺
      }
    }
  }

  protected function uc_authcode($string, $operation = 'DECODE', $key, $expiry = 0) {

    $ckey_length = 4; 

    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
      $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
      $j = ($j + $box[$i] + $rndkey[$i]) % 256;
      $tmp = $box[$i];
      $box[$i] = $box[$j];
      $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
      $a = ($a + 1) % 256;
      $j = ($j + $box[$a]) % 256;
      $tmp = $box[$a];
      $box[$a] = $box[$j];
      $box[$j] = $tmp;
      $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
      if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
        return substr($result, 26);
      } else {
        return '';
      }
    } else {
      return $keyc.str_replace('=', '', base64_encode($result));
    }

  }

}
