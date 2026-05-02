#!/bin/bash

WORKING_DIRECTORY=$2
JOB=$3
PHP_VERSION=$(echo "${JOB}" | jq -r '.php')

if [[ "${PHP_VERSION}" == "8.5" ]]; then
  ACCEPT_EULA=Y apt update -qq
  ACCEPT_EULA=Y apt install -yqq msodbcsql18 php-pear "php${PHP_VERSION}-dev" "php${PHP_VERSION}-xml" unixodbc-dev
  pecl channel-update pecl.php.net
  pecl install sqlsrv pdo_sqlsrv
  printf "; priority=20\nextension=sqlsrv.so\n" > "/etc/php/${PHP_VERSION}/mods-available/sqlsrv.ini"
  printf "; priority=30\nextension=pdo_sqlsrv.so\n" > "/etc/php/${PHP_VERSION}/mods-available/pdo_sqlsrv.ini"
  phpenmod -v "${PHP_VERSION}" sqlsrv pdo_sqlsrv
else
  apt update -qq
  apt install -yqq "php${PHP_VERSION}-sqlsrv"
fi

if [ ! -z "$GITHUB_BASE_REF" ] && [[ "$GITHUB_BASE_REF" =~ ^[0-9]+\.[0-9] ]]; then
  readarray -td. TARGET_BRANCH_VERSION_PARTS <<<"${GITHUB_BASE_REF}.";
  unset 'TARGET_BRANCH_VERSION_PARTS[-1]';
  declare -a TARGET_BRANCH_VERSION_PARTS
  MAJOR_OF_TARGET_BRANCH=${TARGET_BRANCH_VERSION_PARTS[0]}
  MINOR_OF_TARGET_BRANCH=${TARGET_BRANCH_VERSION_PARTS[1]}

  export COMPOSER_ROOT_VERISON="${MAJOR_OF_TARGET_BRANCH}.${MINOR_OF_TARGET_BRANCH}.99"
  echo "Exported COMPOSER_ROOT_VERISON as ${COMPOSER_ROOT_VERISON}"
fi
