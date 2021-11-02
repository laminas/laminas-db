#!/bin/bash
#------------------------------------------------------------------------------
set -e
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
# @see https://hub.docker.com/_/mysql?tab=description
# @see https://github.com/docker-library/mysql
# @see https://github.com/docker-library/mysql/blob/master/8.0/docker-entrypoint.sh
# @see https://github.com/docker-library/mysql/blob/master/8.0/Dockerfile.debian
#----

imageName=mysql:8.0.27

MYSQL_ROOT_PASSWORD='Password123'
MYSQL_DATABASE='laminasdb_test'
MYSQL_USER='root'
MYSQL_PASSWORD='Password123'

dbDir=$(pwd)/db
if [ ! -d ${dbDir} ]; then
  mkdir ${dbDir}
  chmod 777 ${dbDir}
fi

#------------------------------------------------------------------------------
# RUN image and create database, user, schema.
#-----

#  -e MYSQL_USER=${MYSQL_USER} \

docker run --rm -ti \
  --name tmp.ldbt_mysql \
  --user 1000:1000 \
  -p 3306:3306 \
  -p 33060:33060 \
  -e MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD} \
  -e MYSQL_DATABASE=${MYSQL_DATABASE} \
  -e MYSQL_PASSWORD=${MYSQL_PASSWORD} \
  -v ${dbDir}:/var/lib/mysql \
  ${imageName}

#------------------------------------------------------------------------------
