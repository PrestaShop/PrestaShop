<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Data\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\SupplierGridDefinitionFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SupplierGridDataFactoryConfigurationTest extends TestCase
{
    /**
     * The grid id given to the data factory names the actionSupplierGridQueryBuilderModifier hook
     * and selects the extra properties of the grid, so it must be the grid definition's id.
     */
    public function testSupplierDataFactoryUsesSupplierGridId(): void
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../../../../../src/PrestaShopBundle/Resources/config/services/core/grid')
        );
        $loader->load('grid_data_factory.yml');

        $this->assertSame(
            SupplierGridDefinitionFactory::GRID_ID,
            $container->getDefinition('prestashop.core.grid.data_provider.supplier')->getArgument(3)
        );
    }
}
