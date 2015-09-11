FROM debian:jessie
MAINTAINER Micheal Waltz <ecliptik@gmail.com>

#Setup basic environment
ENV DEBIAN_FRONTEND=noninteractive LANG=en_US.UTF-8 LC_ALL=C.UTF-8 LANGUAGE=en_US.UTF-8

#Install required packages
RUN apt-get -q update && \
    apt-get install -qy --force-yes \
    gearman-job-server

#Listen on port 4730
EXPOSE 4730

#Run gearmand job server
ENTRYPOINT [ "gearmand", "--verbose=ERROR" ]
