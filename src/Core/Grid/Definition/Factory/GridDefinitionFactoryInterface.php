<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;

/**
 * Interface GridDefinitionFactoryInterface defines contract for creating grid.
 */
interface GridDefinitionFactoryInterface
{
    /**
     * Create new grid definition.
     *
     * @return GridDefinitionInterface
     */
    public function getDefinition();
}
