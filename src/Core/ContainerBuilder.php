<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core;

use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Stock\StockManager;

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
        // StockManager's optional MutationTracker cannot be built by this container (the
        // reflection-based resolution chokes on its EntityManager dependency), so the
        // instantiation is explicit. The tracker is not needed in the legacy contexts served
        // by this container: it only records mutations for the Admin API kernel requests,
        // which resolve StockManager through the Symfony container.
        $stockManagerFactory = static function (): StockManager {
            return new StockManager();
        };
        $container->bind('\\PrestaShop\\PrestaShop\\Core\\Stock\\StockManager', $stockManagerFactory);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Stock\\StockManager', $stockManagerFactory);

        return $container;
    }
}
