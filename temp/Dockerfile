FROM debian:jessie
MAINTAINER Micheal Waltz <ecliptik@gmail.com>

#Setup basic environment
ENV DEBIAN_FRONTEND=noninteractive LANG=en_US.UTF-8 LC_ALL=C.UTF-8 LANGUAGE=en_US.UTF-8

#Install required packages
RUN apt-get -q update && \
    apt-get install -qy --force-yes \
    php5 \
    php5-gearman

#Set workdir to webroot
WORKDIR /var/www/html

#Copy php files into webroot
COPY / /var/www/html/

#Listen on port 80
EXPOSE 80

RUN ln -sf /dev/stdout /var/log/apache2/access.log
RUN ln -sf /dev/stderr /var/log/apache2/error.log

#Run apache
ENTRYPOINT [ "apache2ctl", "-D", "FOREGROUND" ]
