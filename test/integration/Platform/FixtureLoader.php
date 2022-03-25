<?php

declare(strict_types=1);

namespace LaminasIntegrationTest\Db\Platform;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface FixtureLoader
{
    public function createDatabase();

    public function dropDatabase();
}
