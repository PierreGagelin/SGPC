#!/usr/bin/env bash

docker exec sgpc service apache2 stop

docker-compose down
