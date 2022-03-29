<?php

declare(strict_types=1);

namespace Laminas\Db\RowGateway;

interface RowGatewayInterface
{
    public function save();

    public function delete();
}
