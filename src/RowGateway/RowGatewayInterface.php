<?php

namespace Laminas\Db\RowGateway;

interface RowGatewayInterface
{
    public function save();

    public function delete();
}
