<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;
use ORM;

/**
 * Api controller
 */
class Api extends Base
{
  protected function before()
  {
    parent::before();

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

  protected function user()
  {
    // TODO: 需要存在性效验
    $client = $_POST['client'];
    $ticket = $_POST['ticket'];
    $signature = $_POST['signature'];

    $clients = ServiceLocator::instance()->getService('clients');
    $secret = $clients->getSecret($client);
    $timestamp = $_POST['timestamp'];
    $nonce = $_POST['nonce'];

    $signArray = array($secret, $timestamp, $nonce);
    sort($signArray);
    if (sha1(implode($signArray)) == $signature)
    {
      $t = ORM::for_table('tickets')->find_one($ticket);
      if ($t === FALSE)
      {
        echo '指定的Ticket不存在。';
      } elseif (time() >= intval($t->expire)) {
        echo '指定的Ticket已过期。';
        $t->delete();
      } else {
        // Ticket存在且仍然有效
        $users = ServiceLocator::instance()->getService('users');
        $user = $users->getUserById($t->uid);        
        echo json_encode($user);
        $t->delete();
      }      
    } else {
      echo '请求非法。';
    }
  }

  protected function session()
  {

  }


}
