#!/usr/bin/env bash

docker-compose up -d

docker exec sgpc service apache2 start
docker exec sgpc service mysql start
