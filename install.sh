#!/bin/bash
apt-get update

export MYSQL_ROOT_PASSWORD=password
debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_ROOT_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD"

apt-get install -y apache2 libapache2-mod-fcgid php-fpm php php-cli php-curl php-gd php-readline php-bcmath php-imagick php-mysql mariadb-server git-core bindfs composer
# Set up Apache
a2enmod proxy_fcgi setenvif
a2enconf php7.2-fpm
systemctl restart apache2
chown -R www-data:www-data /var/www
# Set up Database
echo "CREATE DATABASE db;" | mysql -u root
echo "GRANT ALL PRIVILEGES ON db.* TO 'db'@'localhost' IDENTIFIED BY 'db';" | mysql -u root
echo "FLUSH PRIVILEGES;" | mysql -u root
# Set up Website
cd /var/www
rm -Rf html
echo "/vagrant/var/www  /var/www   fuse.bindfs _netdev,force-user=www-data,force-group=www-data,perms=0644:a+D 0 0" >> /etc/fstab
mount -a