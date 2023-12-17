# Multi database application project based on Symfony and Doctrine

An implementation to have a single application running that can connect to multiple databases. Some commands to create
and maintain all the databases, and an example simple logic to map requests to the correct database.

Done with Symfony 6.4 and Doctrine 2.11 runnig on PHP 8.2

## Installation

Pull the project and run:

```shell
docker-compose -f docker-compose.yaml up -d
```

or

```shell
docker compose -f docker-compose.yaml up -d
```

depending on your docker version.

Then shell into the php container ```docker exec -ti multi-database-site-app-1 bash``` and run:

```shell
composer install
```

to install dependencies.

The controller would be available at localhost:8081 and the created databases will be available at localhost:3306.

You can change the ports in the docker-compose.yaml file.

## Disclaimer
This project has been created just as code demostration of this post. It`s not supported and not intended to be used at production environments.
Some code has been pasted from other projects, so you may find some unused code.
