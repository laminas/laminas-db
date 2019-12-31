<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform;

use Laminas\Db\Adapter\Adapter;

class Platform extends AbstractPlatform
{

    /**
     * @var Adapter
     */
    protected $adapter = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'sqlserver':
                $platform = new SqlServer\SqlServer();
                $this->decorators = $platform->decorators;
                break;
            default:
        }
    }

}
