cd .deployment
wget http://openresty.org/download/ngx_openresty-1.4.2.1.tar.gz
tar xzf ngx_openresty-1.4.2.1.tar.gz
cd ngx_openresty-1.4.2.1
./configure --prefix=$HOME/openresty --with-luajit
make
make install
cd ../..