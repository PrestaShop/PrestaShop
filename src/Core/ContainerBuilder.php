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
        $container->bind('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface', '\\PrestaShop\\PrestaShop\\Adapter\\Database', true);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface', '\\PrestaShop\\PrestaShop\\Adapter\\Database', true);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Image\\ImageFormatConfiguration', 'PrestaShop\\PrestaShop\\Core\\Image\\ImageFormatConfiguration', true);

        return $container;
    }
}
