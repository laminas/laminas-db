#!/bin/bash
#------------------------------------------------------------------------------
set -e
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
# @see https://hub.docker.com/_/postgres?tab=description
# @see https://github.com/docker-library/docs/blob/master/postgres/README.md
# @see https://github.com/docker-library/postgres
# @see https://github.com/docker-library/postgres/tree/master/14/alpine
# @see https://github.com/docker-library/postgres/blob/master/14/alpine/Dockerfile
# @see https://github.com/docker-library/postgres/blob/master/14/alpine/docker-entrypoint.sh
#----
imageName='postgres:14.0-alpine'

POSTGRES_DB='laminasdb_test'
POSTGRES_USER='postgres'
#POSTGRES_PASSWORD='Password123'

dbDir=$(pwd)/db
if [ ! -d ${dbDir} ]; then
  mkdir ${dbDir}
  chmod 777 ${dbDir}
fi

#------------------------------------------------------------------------------
# RUN image and create database, user, schema.
#-----

#  --user 1000:1000 \
#  -e POSTGRES_PASSWORD=${POSTGRES_PASSWORD} \
docker run --rm -ti \
  --name 'tmp.ldbt_postgres_14' \
  -p 5432:5432 \
  -e POSTGRES_USER=${POSTGRES_USER} \
  -e POSTGRES_HOST_AUTH_METHOD=trust \
  -e POSTGRES_DB=${POSTGRES_DB} \
  -v ${dbDir}:/var/lib/postgresql/data \
  ${imageName}


#------------------------------------------------------------------------------
