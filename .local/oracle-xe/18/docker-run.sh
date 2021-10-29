#!/bin/bash
#------------------------------------------------------------------------------
set -e
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
# @see https://github.com/gvenzl/oci-oracle-xe
# @see https://github.com/gvenzl/oci-oracle-xe/blob/main/container-entrypoint.sh
# @see https://github.com/gvenzl/oci-oracle-xe/blob/main/Dockerfile.1840
# @see https://github.com/gvenzl/oci-oracle-xe/blob/main/install.1840.sh
#
# FROM oraclelinux:8-slim
# WORKDIR ${ORACLE_BASE} is '/opt/oracle'
# sqlplus is /opt/oracle/product/18c/dbhomeXE/bin/sqlplus
#----

# Avalible:
# 18.4.0, 18, latest
# 18.4.0-slim, 18-slim, slim
# 18.4.0-full, 18-full, full
imageName='gvenzl/oracle-xe:18.4.0'

# ORACLE_PASSWORD
# This variable is mandatory for the first container startup and
# specifies the password for the Oracle Database SYS and SYSTEM users.
oraclePassword='sysPass'

# ORACLE_DATABASE
# (for 18c only)
# This is an optional variable. Set this variable to a non-empty string to
# create a new pluggable database with the name specified in this variable.
oracleDatabase='laminasdb_test'

# APP_USER
# This is an optional variable. Set this variable to a non-empty string to
# create a new database schema user with the name specified in this variable.
appUser='ldbtUser'

# APP_USER_PASSWORD
# This is an optional variable. Set this variable to a non-empty string to
# define a password for the database schema user specified by APP_USER.
# This variable requires APP_USER to be specified as well.
appUserPassword='Password123'

dbDir=$(pwd)/db
if [ ! -d $dbDir ]; then
  mkdir ${dbDir}
  chmod 777 ${dbDir}
fi

#------------------------------------------------------------------------------
# RUN image and create database, user, schema.
#-----

#  --user 1000:1000 \
docker run --rm -ti \
  --name='tmp.ldbt_oracle_xe_18' \
  -p 1521:1521 \
  -e ORACLE_PASSWORD=${oraclePassword} \
  -e ORACLE_DATABASE=${oracleDatabase} \
  -e APP_USER=${appUser} \
  -e APP_USER_PASSWORD=${appUserPassword} \
  -v ${dbDir}:/opt/oracle/oradata \
  $imageName

#------------------------------------------------------------------------------
