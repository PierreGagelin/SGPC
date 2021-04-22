#!/usr/bin/env bash

#
# Set project environment up
#

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
        echo "SGPC INFO: Finished to set project up"
    else
        echo ""
        echo "SGPC ERROR: Failed to set project up" 1>&2
    fi
}

# Build a container with options
function docker_build()
{
    local dir_docker=

    # Get Docker directory
    dir_docker=$(dirname "${BASH_SOURCE[0]}")
    dir_docker=$(readlink -e "${dir_docker}/docker")

    docker image build --tag sgpc "${dir_docker}"
}

# Run a container with options
function docker_run()
{
    local dir_src=
    local opt_list=()

    # Get sources directory
    dir_src=$(dirname "${BASH_SOURCE[0]}")
    dir_src=$(readlink -e "${dir_src}/src")

    opt_list+=("--interactive")
    opt_list+=("--tty")
    opt_list+=("--detach")
    opt_list+=("--rm")
    opt_list+=("--name" "sgpc")
    opt_list+=("--hostname" "sgpc")
    opt_list+=("--mount" "type=bind,src=${dir_src},dst=/workspace/src")
    opt_list+=("--publish" "80:80")

    # Allow X usage to any user
    xhost + > /dev/null

    docker container run "${opt_list[@]}" sgpc
}

# Prepare workspace for SGPC
function sgpc_workspace()
{
    # Clean HTML folder
    docker container exec sgpc rm -rf /var/www/html
    docker container exec sgpc mkdir /var/www/html

    # Get PhpSpreadsheet library up
    docker container exec sgpc composer require --working-dir=/var/www/html phpoffice/phpspreadsheet

    # Add links to sources into HTML folder
    docker container exec sgpc find /workspace/src -mindepth 1 -maxdepth 1 -type f -exec ln -sft /var/www/html {} \;

    # Create output directories
    docker container exec sgpc mkdir -p /var/www/html/Auvergne
    docker container exec sgpc mkdir -p /var/www/html/Ile-de-France
    docker container exec sgpc mkdir -p /var/www/html/Hauts-de-France
    docker container exec sgpc mkdir -p /var/www/html/National

    # Reset rights
    docker container exec sgpc chown -R www-data:www-data /var/www/html
}

# Build container image
docker_build

# Clean useless containers and images
docker container prune --force
docker image prune --force

# Run the container
docker_run

# Start services
docker container exec sgpc service apache2 start
docker container exec sgpc service mysql start

# Prepare workspace
sgpc_workspace
