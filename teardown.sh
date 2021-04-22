#!/usr/bin/env bash

# Forbid error and undefined variable
set -e
set -u

docker exec sgpc service mysql stop
docker exec sgpc service apache2 stop

docker container stop sgpc
