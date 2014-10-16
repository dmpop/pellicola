#!/bin/bash
apt-get update
apt-get -y install apache2 php5 php5-gd git
cd /var/www/
git clone https://github.com/dmpop/mejiro.git
chown www-data:www-data -R mejiro/
echo "All done!"
