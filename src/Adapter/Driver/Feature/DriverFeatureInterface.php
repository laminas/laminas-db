<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver\Feature;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
interface DriverFeatureInterface
{
    public function setupDefaultFeatures();
    public function addFeature($name, $feature);
    public function getFeature($name);
}
