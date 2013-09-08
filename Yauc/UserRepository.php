<?php
namespace Yauc;

/**
 * User Repository
 */
class UserRepository
{

  public function __construct()
  {
  }

  public function validateBasic($username, $password)
  {
  	// @fake
  	return TRUE;
  }

  public function getUserByName($username)
  {
  	// @fake
  	return array(
      'uid' => 10001,
      'username' => $username,
      'display' => '='.$username.'=');
  }

}
