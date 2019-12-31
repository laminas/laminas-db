<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform;

use Laminas\Db\Adapter\AdapterInterface;

class Platform extends AbstractPlatform
{
    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'mysql':
                $platform = new Mysql\Mysql();
                $this->decorators = $platform->decorators;
                break;
            case 'sqlserver':
                $platform = new SqlServer\SqlServer();
                $this->decorators = $platform->decorators;
                break;
            case 'oracle':
                $platform = new Oracle\Oracle();
                $this->decorators = $platform->decorators;
                break;
            case 'ibm db2':
            case 'ibm_db2':
            case 'ibmdb2':
                $platform = new IbmDb2\IbmDb2();
                $this->decorators = $platform->decorators;
            default:
        }
    }
}
