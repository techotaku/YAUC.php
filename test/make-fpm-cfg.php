<?php
  $user = $argv[1];
  $sock = $argv[2];
?>
[global]
pid = /home/<?php echo $user ?>/php5-fpm.pid

; Error log file
; If it's set to "syslog", log is sent to syslogd instead of being written
; in a local file.
; Note: the default prefix is /var
; Default Value: log/php-fpm.log
;error_log = log/php-fpm.log
error_log = /home/<?php echo $user ?>/php5-fpm-error.log

[travis]
user = <?php echo $user ?> 
group = <?php echo $user ?> 
listen = <?php echo $sock ?> 
pm = static
pm.max_children = 2

php_admin_value[memory_limit] = 128M