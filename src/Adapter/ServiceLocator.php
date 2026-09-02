<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * @internal
 *
 * To be removed in 1.7.1.
 */
class ServiceLocator
{
    /**
     * Set a service container Instance.
     *
     * @var Container|null
     */
    private static $service_container;

    public static function setServiceContainerInstance(?Container $container)
    {
        self::$service_container = $container;
    }

    public static function getContainer(): ?Container
    {
        return self::$service_container;
    }

    /**
     * Get a service depending on its given $serviceName.
     *
     * @param string $serviceName
     *
     * @return mixed|object
     *
     * @throws CoreException
     */
    public static function get($serviceName)
    {
        if (null === self::$service_container) {
            throw new CoreException('Service container is not set.');
        }

        return self::$service_container->make($serviceName);
    }
}
