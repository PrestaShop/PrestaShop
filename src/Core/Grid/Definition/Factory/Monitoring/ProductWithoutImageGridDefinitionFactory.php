<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring;

/**
 * Builds Grid definition for product without image grid
 */
final class ProductWithoutImageGridDefinitionFactory extends AbstractProductGridDefinitionFactory
{
    public const GRID_ID = 'product_without_image';

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('List of products without images', [], 'Admin.Catalog.Feature');
    }
}
