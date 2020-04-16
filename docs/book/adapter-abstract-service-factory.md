# Adapter Abstract Service Factory

When you install a Laminas MVC Skeleton project, all the Services are geared together and ready to use. The Laminas Db component is not included in the core of the MVC Skeleton installation but it can be either added during the project creation or after with `composer`.

Once Laminas Db is added to your project, a `Laminas\Db\Adapter\AdapterAbstractServiceFactory` is automatically added to the Application Service Manager. You won't have to add the abstract factory to the Service Manager.

The `AdapterAbstractServiceFactory` is using a top-level configuration key "db" with a subkey "adapters".

You can use it to create your adapters for your MVC application.

## Using one Adapter

```php
// config/autoload/dbadapters.local.php

return [
    'db' => [
        'adapters' => [
            Db\MySqlLiteAdapter::class => [
                'driver'   => 'Pdo_Sqlite',
                'database' => 'data/db/users.db',
            ],
        ],
    ],
];
```

Back in your application, you don't _have_ to create an adapter for your SqlLite database. It is already created by the `AdapterAbstractServiceFactory`.

You just need to call your adapter by doing :

```php
use Db\MySqlLiteAdapter ;

$adapter = $container->get(Db\MySqlLiteAdapter::class) ;
```

The `$container` implements a Interop\Container\ContainerInterface, in the MVC Skeleton, it is the Service Manager. You'll find it mostly in all your factories.

## Using multiple Adapters

Maybe you want to work with multiple databases or the same databases but multiple schemas.

```php
// config/autoload/dbadapters.local.php

return [
    'db' => [
        'adapters' => [
            'Db\MyFirstAdapter' => [
                'driver'   => 'Pdo',
                'database' => 'UserDatabase',
                'dsn' => 'mysql:dbname=UserDatabase;host=localhost;charset=utf8',
                'username' => 'myAdminUser',
                'password' => 'mySecretPassword',
                'driver_options' => [
                     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ],
            ],
            'Db\MySecondAdapter' => [
                'driver'   => 'Pdo',
                'database' => 'CustomerDatabase',
                'dsn' => 'mysql:dbname=CustomerDatabase;host=localhost;charset=utf8',
                'username' => 'myCRMUser',
                'password' => 'anotherSecretPassword',
                'driver_options' => [
                     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ],
            ],
        ],
    ],
];
```

Back in your application, you will call either adapters by doing :

```php
$userDatabaseAdapter = $container->get('Db\MyFirstAdapter') ;
$customerDatabaseAdapter = $container->get('Db\MySecondAdapter') ;
```

You can either use strings or fully qualified names for your adapters. 

> NOTE :
> There is a current conflict with Laminas Developer Tools so if you wish to work with multiple adapters, you can work around the issue by adding a driver key to the config.
> ```php
> // config/autoload/dbadapters.local.php
>  
> return [
>     'db' => [
>         'driver' => 'Pdo',
>         'adapters' => [
>             'Db\MyFirstAdapter' => [
>                 'driver'   => 'Pdo_Sqlite',
>                 'database' => 'data/db/users.db',
>             ],
>             'Db\MySecondAdapter' => [
>                 'driver'   => 'Pdo_Sqlite',
>                 'database' => 'data/db/customers.db',
>             ],
>         ],
>     ],
> ];
> ```
