#!/bin/bash
apt-get update
apt-get -y install lighttpd php5 php5-gd git
cd /var/www/
git clone https://github.com/dmpop/photocrumbs.git
chown www-data:www-data -R photocrumbs/
echo "All done!"
