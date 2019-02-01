<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Feature;

interface DriverFeatureInterface
{
    /**
     * Setup the default features for Pdo
     *
     * @return DriverFeatureInterface
     */
    public function setupDefaultFeatures(): DriverFeatureInterface;

    /**
     * Add feature
     *
     * @param mixed $feature
     */
    public function addFeature(string $name, $feature): DriverFeatureInterface;

    /**
     * Get feature
     *
     * @return mixed|false
     */
    public function getFeature(string $name);
}
