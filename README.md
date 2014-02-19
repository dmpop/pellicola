Photocrumbs is a simple PHP app for publishing photos on the web.

##Requirements

* Apache server with PHP5
* Git (optional)

##Installation and Usage

1. Install the required packages. On Debian and Ubuntu, this can be done by running the following command as root: `apt-get install apache2 php5 git`
2. In the terminal, switch to the root directory of the server (e.g., */var/www*) and use the `git clone git@github.com:dmpop/photocrumbs.git` command to fetch the latest source code.
3. Open the *index.php* file in a text editor and edit settings, if needed.
4. Put photos into the *photos* directory.
5. Make sure that the *photos/thumbs* directory is writable by the server.
6. Point the browser to *http://127.0.0.1/photocrumbs/* (replace *127.0.0.1* with the actual IP address or domain name of your server).

You can add descriptions to a photos by creating *.php* files. For example, to add a description to the *F994362-R1-14-15.jpg* photo, create the *F994362-R1-14-15.php* file containing a short text. You can use HTML markup for formatting the text.
