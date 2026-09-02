<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring;

/**
 * Builds Grid definition for product with combination and without quantities grid
 */
final class NoQtyProductWithCombinationGridDefinitionFactory extends AbstractProductGridDefinitionFactory
{
    public const GRID_ID = 'no_qty_product_with_combination';

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans(
            'List of products with combinations but without available quantities for sale',
            [],
            'Admin.Catalog.Feature'
        );
    }
}
