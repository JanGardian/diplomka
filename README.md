# diplomka

## installation
 - clone repository `git clone https://github.com/JanGardian/diplomka.git`
 - run `composer install` to install libraries
 - `mv app/config/config.local-example.neon app/config/config.local.neon`
 - setup DB connection parameters in app/config/config.local.neon
 - setup a vhost uncomment /etc/httpd/conf/httpd.conf -> uncomented "LoadModule vhost_alias_module modules/mod_vhost_alias.so"
 - down in httpd.conf :
 - `<VirtualHost *:80>
 ServerAdmin john.doe@diplomka
 DocumentRoot /var/www/html/web-project/www
 ServerName diplomka
 ErrorLog /var/www/html/web-project/log/diplomka-error_log
 CustomLog /var/www/html/web-project/log/diplomka-access_log common
 </VirtualHost>`
 - and run
