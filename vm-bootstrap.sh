#!/usr/bin/env bash
set -ex
#
#PHP_VERSION="$1"
APPENV=dev
DBHOST=localhost
DBNAME=app
DBUSER=root
DBPASSWD=null

# Prepare the machine
#sudo apt-get remove --purge getdeb-repository
#sudo apt-get -y autoremove
sudo apt-get update

# mysql
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
sudo apt-get install -y vim curl python-software-properties
sudo apt-get update
sudo apt-get -y install mysql-server
sudo sed -i "s/^bind-address/#bind-address/" /etc/mysql/my.cnf
mysql -u root -proot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES;"
sudo /etc/init.d/mysql restart

#if [ "$PHP_VERSION" == "7" ]; then
source /home/vagrant/provision/php7.sh
#else
#   source /home/vagrant/provision/php5.sh
#fi

# Common Apache settings
sudo a2enmod rewrite

# Get the source code
#if [ ! -d ~/dev/learnzf2 ]; then
#   cd ~/dev
#   git clone https://github.com/slaff/learnzf2.git
#fi

cd /home/vagrant;
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
sudo composer self-update
composer install

sudo ln -sf /home/vagrant/provision/apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf
sudo /etc/init.d/apache2 restart

sudo update-rc.d apache2 defaults
sudo update-rc.d apache2 enable