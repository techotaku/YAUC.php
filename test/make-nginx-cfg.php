<?php
  $root = $argv[1];
  $sock = $argv[2];
?>
worker_processes  1;

events {
  worker_connections  1024;
}

http {
  include       mime.types;
  default_type  application/octet-stream;

  sendfile        on;
  keepalive_timeout  65;

  index index.php;

  server {
    listen       8080;
    root         <?php echo $root ?>;

    if (!-e $request_filename) {
      rewrite  ^(.*)$  /index.php/$1  last;
    }

    location ~ \.php($|/) {      
      fastcgi_pass   unix:<?php echo $sock ?>;
      fastcgi_index  index.php;
      fastcgi_param  SCRIPT_FILENAME  <?php echo $root ?>$fastcgi_script_name;
      include        fastcgi_params;
    }
  }

}