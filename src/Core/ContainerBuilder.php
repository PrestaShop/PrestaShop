<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core;

use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

class ContainerBuilder
{
    /**
     * Construct PrestaShop Core Service container.
     *
     * @return Container
     *
     * @throws Foundation\IoC\Exception
     */
    public function build()
    {
        $container = new Container();

        $container->bind('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface', '\\PrestaShop\\PrestaShop\\Adapter\\Configuration', true);
        $container->bind('PrestaShop\\PrestaShop\\Core\\ConfigurationInterface', '\\PrestaShop\\PrestaShop\\Adapter\\Configuration', true);
        // Bound via a factory rather than the class name: Container::makeInstanceFromClassName() would
        // otherwise try to build Database's optional Connection $connection constructor argument by
        // reflection (it only checks for a default value *after* checking for a class type-hint), which
        // it cannot do since Connection needs real driver configuration. This container has no notion of
        // Doctrine/Symfony services anyway, so Database is built here without connection sharing.
        $databaseFactory = static fn () => new \PrestaShop\PrestaShop\Adapter\Database();
        $container->bind('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface', $databaseFactory, true);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface', $databaseFactory, true);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Image\\ImageFormatConfiguration', 'PrestaShop\\PrestaShop\\Core\\Image\\ImageFormatConfiguration', true);

        return $container;
    }
}
