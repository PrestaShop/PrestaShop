<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring;

/**
 * Builds Grid definition for product without combination and without quantities grid
 */
final class NoQtyProductWithoutCombinationGridDefinitionFactory extends AbstractProductGridDefinitionFactory
{
    public const GRID_ID = 'no_qty_product_without_combination';

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans(
            'List of products without combinations and without available quantities for sale',
            [],
            'Admin.Catalog.Feature'
        );
    }
}
