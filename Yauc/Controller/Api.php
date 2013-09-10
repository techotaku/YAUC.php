<?php
namespace Yauc\Controller;
use Yauc\ServiceLocator;

/**
 * Api controller
 */
class Api extends Base
{

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
      $tokenMgr = ServiceLocator::instance()->getService('token');
      // 效验Ticket，同时删除Ticket
      if (($uid = $tokenMgr->isValidTicket($ticket)) === FALSE)
      {
        echo '指定的Ticket无效。';
      } else {
        $users = ServiceLocator::instance()->getService('users');
        $user = $users->getUserById($uid);
        echo json_encode($user);
      }
    } else {
      echo '请求非法。';
    }
  }

}
