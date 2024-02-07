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

# Don't start as root
if [[ $EUID -eq 0 ]]; then
   echo "Run the script as a regular user"
   exit 1
fi

if [ -z "$(command -v apt)" ]; then
   echo "Looks like you can't use this script with your system."
   exit 1
fi

# Update source and perform the full system upgrade
sudo apt update
sudo apt full-upgrade -y
sudo apt update

# Install the required packages
sudo apt install -y apache2 php php-gd php-common git rsync vnstat vnstati

# Remove obsolete packages
sudo apt autoremove -y

# Clone the Git repo
cd
git clone https://github.com/dmpop/pellicola.git

# Copy Pellicola to the document root
rsync -avh --delete --progress pellicola/ /var/www/html

# Finish
echo "All done!"
