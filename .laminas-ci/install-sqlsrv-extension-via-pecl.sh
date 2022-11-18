#!/bin/bash

PHP_VERSION="$1"

if ! [[ "${PHP_VERSION}" =~ 8\.2 ]]; then
  echo "sqlsrv is only installed from pecl for PHP 8.2, ${PHP_VERSION} detected."
  exit 0;
fi

set +e

apt-get update && apt-get install -y g++ unixodbc-dev
pecl install sqlsrv
echo "extension=sqlsrv.so" > /etc/php/${PHP_VERSION}/mods-available/sqlsrv.ini
phpenmod -v ${PHP} -s cli sqlsrv
