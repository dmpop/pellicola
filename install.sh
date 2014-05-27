#!/bin/bash
apt-get update
apt-get -y install apache2 php5 php5-gd git
cd /var/www/
git clone https://github.com/dmpop/photocrumbs.git
sudo chown www-data:www-data -R photocrumbs/
echo "All done!"
