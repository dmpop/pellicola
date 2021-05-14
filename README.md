## About Mejiro

Mejiro is an easy-to-use PHP web app for instant photo publishing.

<img src="mejiro.png" alt="Mejiro">

The [Linux Photography](https://gumroad.com/l/linux-photography) book provides detailed information on using Mejiro. Get your copy at [Google Play Store](https://play.google.com/store/books/details/Dmitri_Popov_Linux_Photography?id=cO70CwAAQBAJ) or [Gumroad](https://gumroad.com/l/linux-photography).

<img src="https://i.imgur.com/qP9Z9mQ.jpg" title="Linux Photography book" width="200"/>

## Features

- **Simplicity** Mejiro requires no installation, and it can be deployed on any web server with PHP.
- **Instant and easy photo publishing** Upload photos, and Mejiro does the rest.
- **Responsive design** Mejiro works well on mobile devices.
- **Pagination** Mejiro automatically splits photo collection into pages. You can specify the desired number of photos per page.
- **Basic EXIF data** The app extracts and displays basic EXIF info for each photo, including aperture, shutter speed, and ISO.
- **Display geographical coordinates on OpenStreetMap or Google Maps** For geotagged photos, you can view their exact locations on OpenStreetMap.
- **Optional description text** You can add a description to each photo by creating an accompanying *.txt* file. The app can also read and display descriptions from the photo's *UserComment* EXIF field.
- **Automatic language detection** Mejiro automatically detects the browser language and picks the description text file with the appropriate language prefix.
- **Link to RAW** For each photo in the JPEG format, you can upload the accompanying RAW file, and Mejiro automatically adds a link to it. This feature can be disabled.
- **Password protection** Mejiro allows you to protect the published contents with a password. The application supports multiple passwords, so you can grant and revoke temporary access.
- **Support for subfolders** Photos inside the default photo directory can be organized into subfolders.
- **Access keys** The application supports access keys for common actions.

## Requirements

* A web server with PHP5 or higher (Tested with Apache and lighttpd)
* PHP libraries: gd, exif, imagick
* Git (optional)

## Installation and usage

1. Install the required packages. On Debian and Ubuntu, this can be done by running the following command as root: `apt install apache2 php php-gd php-imagick php-exif git`
2. In the terminal, switch to the root directory of the server (e.g., */var/www/html*) and use the `git clone https://github.com/dmpop/mejiro.git` command as root to fetch the latest source code. Alternatively, you can download the ZIP archive and extract it into the document root of the server.
3. Open the *config.php* file in a text editor and edit settings.
4. Put photos into the *photos* directory (*.jpg*, *jpeg*, *.JPG*, and *.JPEG* as well as RAW files).
5. Make the *mejiro* directory writable by the server using the `chown www-data -R mejiro` command as root.
6. Point the browser to *http://127.0.0.1/mejiro/* (replace *127.0.0.1* with the actual IP address or domain name of your server).

You can add descriptions to photos by creating accompanying *.txt* files. For example, to add a description to the *F994362-R1-14-15.jpg* photo, create the *F994362-R1-14-15.txt* file containing a short text. You can use HTML markup for formatting the text. To add description files in other languages, use the appropriate language prefix as follows: *de-F994362-R1-14-15.txt* (for German), *ja-F994362-R1-14-15.txt* (for Japanese), etc.

## Run Mejiro in a container

1. Install [Podman](https://podman.io) and [Buildah](https://buildah.io).
2. Create a directory for your photos on the host machine.
3. Switch to the _mejiro_ directory and build an image using the `./buildah.sh` command.
4. Run a container on port 8000: `podman run -d --rm -p 8000:8000 -v /path/to/photos:/usr/src/mejiro/photos:rw mejiro` (replace _/path/to/photos_ with the actual path to the directory on the host containing photos).
5. Point the browser to _http://127.0.0.1:8000_ (replace _127.0.0.1_ with the actual IP address or domain name of the machine running the container).

## Author

Dmitri Popov ([dmpop@linux.com](mailto:dmpop@linux.com))

## License

Mejiro is released under the [GNU General Public License version 3](http://www.gnu.org/licenses/gpl-3.0.en.html) license.
