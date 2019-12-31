<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Source\SqliteMetadata;

/**
 * @requires extension pdo_sqlite
 */
class SqliteMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqliteMetadata
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
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('I cannot test without the pdo_sqlite extension');
        }
        $this->adapter = new Adapter(array(
            'driver' => 'Pdo',
            'dsn' => 'sqlite::memory:'
        ));
        $this->metadata = new SqliteMetadata($this->adapter);
    }

    public function testGetSchemas()
    {
        $schemas = $this->metadata->getSchemas();
        $this->assertContains('main', $schemas);
        $this->assertCount(1, $schemas);
    }

    public function testGetTableNames()
    {
        $tables = $this->metadata->getTableNames('main');
        $this->assertCount(0, $tables);
    }

    public function testGetColumnNames()
    {
        $columns = $this->metadata->getColumnNames(null, 'main');
        $this->assertCount(0, $columns);
    }

    public function testGetConstraints()
    {
        $constraints = $this->metadata->getConstraints(null, 'main');
        $this->assertCount(0, $constraints);
        $this->assertContainsOnlyInstancesOf(
            'Laminas\Db\Metadata\Object\ConstraintObject',
            $constraints
        );
    }

    /**
     * @group Laminas-3719
     */
    public function testGetConstraintKeys()
    {
        $keys = $this->metadata->getConstraintKeys(
            null, null, 'main'
        );
        $this->assertCount(0, $keys);
        $this->assertContainsOnlyInstancesOf(
            'Laminas\Db\Metadata\Object\ConstraintKeyObject',
            $keys
        );
    }

    public function testGetTriggers()
    {
        $triggers = $this->metadata->getTriggers('main');
        $this->assertCount(0, $triggers);
        $this->assertContainsOnlyInstancesOf(
            'Laminas\Db\Metadata\Object\TriggerObject',
            $triggers
        );
    }
}
