# Usage in a laminas-mvc Application

The minimal installation for a laminas-mvc based application doesn't include any database features.

## When installing the Laminas MVC Skeleton Application

While `Composer` is [installing the MVC Application](https://docs.laminas.dev/laminas-mvc/quick-start/#install-the-laminas-mvc-skeleton-application), you can add the `laminas-db` package while prompted.

## Adding to an existing Laminas MVC Skeleton Application

If the MVC application is already created, then use Composer to [add the laminas-db](../index.md) package.

## The Abstract Factory

Now that the laminas-db package is installed, the abstract factory `Laminas\Db\Adapter\AdapterAbstractServiceFactory` is available to be used with the service configuration.

### Configuring the adapter

The abstract factory expects the configuration key `db` in order to create a `Laminas\Db\Adapter\Adapter` instance.

### Working with a Sqlite database

Sqlite is a lightweight option to have the application working with a database.

Here is an example of the configuration array for a sqlite database.
Assuming the sqlite file path is `data/sample.sqlite`, the following configuration will produce the adapter:

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

The `data/` filepath for the sqlite file is the default `data/` directory from the Laminas MVC application.

### Working with a MySQL database

Unlike a sqlite database, the MySQL database adapter requires a MySQL server.

Here is an example of a configuration array for a MySQL database.

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

## Working with the adapter

Once you have configured an adapter, as in the above examples, you now have a `Laminas\Db\Adapter\Adapter` available to your application.

A factory for a class that consumes an adapter can pull the adapter by the name used in configuration.
As an example, for the sqlite database configured earlier, we could write the following:

```php
use sqliteAdapter ;

$adapter = $container->get(sqliteAdapter::class) ;
```

For the MySQL Database configured earlier:

```php
use mysqlAdapter ;

$adapter = $container->get(mysqlAdapter::class) ;
```

You can read more about the [adapter in the adapter chapter of the documentation](../adapter.md).

## Running with Docker

When working with a MySQL database and when running the application with Docker, some files need to be added or adjusted.

### Adding the MySQL extension to the PHP container

Change the `Dockerfile` to add the PDO MySQL extension to PHP.

```Dockerfile
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

Change the `docker-compose.yml` file to add a new container for mysql.

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

Though it is not the topic to explain how to write a `docker-compose.yml` file, a few details need to be highlighted :

- The name of the container is `mysql`.
- MySQL database files will be stored in the directory `/.data/db/`.
- SQL schemas will need to be added to the `/.docker/mysql/` directory so that Docker will be able to build and populate the database(s).
- The mysql docker image is using the `$MYSQL_ROOT_PASSWORD` environment variable to set the mysql root password.

### Link the containers

Now link the mysql container and the laminas container so that the application knows where to find the mysql server.

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

### Adding phpMyAdmin

Optionnally, you can also add a container for phpMyAdmin.

```yaml
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
     - 8081:80
    environment:
     - PMA_HOST=${PMA_HOST}
```

The image uses the `$PMA_HOST` environment variable to set the host of the mysql server.
The expected value is the name of the mysql container.

Putting everything together:

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

Docker will read the ENV variables from a `.env` file.

```env
MYSQL_ROOT_PASSWORD=rootpassword
PMA_HOST=mysql
```

### Initiating the database schemas

At build, if the `/.data/db` directory is missing, Docker will create the mysql database with any `.sql` files found in the `.docker/mysql/` directory.
(These are the files with the `CREATE DATABASE`, `USE (database)`, and `CREATE TABLE, INSERT INTO` directives defined earlier in this document).
If multiple `.sql` files are present, it is a good idea to safely order the list because Docker will read the files in ascending order.
