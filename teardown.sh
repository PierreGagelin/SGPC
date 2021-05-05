#!/usr/bin/env bash

# Forbid error and undefined variable
set -e
set -u

# Run catch before end of program
trap 'catch ${?}' EXIT

# Print result of program
function catch()
{
    if [ "${1}" -eq 0 ]
    then
        echo ""
        echo "SGPC INFO: Finished to tear project down"
    else
        echo ""
        echo "SGPC ERROR: Failed to tear project down" 1>&2
    fi
}

docker exec sgpc service mysql stop
docker exec sgpc service apache2 stop

docker container stop sgpc
