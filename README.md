##About Photocrumbs

Photocrumbs is a forgetful single-file PHP app for  instantly publishing photos as a chronological stream. The app features expiration functionality. When enabled, it deletes photos that are older than a user-defined number of days -- hence the *forgetful* moniker.

##Features

* **Simplicity** The entire app consists of a single PHP file (plus a fav icon). Photocrumbs requires no installation, and it can be deployed on any web server with PHP and the GD library.
* **Instant and easy photo publishing** Upload photos, and Photocrumbs does the rest.
* **Automatic thumbnail generation** Photocrumbs automatically generates thumbnails for faster preview.
* **Basic EXIF data** The app extract and displays basic EXIF info for each photo, including aperture, shutter speed, ISO, and timestamp.
* **Optional description** You can add a description to each photo by creating an accompanying *.php* file. The app can also read and display description from the photo's *UserComment* EXIF field.

##Requirements

* Apache server with PHP5 and the GD library
* Git (optional)

##Installation and Usage

1. Install the required packages. On Debian and Ubuntu, this can be done by running the following command as root: `apt-get install apache2 php5 php5-gd git`
2. In the terminal, switch to the root directory of the server (e.g., */var/www*) and use the `git clone git@github.com:dmpop/photocrumbs.git` command to fetch the latest source code. Alternatively, you can download the ZIP archive an extract it into the document root of the server.
3. Open the *index.php* file in a text editor and edit settings. This step is optional.
4. Put photos into the *photos* directory (*.jpg* file only).
5. Point the browser to *http://127.0.0.1/photocrumbs/* (replace *127.0.0.1* with the actual IP address or domain name of your server).

You can add descriptions to photos by creating accompanying *.php* files. For example, to add a description to the *F994362-R1-14-15.jpg* photo, create the *F994362-R1-14-15.php* file containing a short text. You can use HTML markup for formatting the text.
