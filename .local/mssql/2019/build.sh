#!/usr/bin/env bash
#------------------------------------------------------------------------------
cd $(dirname $(readlink -e $0))
#------------------------------------------------------------------------------
imgName='local/laminas-db-test-mssql:2019'

# docker image rm -f ${imgName}

# Create image on local computer
docker build --tag ${imgName} .
if [ ! "$?" == "0" ]; then
  echo "-- Error build image ${imgName}"
  exit 1
fi

# Push local image to dockerhub
#echo '--push image'
#docker login
#docker push ${imgName}
#if [ ! "$?" == "0" ]; then
#   echo "-- Error push image ${imgName}"
#   exit 1
#fi
#------------------------------------------------------------------------------
echo 'docker0 host is'
ifconfig docker0 | grep "inet "
#------------------------------------------------------------------------------
