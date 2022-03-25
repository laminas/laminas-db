<?php

declare(strict_types=1);

namespace LaminasIntegrationTest\Db;

use LaminasIntegrationTest\Db\Platform\MysqlFixtureLoader;
use LaminasIntegrationTest\Db\Platform\PgsqlFixtureLoader;
use LaminasIntegrationTest\Db\Platform\SqlServerFixtureLoader;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\TestHook;

use function array_search;
use function getenv;
use function printf;
use function trim;

class IntegrationTestPHPUnitExtension implements TestHook, BeforeFirstTestHook, AfterLastTestHook
{
    private array $fixtureLoaders = [];

    public function executeBeforeFirstTest(): void
    {
        if ($this->getPhpUnitParameter("testsuite") !== 'integration test') {
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

    public function executeAfterLastTest(): void
    {
        if (
            $this->getPhpUnitParameter("testsuite") === 'integration test' ||
            $this->fixtureLoaders === []
        ) {
            return;
        }

        printf("\nIntegration test ended.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->dropDatabase();
        }
    }

    /**
     * Resolves the parameters passed to PHPUnit.
     *
     * eg. "phpunit --testsuite Unit --filter FirstTest"
     *
     * $this->getPhpUnitParameter("filter"); // FirstTest
     * $this->getPhpUnitParameter("testsuite"); // Unit
     */
    private function getPhpUnitParameter(string $paramName): ?string
    {
        if ($offset = array_search("--$paramName", $GLOBALS['argv']) === false) {
            return null;
        }

        return trim($GLOBALS['argv'][$offset + 1]);
    }
}
