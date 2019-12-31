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

    public function testGetConstraints()
    {
        $constraints = $this->metadata->getConstraints(null, 'main');
        self::assertCount(0, $constraints);
        self::assertContainsOnlyInstancesOf(
            'Laminas\Db\Metadata\Object\ConstraintObject',
            $constraints
        );
    }
}
