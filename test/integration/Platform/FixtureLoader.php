<?php

namespace LaminasIntegrationTest\Db\Platform;

interface FixtureLoader
{
    public function createDatabase();
    public function dropDatabase();
}
