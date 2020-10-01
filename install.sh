#!/bin/bash

if [ "$(whoami)" != "root" ]; then
	echo "Run this script as sudo or as root"
	exit 1
fi

apt-get update
apt-get -y install apache2 php php-gd php git

cd /var/www/
git clone https://github.com/dmpop/mejiro.git
chown www-data:www-data -R mejiro/

echo "All done!"
