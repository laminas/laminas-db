<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

use Laminas\Db\ResultSet\HydratingResultSet;
use PHPUnit\Framework\TestCase;

class HydratingResultSetIntegrationTest extends TestCase
{
    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrentWillReturnBufferedRow()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
        ]));
        $hydratingRs->buffer();
        $obj1 = $hydratingRs->current();
        $hydratingRs->rewind();
        $obj2 = $hydratingRs->current();
        self::assertSame($obj1, $obj2);
    }
}
