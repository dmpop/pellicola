#!/usr/bin/env bash

container=$(buildah from opensuse/leap)
buildah run $container zypper update
buildah run $container zypper -n install php7 php7-gd php-exif
buildah copy $container . /usr/src/pellicola/
buildah config --workingdir /usr/src/pellicola $container
buildah config --port 8000 $container
buildah config --cmd "php -S 0.0.0.0:8000" $container
buildah config --label description="Pellicola container image" $container
buildah config --label maintainer="dmpop@cameracode.coffee" $container
buildah config --label version="0.1" $container
buildah commit --squash $container pellicola
buildah rm $container
