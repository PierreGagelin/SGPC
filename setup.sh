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
    local dir_build=
    local opt_list=()

    # Get directories
    dir_src=$(dirname "${BASH_SOURCE[0]}")
    dir_src=$(readlink -e "${dir_src}/src")

    dir_build=$(dirname "${BASH_SOURCE[0]}")
    dir_build=$(readlink -m "${dir_build}/../build/sgpc")

    mkdir -p "${dir_build}"

    opt_list+=("--interactive")
    opt_list+=("--tty")
    opt_list+=("--detach")
    opt_list+=("--rm")
    opt_list+=("--name" "sgpc")
    opt_list+=("--hostname" "sgpc")
    opt_list+=("--mount" "type=bind,src=/etc/timezone,dst=/etc/timezone")
    opt_list+=("--mount" "type=bind,src=/etc/localtime,dst=/etc/localtime")
    opt_list+=("--mount" "type=bind,src=${dir_src},dst=/workspace/src")
    opt_list+=("--mount" "type=bind,src=${dir_build},dst=/var/www/html")
    opt_list+=("--publish" "80:80")

    # Allow X usage to any user
    xhost + > /dev/null

    docker container run "${opt_list[@]}" sgpc
}

# Prepare workspace for SGPC
function sgpc_workspace()
{
    # Get libraries
    docker container exec sgpc composer require --working-dir=/var/www/html phpoffice/phpspreadsheet
    docker container exec sgpc composer require --working-dir=/var/www/html phpmailer/phpmailer

    # Add links to sources into HTML folder
    docker container exec sgpc find /workspace/src -mindepth 1 -maxdepth 1 -type f -exec ln -sft /var/www/html {} \;

    # Create output directories
    docker container exec sgpc mkdir -p /var/www/html/Ile-de-France
    docker container exec sgpc mkdir -p /var/www/html/Hauts-de-France
    docker container exec sgpc mkdir -p /var/www/html/National

    # Reset rights
    docker container exec sgpc chown -R www-data:www-data /var/www/html
}

# Create database and tables
function sgpc_database()
{
    local sql_cmd=

    # Create SGPC database
    docker container exec sgpc mysql -u root -e "CREATE DATABASE sgpc;"

    # Create account table
    sql_cmd="USE sgpc;"
    sql_cmd="${sql_cmd} CREATE TABLE account ("
    sql_cmd="${sql_cmd} user varchar(200) NOT NULL PRIMARY KEY,"
    sql_cmd="${sql_cmd} password varchar(200) NOT NULL,"
    sql_cmd="${sql_cmd} region varchar(200) NOT NULL,"
    sql_cmd="${sql_cmd} privileged int NOT NULL"
    sql_cmd="${sql_cmd} );"
    docker container exec sgpc mysql -u root -e "${sql_cmd}"

    # Create member table
    sql_cmd="USE sgpc;"
    sql_cmd="${sql_cmd} CREATE TABLE member ("
    sql_cmd="${sql_cmd} numero_adherent varchar(5) NOT NULL PRIMARY KEY,"
    sql_cmd="${sql_cmd} nom varchar(200) NOT NULL,"
    sql_cmd="${sql_cmd} prenom varchar(200) NOT NULL,"
    sql_cmd="${sql_cmd} cotis_payee varchar(3),"
    sql_cmd="${sql_cmd} date_paiement varchar(10),"
    sql_cmd="${sql_cmd} p_ou_rien varchar(1),"
    sql_cmd="${sql_cmd} cotis_payee_prec varchar(3),"
    sql_cmd="${sql_cmd} cotis_date_premiere varchar(4),"
    sql_cmd="${sql_cmd} cotis_date_derniere varchar(4),"
    sql_cmd="${sql_cmd} cotis_region varchar(18),"
    sql_cmd="${sql_cmd} adresse_1 varchar(200),"
    sql_cmd="${sql_cmd} adresse_2 varchar(200),"
    sql_cmd="${sql_cmd} code_postal varchar(200),"
    sql_cmd="${sql_cmd} commune varchar(200),"
    sql_cmd="${sql_cmd} ad varchar(6),"
    sql_cmd="${sql_cmd} profession varchar(5),"
    sql_cmd="${sql_cmd} region varchar(18) NOT NULL,"
    sql_cmd="${sql_cmd} echelon varchar(200),"
    sql_cmd="${sql_cmd} bureau_nat varchar(1),"
    sql_cmd="${sql_cmd} comite_nat varchar(1),"
    sql_cmd="${sql_cmd} tel_port varchar(10),"
    sql_cmd="${sql_cmd} tel_prof varchar(10),"
    sql_cmd="${sql_cmd} tel_dom varchar(10),"
    sql_cmd="${sql_cmd} fonc_nat_sgpc varchar(3),"
    sql_cmd="${sql_cmd} fonc_nat_ccse varchar(15),"
    sql_cmd="${sql_cmd} fonc_reg_sgpc varchar(2),"
    sql_cmd="${sql_cmd} fonc_reg_cse varchar(14),"
    sql_cmd="${sql_cmd} mail_priv varchar(200),"
    sql_cmd="${sql_cmd} mail_prof varchar(200),"
    sql_cmd="${sql_cmd} remarque_r varchar(200),"
    sql_cmd="${sql_cmd} remarque_n varchar(200),"
    sql_cmd="${sql_cmd} com_bud varchar(1),"
    sql_cmd="${sql_cmd} com_com varchar(1),"
    sql_cmd="${sql_cmd} com_cond varchar(1),"
    sql_cmd="${sql_cmd} com_ce varchar(1),"
    sql_cmd="${sql_cmd} com_dent varchar(1),"
    sql_cmd="${sql_cmd} com_ffass varchar(1),"
    sql_cmd="${sql_cmd} com_pharma varchar(1),"
    sql_cmd="${sql_cmd} com_ret varchar(1),"
    sql_cmd="${sql_cmd} naissance varchar(10),"
    sql_cmd="${sql_cmd} entree varchar(10),"
    sql_cmd="${sql_cmd} abcd varchar(1),"
    sql_cmd="${sql_cmd} c1 varchar(200),"
    sql_cmd="${sql_cmd} c2 varchar(200),"
    sql_cmd="${sql_cmd} c3 varchar(200),"
    sql_cmd="${sql_cmd} c4 varchar(200),"
    sql_cmd="${sql_cmd} c5 varchar(200),"
    sql_cmd="${sql_cmd} c6 varchar(200),"
    sql_cmd="${sql_cmd} c7 varchar(200),"
    sql_cmd="${sql_cmd} c8 varchar(200),"
    sql_cmd="${sql_cmd} c9 varchar(200),"
    sql_cmd="${sql_cmd} c10 varchar(200),"
    sql_cmd="${sql_cmd} c11 varchar(200),"
    sql_cmd="${sql_cmd} c12 varchar(200)"
    sql_cmd="${sql_cmd} );"
    docker container exec sgpc mysql -u root -e "${sql_cmd}"

    # Add accounts
    sql_cmd="USE sgpc;"
    sql_cmd="${sql_cmd} INSERT INTO account VALUES ('root', 'root', 'National', 1);"
    sql_cmd="${sql_cmd} INSERT INTO account VALUES ('user', 'user', 'Ile-de-France', 0);"
    docker container exec sgpc mysql -u root -e "${sql_cmd}"

    # Add members
    sql_cmd="USE sgpc;"
    sql_cmd="${sql_cmd} INSERT INTO member(numero_adherent, nom, prenom, region) VALUES ('AA001', 'SURLALUNE', 'Toto', 'Ile-de-France');"
    sql_cmd="${sql_cmd} INSERT INTO member(numero_adherent, nom, prenom, region) VALUES ('AA002', 'DANSTONCUL', 'Lulu', 'Hauts-de-France');"
    docker container exec sgpc mysql -u root -e "${sql_cmd}"

    # Create SQL user
    sql_cmd=
    sql_cmd="${sql_cmd} CREATE USER 'sgpc'@'localhost' IDENTIFIED BY 'sgpcp';"
    sql_cmd="${sql_cmd} GRANT ALL PRIVILEGES ON *.* TO 'sgpc'@'localhost';"
    sql_cmd="${sql_cmd} FLUSH PRIVILEGES;"
    docker container exec sgpc mysql -u root -e "${sql_cmd}"
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

# Initialize database
sgpc_database
