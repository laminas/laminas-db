<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Source\OracleMetadata;
use LaminasTest\Db\Adapter\Driver\Oci8\AbstractIntegrationTest;

/**
 * @requires extension oci8
 */
class OracleMetadataTest extends AbstractIntegrationTest
{
    /**
     * @var OracleMetadata
     */
    protected $metadata;

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (! extension_loaded('oci8')) {
            $this->markTestSkipped('I cannot test without the oci8 extension');
        }
        parent::setUp();
        $this->variables['driver'] = 'OCI8';
        $this->adapter = new Adapter($this->variables);
        $this->metadata = new OracleMetadata($this->adapter);
    }

    /**
     * @dataProvider constraintDataProvider
     *
     * @param array $constraintData
     */
    public function testGetConstraints(array $constraintData)
    {
        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\Oci8\Statement')
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->willReturn($constraintData);

        /** @var \Laminas\Db\Adapter\Adapter|\PHPUnit_Framework_MockObject_MockObject $adapter */
        $adapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setConstructorArgs([$this->variables])
            ->getMock();
        $adapter->expects($this->once())
            ->method('query')
            ->willReturn($statement);

        $this->metadata = new OracleMetadata($adapter);

        $constraints = $this->metadata->getConstraints(null, 'main');

        self::assertCount(count($constraintData), $constraints);

        self::assertContainsOnlyInstancesOf(
            'Laminas\Db\Metadata\Object\ConstraintObject',
            $constraints
        );
    }

    /**
     * @return array
     */
    public function constraintDataProvider()
    {
        return [
            [
                [
                    // no constraints
                ]
            ],
            [
                [
                    [
                        'OWNER' => 'SYS',
                        'CONSTRAINT_NAME' => 'SYS_C000001',
                        'CONSTRAINT_TYPE' => 'C',
                        'CHECK_CLAUSE' => '"COLUMN_1" IS NOT NULL',
                        'TABLE_NAME' => 'TABLE',
                        'DELETE_RULE' => null,
                        'COLUMN_NAME' => 'COLUMN_1',
                        'REF_TABLE' => null,
                        'REF_COLUMN' => null,
                        'REF_OWNER' => null,
                    ],
                    [
                        'OWNER' => 'SYS',
                        'CONSTRAINT_NAME' => 'SYS_C000002',
                        'CONSTRAINT_TYPE' => 'C',
                        'CHECK_CLAUSE' => '"COLUMN_2" IS NOT NULL',
                        'TABLE_NAME' => 'TABLE',
                        'DELETE_RULE' => null,
                        'COLUMN_NAME' => 'COLUMN_2',
                        'REF_TABLE' => null,
                        'REF_COLUMN' => null,
                        'REF_OWNER' => null,
                    ],
                ],
            ],
        ];
    }
}
