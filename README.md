## About Mejiro

Mejiro is a forgetful single-file PHP web app for instant photo publishing. The app features expiration functionality. When enabled, it deletes photos older than a user-defined number of days -- hence the *forgetful* moniker.

<img src="mejiro.png" alt="Mejiro">

## Features

- **Simplicity** The entire app consists of a single PHP file (plus a fav icon). Mejiro requires no installation, and it can be deployed on any web server with PHP5 and the GD library.
- **Instant and easy photo publishing** Upload photos, and Mejiro does the rest.
- **Automatic thumbnail generation** Mejiro automatically generates thumbnails for faster preview.
- **Expiration functionality** The app can automatically delete photos older than a specific number of days.
- **Basic EXIF data** The app extracts and displays basic EXIF info for each photo, including aperture, shutter speed, and ISO.
- **IPTC keywords** Mejiro parses IPTC metadata and displays keywords assigned to photos.
- **Display geographical coordinates on OpenStreetMap or Google Maps** For geotagged photos, you can view their exact locations on OpenStreetMap.
- **Short URLs** Mejiro uses the [is.gd](http://is.gd/) service to generate short URLs for easy sharing.
- **Optional description text** You can add a description to each photo by creating an accompanying *.txt* file. The app can also read and display descriptions from the photo's *UserComment* EXIF field.
- **Link to RAW** For each photo in the JPEG format, you can upload the accompanying RAW file, and Mejiro automatically adds a link to it.
- **Support for subfolders** Photos inside the default photo directory can be organized into subfolders.
- **Automatic language detection** Mejiro automatically detects the browser language and picks the description text file with the appropriate language prefix.
- **Keyboard shortcuts** The application supports keyboard shortcuts for common actions.
- **CrazyStat integration** Mejiro supports integration with the [CrazyStat](http://en.christosoft.de/CrazyStat) web analytics software.

## Requirements

* A web server with PHP5 and the GD library. (Tested with Apache and lighttpd)
* cURL
* Git (optional)

## Installation and Usage

1. Install the required packages. On Debian and Ubuntu, this can be done by running the following command as root: `apt-get install apache2 php5 php5-gd curl git`
2. In the terminal, switch to the root directory of the server (e.g., */var/www*) and use the `git clone https://github.com/dmpop/mejiro.git` command as root to fetch the latest source code. Alternatively, you can download the ZIP archive and extract it into the document root of the server.
3. Open the *index.php* file in a text editor and edit settings. This step is optional.
4. Put photos into the *photos* directory (*.jpg*, *jpeg*, *.JPG*, and *.JPEG* files only).
5. Make the *mejiro* directory writable by the server using the `chown www-data -R mejiro` command as root.
6. Point the browser to *http://127.0.0.1/mejiro/* (replace *127.0.0.1* with the actual IP address or domain name of your server).

You can add descriptions to photos by creating accompanying *.txt* files. For example, to add a description to the *F994362-R1-14-15.jpg* photo, create the *F994362-R1-14-15.txt* file containing a short text. You can use HTML markup for formatting the text. To add description files in other languages, use the appropriate language prefix as follows: *de-F994362-R1-14-15.txt* (for German), *ja-F994362-R1-14-15.txt* (for Japanese), etc.

To enable the expiration feature, change the *$expire = false;* line in the *index.php* script to *$expire = true;* and specify the desired expiration period by modifying the *$days* variable.

## Demo

A [Mejiro demo](http://dmpop.dhcp.io/mejiro/) is available for your viewing pleasure.

## Little Mejiro Book

Want to get the most out of Mejiro? Read the [Little Mejiro Book](http://scribblesandsnaps.com/little-mejiro-book/).
