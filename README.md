# Formstack
Production ready application Stack for a City Tempature API requested by [Formstack](http://formstack.com)

## Description

This application stack uses [gearmand](http://gearman.org/) worker to provide data to a [PHP](http://www.php.net) web API running on [Apache](https://www.apache.org/) to return a tempature for a given city in [JSON](http://json.org/) data structure.

Example API Usage:
```
curl "http://localhost/temp.php?city=sandiego"
{"temp":"79"}
```

## Containers

This application stack is composed of three [Docker](https://www.docker.com/) containers all based on the [Debian Jessie](https://hub.docker.com/_/debian/) official image.

Containers:
- [ecliptik/gearmand](https://hub.docker.com/r/ecliptik/gearmand/)
  - gearmand job server
- [ecliptik/worker](https://hub.docker.com/r/ecliptik/worker/)
  - [worker/worker.php] PHP script to fetch tempatures running in gearmand
- [ecliptik/temp](https://hub.docker.com/r/ecliptik/temp/)
  - Apache server with PHP enabled and serving [temp/temp.php]

The `worker` and `temp` containers are linked to `gearmand`, which only exposes the gearmand port 4730 to these two containers and nothing else for increased security.

## Running Containers Locally

The following three Docker commands will run and link the containers,

`gearmand`
```
docker run -d --name gearmand -P ecliptik/gearmand
```

`worker`
```
docker run -d --name worker --link gearmand:gearmand ecliptik/worker
```

`temp`
```
docker run -d --name temp -p 8080:80 --link gearmand:gearmand ecliptik/temp
```

## Running Containers with Docker Compose
[Docker Compose](https://docs.docker.com/compose/) makes launching the container application stack much easier and this repository contains a [docker-compose.yml] file.

```
docker-compose up
Creating formstack_gearmand_1...
Creating formstack_worker_1...
Creating formstack_temp_1...
Attaching to formstack_gearmand_1, formstack_worker_1, formstack_temp_1
worker_1   | Waiting for a job...
```

## Using the API Stack

When all three containers are running in the stack, the API is available at `http://localhost:8080/temp?city=CITYNAME`, where CITYNAME is the appropriate name of a city.

For example, to get the tempature of San Diego using curl
```
curl "http://localhost:8080/temp.php?city=sandiego"
{"temp":"79"}
```

The logs of the `worker` and `temp` containers will show expected process information.

## Using With AWS

## Future Features

While this repository currently contains a basic stack, there are still many improve performance and reliability.

Some ideas are,

- Used [memcached](http://memcached.org/) or [redis](http://redis.io/) to cache a tempature value for a period of time, lessening the calls to the upstream tempature provider
- Dynamically create new gearmand containers when load is increased
- Re-tool the [worker/worker.php] and [temp/temp.php] gearmand settings to use multiple gearmand containers and dynamically scale on-demand
- Leverage AWS features such as [Elastic Beanstalk](https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/create_deploy_docker_ecs.html)
