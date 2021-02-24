<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Metadata;

use Laminas\Db\Metadata\MetadataInterface;
use Laminas\Db\Metadata\Object\ConstraintObject;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-mysql
 */
abstract class AnsiTestCase extends TestCase
{
    /**
     * @var MetadataInterface
     */
    public $source = null;
    public $defaultSchema = null;

    public function testGetTableNames()
    {
        $expected = ['test', 'test_audit_trail', 'test_charset'];
        $actual = $this->source->getTableNames($this->defaultSchema);
        sort($actual);

        self::assertEquals($expected, $actual);
    }

    public function testGetTables()
    {
        $tables = $this->source->getTables($this->defaultSchema);
        $foundTestTable = false;
        foreach ($tables as $table) {
            if ($table->getName() == 'test') {
                $foundTestTable = true;
                break;
            }
        }

        $this->assertEquals(true, $foundTestTable);
    }

    public function testGetViews()
    {
        $views = $this->source->getViews($this->defaultSchema);
        $foundViewTable = false;
        foreach ($views as $view) {
            if (strpos($view->getViewDefinition(), 'v_value') >= -1) {
                $foundViewTable = true;
                break;
            }
        }

        $this->assertEquals(true, $foundViewTable);
    }

    public function testGetColumnNames()
    {
        $expected = ['id', 'name', 'value'];
        $actual = $this->source->getColumnNames('test', $this->defaultSchema);

        self::assertEquals($expected, $actual);
    }

    public function testGetViewNames()
    {
        $expected = ['test_view'];
        $actual = $this->source->getViewNames($this->defaultSchema);

        self::assertEquals($expected, $actual);
    }

    public function testGetTriggerNames()
    {
        $expected = ['after_test_update'];
        $actual = $this->source->getTriggerNames();

        self::assertEquals($expected, $actual);
    }

    public function testGetConstraints()
    {
        $actual = $this->source->getConstraints('test', $this->defaultSchema);
        self::assertEquals(1, count($actual));

        $constraint = $actual[0];
        self::assertInstanceOf(ConstraintObject::class, $constraint);
        self::assertEquals(true, $constraint->isPrimaryKey());
        self::assertEquals('test', $constraint->getTableName());
    }
}
