#!/usr/bin/env bash
set -x

# Install MSSQL client development libraries
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
sudo curl https://packages.microsoft.com/config/ubuntu/18.04/prod.list -o /etc/apt/sources.list.d/mssql-release.list
sudo apt-get update
sudo ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools unixodbc-dev
echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile
echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc

export PATH="$PATH:/opt/mssql-tools/bin"

# Install SqlServer PHP extensions
pecl install sqlsrv
pecl install pdo_sqlsrv
