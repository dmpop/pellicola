#!/usr/bin/env bash

# Author: Dmitri Popov, dmpop@cameracode.coffee

#######################################################################
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#######################################################################

if [ "$(whoami)" != "root" ]; then
    echo "You must run this script as root"
    exit 1
fi

if [ -z "$(command -v apt)" ]; then
   echo "Looks like you can't use this script with your system."
   exit 1
fi

apt update
apt upgrade
apt install apache2 git php php-gd php-common libapache2-mod-php
apt install certbot python3-certbot-apache

echo "-----------------------"
echo " Specify new user name"
echo " Example: monkey"
echo " Press ENTER"
echo "-----------------------"
echo

read newuser

useradd -m $newuser
passwd $newuser
apt install sudo
usermod -aG sudo $newuser

echo "----------------------"
echo " Specify domain  name"
echo " Example: hello.xyz"
echo " Press ENTER"
echo "----------------------"
echo
read domain_name

touch /etc/apache2/sites-available/$domain_name.conf

echo "<VirtualHost *:80>" >/etc/apache2/sites-available/$domain_name.conf
echo "" >>/etc/apache2/sites-available/$domain_name.conf
echo "DocumentRoot /var/www/html/$domain_name" >>/etc/apache2/sites-available/$domain_name.conf
echo "ServerName $domain_name" >>/etc/apache2/sites-available/$domain_name.conf
echo "ServerAlias $domain_name" >>/etc/apache2/sites-available/$domain_name.conf
echo "Redirect permanent / https://$domain_name" >>/etc/apache2/sites-available/$domain_name.conf
echo "" >>/etc/apache2/sites-available/$domain_name.conf
echo "<Directory /var/www/html/$domain_name/>" >>/etc/apache2/sites-available/$domain_name.conf
echo "Options FollowSymlinks" >>/etc/apache2/sites-available/$domain_name.conf
echo "AllowOverride All" >>/etc/apache2/sites-available/$domain_name.conf
echo "Require all granted" >>/etc/apache2/sites-available/$domain_name.conf
echo "</Directory>" >>/etc/apache2/sites-available/$domain_name.conf
echo "" >>/etc/apache2/sites-available/$domain_name.conf
echo 'ErrorLog ${APACHE_LOG_DIR}/error.log' >>/etc/apache2/sites-available/$domain_name.conf
echo 'CustomLog ${APACHE_LOG_DIR}/access.log combined' >>/etc/apache2/sites-available/$domain_name.conf
echo "" >>/etc/apache2/sites-available/$domain_name.conf
echo "</VirtualHost>" >>/etc/apache2/sites-available/$domain_name.conf

sudo a2dissite 000-default
sudo a2ensite $domain_name.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo certbot --apache
sudo mkdir /var/www/html/$domain_name
sudo chown www-data:www-data -R /var/www/html/$domain_name

cd /var/www/html/$domain_name
git clone https://github.com/dmpop/mejiro.git
chown www-data:www-data -R mejiro/
