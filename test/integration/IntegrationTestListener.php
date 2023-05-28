<?php

namespace LaminasIntegrationTest\Db;

use LaminasIntegrationTest\Db\Platform\FixtureLoader;
use LaminasIntegrationTest\Db\Platform\MysqlFixtureLoader;
use LaminasIntegrationTest\Db\Platform\PgsqlFixtureLoader;
use LaminasIntegrationTest\Db\Platform\SqlServerFixtureLoader;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\TestHook;

use function getenv;
use function printf;

class IntegrationTestListener implements TestHook, TestListener
{
    use TestListenerDefaultImplementation;

    /** @var FixtureLoader[] */
    private $fixtureLoaders = [];

    public function startTestSuite(TestSuite $suite): void
    {
        if ($suite->getName() !== 'integration test') {
            return;
        }

        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->fixtureLoaders[] = new MysqlFixtureLoader();
        }

        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL')) {
            $this->fixtureLoaders[] = new PgsqlFixtureLoader();
        }

        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
            $this->fixtureLoaders[] = new SqlServerFixtureLoader();
        }

        if (empty($this->fixtureLoaders)) {
            return;
        }

        printf("\nIntegration test started.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->createDatabase();
        }
    }

    public function endTestSuite(TestSuite $suite): void
    {
        if (
            $suite->getName() !== 'integration test'
            || empty($this->fixtureLoaders)
        ) {
            return;
        }

        printf("\nIntegration test ended.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->dropDatabase();
        }
    }
}
