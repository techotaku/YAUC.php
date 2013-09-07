php ./test/make-fpm-cfg.php $USER `pwd`/.deployment/php-fpm.sock > ./.deployment/php-fpm.conf
php ./test/make-nginx-cfg.php `pwd` `pwd`/.deployment/php-fpm.sock > ./.deployment/nginx.conf
cp ./.deployment/nginx.conf ~/openresty/nginx/conf/nginx.conf