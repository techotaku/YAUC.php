<?php

return  array(
  'demodz' => array(
    'secret' => '',
    'login' => 'http://demodz.techotaku.net/sso.php?action=login_callback&ticket={ticket}',
    'logout' => 'http://demodz.techotaku.net/sso.php?action=logout_callback',
    'sync' => 'http://demodz.techotaku.net/api/uc.php',
    'type' => 'discuz'),
  'dz2' => array(
    'secret' => '',
    'login' => 'http://dz2.techotaku.net/sso.php?action=login_callback&ticket={ticket}',
    'logout' => 'http://dz2.techotaku.net/sso.php?action=logout_callback',
    'sync' => 'http://dz2.techotaku.net/api/uc.php',
    'type' => 'discuz')
  );