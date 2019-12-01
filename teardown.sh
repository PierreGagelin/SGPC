#!/usr/bin/env bash

docker exec sgpc service mysql stop
docker exec sgpc service apache2 stop

docker-compose down
