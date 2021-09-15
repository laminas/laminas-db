# -*- mode: ruby -*-
# vi: set ft=ruby :

$install_software = <<SCRIPT
export DEBIAN_FRONTEND=noninteractive
apt-get -yq update

# INSTALL PostgreSQL
apt-get -yq install postgresql

# Allow external connections to PostgreSQL as postgres
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/" /etc/postgresql/12/main/postgresql.conf
sed -i "s/peer/trust/" /etc/postgresql/12/main/pg_hba.conf
echo 'host all all 0.0.0.0/0 trust' >> /etc/postgresql/12/main/pg_hba.conf
service postgresql restart

# INSTALL MySQL
debconf-set-selections <<< "mysql-server mysql-server/root_password password Password123"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password Password123"
apt-get -yq install mysql-server

# Allow external connections to MySQL as root (with password Password123)
sed -i 's/127\.0\.0\.1/0\.0\.0\.0/g' /etc/mysql/mysql.conf.d/mysqld.cnf
mysql -u root -pPassword123 -e 'USE mysql; UPDATE `user` SET `Host`="%" WHERE `User`="root" AND `Host`="localhost"; DELETE FROM `user` WHERE `Host` != "%" AND `User`="root"; FLUSH PRIVILEGES;'
mysql -h localhost -u root -pPassword123 -e "ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'Password123';"
service mysql restart

# INSTALL SQL Server
# More info here: https://docs.microsoft.com/en-us/sql/linux/sample-unattended-install-ubuntu?view=sql-server-ver15
MSSQL_SA_PASSWORD='Password123'
MSSQL_PID=developer
SQL_INSTALL_AGENT='y'
curl -s https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
repoargs="$(curl https://packages.microsoft.com/config/ubuntu/20.04/mssql-server-2019.list)"
add-apt-repository "${repoargs}"
repoargs="$(curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list)"
add-apt-repository "${repoargs}"
apt-get -yq update
apt-get -yq install mssql-server

MSSQL_SA_PASSWORD=$MSSQL_SA_PASSWORD \
     MSSQL_PID=$MSSQL_PID \
     /opt/mssql/bin/mssql-conf -n setup accept-eula

echo "Installing mssql-tools and unixODBC developer..."
ACCEPT_EULA=Y apt-get install -y mssql-tools unixodbc-dev

echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> /home/vagrant/.bash_profile
echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> /home/vagrant/.bashrc
source /home/vagrant/.bashrc
SCRIPT


$setup_vagrant_user_environment = <<SCRIPT
if ! grep "cd /vagrant" /home/vagrant/.profile > /dev/null; then
  echo "cd /vagrant" >> /home/vagrant/.profile
fi
SCRIPT

Vagrant.configure(2) do |config|
  config.vm.box = 'bento/ubuntu-20.04'
  config.vm.provider "virtualbox" do |v|
    v.memory = 4096
    v.cpus = 2
  end

  config.vm.network "private_network", ip: "192.168.20.20"

  config.vm.provision 'shell', inline: $install_software
  config.vm.provision 'shell', privileged: false, inline: '/vagrant/.ci/mysql_fixtures.sh'
  config.vm.provision 'shell', privileged: false, inline: '/vagrant/.ci/pgsql_fixtures.sh'
  config.vm.provision 'shell', privileged: false, inline: '/vagrant/.ci/sqlsrv_fixtures.sh'
  config.vm.provision 'shell', inline: $setup_vagrant_user_environment
end
