Photocrumbs is a simple PHP app for publishing photos on the web.

##Requirements

* Apache server with PHP5
* Git (optional)

##Installation and Usage

1. Install the required packages. On Debian and Ubuntu, this can be done by running the following command as root: `apt-get install apache2 php5 git`
2. In the terminal, switch to the root directory of the server (e.g., */var/www*) and use the `git clone git@github.com:dmpop/photocrumbs.git` command to fetch the latest source code.
3. Open the *config.php* file in a text editor and modify the required values.
4. Put photos into the *photocrumbs/photos* directory.
5. Point the browser to *http://127.0.0.1/photocrumbs/* (replace *127.0.0.1* with the actual IP address or domain name of your server).
