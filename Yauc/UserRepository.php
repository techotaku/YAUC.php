<?php
namespace Yauc;
use ORM;

/**
 * User Repository
 */
class UserRepository
{

  public function __construct()
  {
    $config = ServiceLocator::instance()->getService('config');
    $dbcfg = $config->load('database');
    ORM::configure('mysql:host='.$dbcfg['server'].';dbname='.$dbcfg['database'].';charset=utf8');
    ORM::configure('username', $dbcfg['username']);
    ORM::configure('password', $dbcfg['password']);
    ORM::configure('return_result_sets', true);
    ORM::configure('id_column', 'uid');
  }

  public function newUser($display, $email)
  {
    // TODO: 需要异常处理封装

    $user = ORM::for_table('users')->create();
    $user->set('display', $display);
    // email的唯一性验证需要在Controller中完成
    $user->set('email', $email);
    $user->save();
    return $user->id();
  }

  public function newIdentityBasic($uid, $username, $password)
  {
    // TODO: 需要异常处理封装

    $salt = substr(uniqid(rand()), -8);
    $password = hash('sha256', $salt.$password);
    $identity = ORM::for_table('identity_basic')->create();
    $identity->set('uid', $uid);
    // username的唯一性验证需要在Controller中完成
    $identity->set('username', $username);
    $identity->set('password', $password);
    $identity->set('salt', $salt);
    $identity->save();
  }

  public function validateBasic($username, $password)
  {
    $identity = ORM::for_table('identity_basic')
      ->where('username', $username)
      ->find_one();
    if ($identity === FALSE)
    {
      return FALSE;
    }

    $salt = $identity->salt;
    $pwd = $identity->password;
    return $pwd == hash('sha256', $salt.$password);
  }

  public function getUserByBasic($username)
  {
    $user = ORM::for_table('identity_basic')
      ->where('username', $username)
      ->join('users', array('identity_basic.uid', '=', 'users.uid'))
      ->find_one();
    if ($user === FALSE)
    {
      return FALSE;
    }
    return array(
      'uid' => $user->id(),
      'display' => $user->display,
      'email' => $user->email);
  }

  public function getUserById($uid)
  {
    $user = ORM::for_table('users')->find_one($uid);
    if ($user === FALSE)
    {
      return FALSE;
    }
    return array(
      'uid' => $user->id(),
      'display' => $user->display,
      'email' => $user->email);
  }

}
