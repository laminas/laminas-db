<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Oracle;

use Laminas\Db\Sql\Platform\Oracle\Oracle;

class OracleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox unit test / object test: Test Mysql object has Select proxy
     * @covers Laminas\Db\Sql\Platform\Oracle\Oracle::__construct
     */
    public function testConstruct()
    {
        $oracle = new Oracle;
        $decorators = $oracle->getDecorators();

        list($type, $decorator) = each($decorators);
        $this->assertEquals('Laminas\Db\Sql\Select', $type);
        $this->assertInstanceOf('Laminas\Db\Sql\Platform\Oracle\SelectDecorator', $decorator);
    }
}
