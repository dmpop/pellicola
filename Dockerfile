FROM docker.io/debian:latest
LABEL maintainer="dmpop@cameracode.coffee"
LABEL version="1.0"
LABEL description="Pellicola container image"
RUN apt update
RUN apt install -y php-cli php-gd php-common
COPY . /usr/src/pellicola
WORKDIR /usr/src/pellicola
EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000" ]
