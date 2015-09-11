# Formstack
Production ready application stack for a City Tempature API requested by [Formstack](http://formstack.com)

## Description

This application stack uses [gearmand](http://gearman.org/) worker to provide data to a [PHP](http://www.php.net) web API running on [Apache](https://www.apache.org/) to return a tempature for a given city in [JSON](http://json.org/) data structure.

Example API Usage:
```
curl "http://localhost/temp.php?city=sandiego"
{"temp":"79"}
```

## Containers

There are many benefits to using containers for this application stack over a traditional monolithic server install

- Partitioning of services, following the [Unix Philosophy](http://www.catb.org/esr/writings/taoup/html/ch01s06.html) of doing one thing well
- Versioned and tagged container images, adding rollback and [blue-green](https://blog.codeship.com/easy-blue-green-deployments-on-amazon-ec2-container-service/) capabilities
- Consistent, portable, and repeatable infrastructure
  - containers are [cattle, not pets](https://blog.engineyard.com/2014/pets-vs-cattle)
- Runs in development and production environments identically across supported Linux operating systems and cloud providers

This application stack is composed of three [Docker](https://www.docker.com/) containers all based on the [Debian Jessie](https://hub.docker.com/_/debian/) official image.

Containers:
- [ecliptik/gearmand](https://hub.docker.com/r/ecliptik/gearmand/)
  - gearmand job server
- [ecliptik/worker](https://hub.docker.com/r/ecliptik/worker/)
  - [worker/worker.php] PHP script to fetch tempatures running in gearmand
- [ecliptik/temp](https://hub.docker.com/r/ecliptik/temp/)
  - Apache server with PHP enabled and serving [temp/temp.php]

The `worker` and `temp` containers are linked to `gearmand`, which only exposes the gearmand port 4730 to these two containers for increased security. The `worker` and `temp` containers will log to stdout/stderr which is  accessed via the docker logs command.

## Running in Development Locally

[Docker Compose](https://docs.docker.com/compose/) makes launching the container application stack locally extremely easy and this repository contains a [docker-compose.yml] file that will work identically on a local development and AWS production environment.

```
docker-compose up
Creating formstack_gearmand_1...
Creating formstack_worker_1...
Creating formstack_temp_1...
Attaching to formstack_gearmand_1, formstack_worker_1, formstack_temp_1
worker_1   | Waiting for a job...
```

## Running in Production on AWS

Using [docker-machine](https://docs.docker.com/machine/) the application stack can quickly deploy to AWS for production usage,

- Install docker-machine on your system
- create a docker user in the IAM console on AWS
  - grant full EC2 access
  - copy it's security credentials
- Find the AWS region and VPC ID
- Create a new security group called `docker-macine`
  - Open ports 22, 2376, and 80 to 0.0.0.0/0
- Configure your environment with the docker user security credentials from the IAM console

```
export AWS_ACCESS_KEY_ID=<Secret>
export AWS_SECRET_ACCESS_KEY=<Super_Top_Secret>
export AWS_VPC_ID=<vpc-ID>
```

- Create a new instance named `formstack` using docker-machine, and passing the macthing availability zone id (a,b,c,d, etc) for your VPC,

```
docker-machine -D create --driver amazonec2 \
--amazonec2-access-key $AWS_ACCESS_KEY_ID \
--amazonec2-secret-key $AWS_SECRET_ACCESS_KEY \
--amazonec2-vpc-id $AWS_VPC_ID \
--amazonec2-security-group "docker-machine" \
--amazonec2-zone a \
formstack
```

- Wait will the instance is created, and when completed re-initialize your enviornment to use the new Docker host endpoint,
```
eval "$(docker-machine env formstack)"
```

- Find out the IP of the new instance
```
docker-machine ip formstack
```

- Run docker-compose detacted on the new instance,
```
docker-compose up -d
```

- When the stack is completed, connect to the API to test
```
curl "http://localhost/temp.php?city=sandiego"
{"temp":"79"}
```

## Future Features

This stack is very basic, but allows for consistent, portable, and repeatable deployments in a local development environment and production cloud environment, however there are still many things to do that can improve performance and reliability.

Some ideas are,

- Use [memcached](http://memcached.org/) or [redis](http://redis.io/) to cache a tempature value for a period of time, lessening the calls to the upstream tempature provider
- Dynamically create new gearmand containers when load is increased
- Re-tool the [worker/worker.php] and [temp/temp.php] gearmand settings to use multiple gearmand containers and dynamically scale on-demand
- Leverage additional AWS features such as [Amazon Container Service](https://aws.amazon.com/blogs/aws/cloud-container-management/), [Elastic Beanstalk](https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/create_deploy_docker_ecs.html), [Cloud Watch](https://aws.amazon.com/cloudwatch/) and other products

## Additional Information

- http://networkstatic.net/docker-machine-provisioning-on-aws/
- https://developer.rackspace.com/blog/dev-to-deploy-with-docker-machine-and-compose/
