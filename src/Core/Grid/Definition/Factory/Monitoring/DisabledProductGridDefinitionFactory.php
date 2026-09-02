<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring;

/**
 * Builds Grid definition for disabled product grid
 */
final class DisabledProductGridDefinitionFactory extends AbstractProductGridDefinitionFactory
{
    public const GRID_ID = 'disabled_product';

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('List of disabled products', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return parent::getColumns()->remove('active');
    }

    protected function getFilters()
    {
        return parent::getFilters()->remove('active');
    }
}
