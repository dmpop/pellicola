## About Pellicola

Pellicola is an easy-to-use PHP web app for instant photo publishing.

<img src="pellicola.jpg" alt="Pellicola">

The [Pellicola manual](https://dmpop.gumroad.com/l/pellicola-manual) provides detailed information on installing and using Pellicola. Get your copy at [Gumroad](https://dmpop.gumroad.com/l/pellicola-manual).

<img src="https://cameracode.coffee/uploads/pellicola-manual.png" title="Pellicola" width="300"/>

## Features

- **Simplicity** Pellicola requires no installation, and it can be deployed on any web server with PHP.
- **Instant and easy photo publishing** Upload photos, and Pellicola does the rest.
- **Responsive design** Pellicola works well on mobile devices.
- **Pagination** Pellicola automatically splits photo collection into pages. You can specify the desired number of photos per page.
- **Search** Basic search functionality makes it possible to find photos by their file names and descriptions.
- **Basic EXIF data** The app extracts and displays basic EXIF info for each photo, including aperture, shutter speed, and ISO.
- **Show map** If the `$show_map` option is enabled, Pellicola displays an embedded map marking the position of the currently viewed geotagged photo.
- **OpenStreetMap links or Geo URIs** For geotagged photos, Pellicola displays either OpenStreetMap links or geo URIs that show the exact locations where the photos were taken either in OpenStreetMap or in the default map application.
- **Optional album description** If a _preamble.html_ file exists in the album's folder, Pellicola displays its contents as the album's description.
- **Optional photo description** You can add a description to each photo by creating an accompanying _.txt_ file. The app can also read and display descriptions from the photo's _UserComment_ EXIF field.
- **Automatic language detection** Pellicola automatically detects the browser language and picks the description text file with the appropriate language prefix.
- **Downloads** With the download option in the _config.php_ file enabled, visitors can download photos. Downloads are password protected by default. Password protection can be disabled by leaving `$download_password` in the _config.php_ file empty.
- **RAW downloads** For each photo in the JPEG format, you can upload the accompanying RAW file, and Pellicola automatically adds a download link to it when the download option is enabled.
- **Support for subfolders** Photos inside the default photo directory can be organized into subfolders.
- **Statistics** View basic statistics: camera models, focal length stats, the total number of photos and RAW files, disk usage, and the total number of downloads.
- **Access keys** The application supports [access keys](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey) for common actions.
- **Internationalization** support. Pellicola automatically detects and picks the right language. Localizing Pellicola is a matter of translating text strings in an appropriate _.ini_ file.
- **RSS feed** The application automatically generates an RSS feed with newly added photos.
- **Self-contained and GDPR-compliant** Pellicola has no external dependencies, and it doesn't collect any user data.

## Requirements

* A web server with PHP7 or higher (tested with Apache and lighttpd)
* PHP libraries: GD, EXIF
* Git (optional)

## Installation and usage

1. On Debian, Ubuntu, and Raspberry Pi, install Pellicola by running the following command as root: `curl -sSL https://raw.githubusercontent.com/dmpop/pellicola/main/install.sh | bash`. On other distributions, install the required packages and clone the project's Git repository using the command `git clone https://github.com/dmpop/pellicola.git` as root. Alternatively, you can download the ZIP archive and extract it into the document root of the server.
2. Open the *config.php* file in a text editor and edit settings.
3. Put photos into the *photos* directory (_.jpg_, _.jpeg_, _.JPG_, and _.JPEG_ as well as RAW files).
4. Make the _pellicola_ directory writable by the server by running `chown www-data -R pellicola` as root.
5. Point the browser to _http://127.0.0.1/pellicola/_ (replace _127.0.0.1_ with the actual IP address or domain name of your server).

You can add descriptions to photos by creating accompanying _.txt_ files. For example, to add a description to the _F994362-R1-14-15.jpg_ photo, create the _F994362-R1-14-15.txt_ file containing a short text. You can use HTML markup for formatting the text. To add description files in other languages, use the appropriate language prefix as follows: _de-F994362-R1-14-15.txt_ (for German), _ja-F994362-R1-14-15.txt_ (for Japanese), etc.

## Run Pellicola in a container

1. Install [Podman](https://podman.io) and [Buildah](https://buildah.io).
2. Create a directory for your photos on the host machine.
3. Switch to the _pellicola_ directory and build an image using the `./buildah.sh` command.
4. Run a container on port 8000: `podman run -d --rm -p 8000:8000 -v /path/to/photos:/usr/src/pellicola/photos:rw pellicola` (replace _/path/to/photos_ with the actual path to the directory on the host containing photos).
5. Point the browser to _http://127.0.0.1:8000_ (replace _127.0.0.1_ with the actual IP address or domain name of the machine running the container).

## Author

Dmitri Popov ([dmpop@cameracode.coffee](mailto:dmpop@cameracode.coffee))

## Acknowledgments

- Icons: [Iconoir](https://iconoir.com/)
- Internationalization: [php-i18n](https://github.com/Philipp15b/php-i18n)

## License

Pellicola is released under the [GNU General Public License version 3](http://www.gnu.org/licenses/gpl-3.0.en.html) license.
