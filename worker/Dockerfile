FROM debian:jessie
MAINTAINER Micheal Waltz <ecliptik@gmail.com>

#Setup basic environment
ENV DEBIAN_FRONTEND=noninteractive LANG=en_US.UTF-8 LC_ALL=C.UTF-8 LANGUAGE=en_US.UTF-8

#Instal required packages
RUN apt-get -q update && \
    apt-get install -qy --force-yes \
    php5-cli \
    php5-curl \
    php5-gearman

#Set workdir to /app
WORKDIR /app

#Copy contents in /app dir
COPY / /app/

#Run worker.php
ENTRYPOINT ["php", "/app/worker.php" ]
