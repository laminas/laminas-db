#!/bin/bash
#------------------------------------------------------------------------------
set -e
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
# @see https://hub.docker.com/_/microsoft-mssql-server
# @see https://github.com/Microsoft/mssql-docker
#----
#imageName='mcr.microsoft.com/mssql/server:2019-CU13-ubuntu-20.04'
imageName='local/laminas-db-test-mssql:2019'

SA_PASSWORD='Password123'
appDir=$(pwd)/app
dbDir=$(pwd)/db
if [ ! -d ${dbDir} ]; then
  mkdir ${dbDir}
  chmod 777 ${dbDir}
fi

#------------------------------------------------------------------------------
# RUN image and create database, user, schema.
#-----

#  --user 1000:1000 \
docker run --rm -ti \
  --name 'tmp.ldbt_mssql_2019' \
  -p 1433:1433 \
  -e ACCEPT_EULA=Y \
  -e SA_PASSWORD=${SA_PASSWORD} \
  -v ${appDir}:/app \
  -v ${dbDir}:/var/opt/mssql/data \
  ${imageName}

#------------------------------------------------------------------------------
