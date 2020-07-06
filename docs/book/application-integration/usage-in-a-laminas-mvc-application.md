# Usage in a laminas-mvc Application

The minimal installation for a laminas-mvc based application doesn't include any database features.  

**When installing the Laminas MVC Skeleton Application**

While `Composer` is [installing the MVC Application](https://docs.laminas.dev/laminas-mvc/quick-start/#install-the-laminas-mvc-skeleton-application), you can add the `laminas-db` package while prompted.

**Adding to an existing Laminas MVC Skeleton Application**

If the MVC Application is already installed, then use `Composer` to [add the `laminas-db`](https://docs.laminas.dev/laminas-db/) package.

**The Abstract Factory**

Now that the `laminas-db` package is installed, an Abstract Factory `Laminas\Db\Adapter\AdapterAbstractServiceFactory` is available to be used with the Service Manager.  

## Configuring the Adapter 

The abstract factory expects the configuration key **'db'** in order to create a `Laminas\Db\Adapter\Adapter`.

### Working with a Sqlite database

Sqlite is a lightweight option to have the application working with a database. 

Here is an example of the configuration array for a sqlite database. The sqlite file path : `data/sample.sqlite`.

```php
return [
    'db' => [
        'driver' => 'Pdo',
        'adapters' => [
            sqliteAdapter::class => [
                'driver' => 'Pdo',
                'dsn' => 'sqlite:data/sample.sqlite',
            ],
        ],
    ],
];
```

*The data/ filepath for the sqlite file is the default data/ directory from the Laminas MVC application*

### Working with a MySQL database

Unlike a sqlite database, the mysql database requires a mysql server.

Here is an example of the configuration array for a MySQL database.

```php
return [
    'db' => [
        'driver' => 'Pdo',
        'adapters' => [
            mysqlAdapter::class => [
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=your_database_name;host=your_mysql_host;charset=utf8',
                'username' => 'your_mysql_username',
                'password' => 'your_mysql_password',
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ],
            ],
        ],
    ],
];
```

## Working with the Adapter

Now a `Laminas\Db\Adapter\Adapter` is available.

For the Sqlite Database configured earlier :

```php
  use sqliteAdapter ;

  $adapter = $container->get(sqliteAdapter::class) ;
```

For the MySQL Database configured earlier :

```php
  use mysqlAdapter ;

  $adapter = $container->get(mysqlAdapter::class) ;
```

More on the [Adapter](https://docs.laminas.dev/laminas-db/adapter) in the documentation.


## Running with Docker

When working with a MySQL database and when running the application with Docker, some files need to be added or adjusted.

### Adding Mysql extension to the php container

Change the /Dockerfile to add the PDO mysql extension to PHP.

```
FROM php:7.3-apache

RUN apt-get update \
 && apt-get install -y git zlib1g-dev libzip-dev \
 && docker-php-ext-install zip pdo_mysql \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
```

### Adding the mysql container

Change the /docker-compose.yml file to add a new container for mysql.

**Adding the container**

```yaml
  mysql:
    image: mysql
    ports: 
     - 3306:3306
    command:
      --default-authentication-plugin=mysql_native_password
    volumes:
     - ./.data/db:/var/lib/mysql
     - ./.docker/mysql/:/docker-entrypoint-initdb.d/
    environment:
     - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
```

Though it is not the topic to explain how to write a `docker-compose.yml` file, few details need to be highlighted :
* the name of the container is `mysql`
* mysql database files will be stored in `/.data/db` folder
* .sql schemas will need to be in `/.docker/mysql` folder so Docker will be able to build up the database(s)
* the *mysql* docker image is using MYSQL_ROOT_PASSWORD property to set the mysql root password.

**Link the containers**

Now link the mysql container and the laminas container so the Apache/php container with application knows where to find the mysql server.

```yaml
  laminas:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
     - 8080:80
    volumes:
     - .:/var/www
    links:
     - mysql:mysql
```

*add the 'links' directive*

**Adding phpMyAdmin**

Optionnally, you can also add a container for phpMyAdmin.

```yaml
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
     - 8081:80
    environment:
     - PMA_HOST=${PMA_HOST}
```

The image uses PMA_HOST property to set the host of the mysql server. The expected value is the name of the mysql container.

Putting everything together :

```yaml
version: "2.1"
services:
  laminas:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
     - 8080:80
    volumes:
     - .:/var/www
    links:
     - mysql:mysql
  mysql:
    image: mysql
    ports: 
     - 3306:3306
    command:
      --default-authentication-plugin=mysql_native_password
    volumes:
     - ./.data/db:/var/lib/mysql
     - ./.docker/mysql/:/docker-entrypoint-initdb.d/
    environment:
     - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
     - 8081:80
    environment:
     - PMA_HOST=${PMA_HOST}
```

### Defining credentials

The `docker-compose.yml` file uses ENV variables to define the credentials.  

Docker will read the ENV variables in a `/.env` file.

```
MYSQL_ROOT_PASSWORD=rootpassword
PMA_HOST=mysql
```

### Initiating the database schemas

At build, if the `/.data/db` folder is missing, Docker will create the mysql database with the .sql files left in the `.docker/mysql` folder. (the CREATE DATABASE, USE (database), CREATE TABLE, INSERT INTO directives).  
If there's multiple .sql files, it is a good idea to safely order the list of sql files because Docker will read the files in ascending order.

