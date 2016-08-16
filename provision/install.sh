#!/usr/bin/env bash

BLUE_COLOR="\E[1;34m"

echo -e "${BLUE_COLOR}Updating repositories..."
apt-get update &>/dev/null

echo -e "${BLUE_COLOR}Installing Apache..."
apt-get install -y apache2 &>/dev/null
sudo a2enmod rewrite &>/dev/null
sudo rm -f /etc/apache2/sites-available/000-default.conf
sudo cp /vagrant/provision/000-default.conf /etc/apache2/sites-available/
sudo apt-get install apache2-mpm-itk &>/dev/null
sudo service apache2 restart &>/dev/null

echo -e "${BLUE_COLOR}Installing mysql..."
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
sudo apt-get -y install mysql-server &>/dev/null

mysql -uroot -proot <<MYSQL_SCRIPT
use mysql;
create user 'root'@'10.0.2.2' identified by 'root';
grant all privileges on *.* to 'root'@'10.0.2.2' with grant option;
flush privileges;
MYSQL_SCRIPT

sudo sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf
sudo /etc/init.d/mysql restart &>/dev/null

echo -e "${BLUE_COLOR}Creating database..."
mysql -uroot -proot -e "create database if not exists symfony"

echo -e "${BLUE_COLOR}Installing PHP..."
sudo apt-get install -y php5 libapache2-mod-php5 php5-mcrypt php5-mysql &>/dev/null
sudo service apache2 restart &>/dev/null