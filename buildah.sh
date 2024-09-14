#!/usr/bin/env bash

container=$(buildah from docker.io/debian)
buildah run $container apt update
buildah run $container apt install -y php8.2 php8.2-gd php8.2-common
buildah copy $container . /usr/src/pellicola/
buildah config --workingdir /usr/src/pellicola $container
buildah config --port 8000 $container
buildah config --cmd "php -S 0.0.0.0:8000" $container
buildah config --label description="Pellicola container image" $container
buildah config --label maintainer="dmpop@cameracode.coffee" $container
buildah config --label version="0.1" $container
buildah commit --squash $container pellicola
buildah rm $container
