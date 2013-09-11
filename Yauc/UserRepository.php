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
    ServiceLocator::instance()->getService('config')->db();
  }

  public function checkUsernameAvailability($username)
  {
    $user = ORM::for_table('users')
              ->where('username', $username)
              ->find_one();
    if ($user === FALSE)
    {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function checkEmailAvailability($email)
  {
    $user = ORM::for_table('users')
              ->where('email', $email)
              ->find_one();
    if ($user === FALSE)
    {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function newUser($username, $email)
  {
    // TODO: 需要异常处理封装

    $user = ORM::for_table('users')->create();
    $user->set('username', $username);
    // email的唯一性验证需要在Controller中完成
    $user->set('email', $email);
    $user->save();
    return $user->id();
  }

  public function newIdentityBasic($uid, $loginname, $password)
  {
    // TODO: 需要异常处理封装

    $salt = substr(uniqid(rand()), -8);
    $password = hash('sha256', $salt.$password);
    $identity = ORM::for_table('identity_basic')->create();
    $identity->set('uid', $uid);
    // loginname的唯一性验证需要在Controller中完成
    // 关于登录名：保持验证模块与主用户数据的分离，实际可以使用邮件地址
    $identity->set('loginname', $loginname);
    $identity->set('password', $password);
    $identity->set('salt', $salt);
    $identity->save();
  }

  public function validateBasic($loginname, $password)
  {
    $identity = ORM::for_table('identity_basic')
      ->where('loginname', $loginname)
      ->find_one();
    if ($identity === FALSE)
    {
      return FALSE;
    }

    $salt = $identity->salt;
    $pwd = $identity->password;
    return $pwd == hash('sha256', $salt.$password);
  }

  public function getUserByBasic($loginname)
  {
    $user = ORM::for_table('identity_basic')
      ->where('loginname', $loginname)
      ->join('users', array('identity_basic.uid', '=', 'users.uid'))
      ->find_one();
    if ($user === FALSE)
    {
      return FALSE;
    }
    return array(
      'uid' => $user->id(),
      'username' => $user->username,
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
      'username' => $user->username,
      'email' => $user->email);
  }

}
