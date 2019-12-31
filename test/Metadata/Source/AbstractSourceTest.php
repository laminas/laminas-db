<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Metadata\Source;

use Laminas\Db\Metadata\Source\AbstractSource;

class AbstractSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractSource
     */
    protected $abstractSourceMock = null;

    public function setup()
    {
        $this->abstractSourceMock = $this->getMockForAbstractClass('Laminas\Db\Metadata\Source\AbstractSource', [], '', false);
    }

    public function testGetConstraintKeys()
    {
        $refProp = new \ReflectionProperty($this->abstractSourceMock, 'data');
        $refProp->setAccessible(true);

        // internal data
        $data = [
            'constraint_references' => [
                'foo_schema' => [
                    [
                        'constraint_name' => 'bam_constraint',
                        'update_rule' => 'UP',
                        'delete_rule' => 'DOWN',
                        'referenced_table_name' => 'another_table',
                        'referenced_column_name' => 'another_column'
                    ]
                ]
            ],
            'constraint_keys' => [
                'foo_schema' => [
                    [
                        'table_name'=> 'bar_table',
                        'constraint_name' => 'bam_constraint',
                        'column_name' => 'a',
                        'ordinal_position' => 1,
                    ]
                ]
            ]
        ];

        $refProp->setValue($this->abstractSourceMock, $data);
        $constraints = $this->abstractSourceMock->getConstraintKeys('bam_constraint', 'bar_table', 'foo_schema');
        $this->assertCount(1, $constraints);

        /**
         * @var \Laminas\Db\Metadata\Object\ConstraintKeyObject $constraintKeyObj
         */
        $constraintKeyObj = $constraints[0];
        $this->assertInstanceOf('Laminas\Db\Metadata\Object\ConstraintKeyObject', $constraintKeyObj);

        // check value object is mapped correctly
        $this->assertEquals('a', $constraintKeyObj->getColumnName());
        $this->assertEquals(1, $constraintKeyObj->getOrdinalPosition());
        $this->assertEquals('another_table', $constraintKeyObj->getReferencedTableName());
        $this->assertEquals('another_column', $constraintKeyObj->getReferencedColumnName());
        $this->assertEquals('UP', $constraintKeyObj->getForeignKeyUpdateRule());
        $this->assertEquals('DOWN', $constraintKeyObj->getForeignKeyDeleteRule());
    }
}
