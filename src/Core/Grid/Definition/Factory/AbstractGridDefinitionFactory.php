<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\BulkActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Column;
use PrestaShop\PrestaShop\Core\Grid\Action\RowAction;
use PrestaShop\PrestaShop\Core\Grid\Definition\Definition;
use PrestaShopBundle\Translation\TranslatorAwareTrait;

/**
 * Class AbstractGridDefinitionFactory implements grid definition creation
 */
abstract class AbstractGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    final public function create()
    {
        $definition = new Definition(
            $this->getIdentifier(),
            $this->getName(),
            $this->getDefaultOrderBy(),
            $this->getDefaultOrderWay()
        );

        foreach ($this->getColumns() as $column) {
            $definition->addColumn($column);
        }

        foreach ($this->getRowActions() as $rowAction) {
            $definition->addRowAction($rowAction);
        }

        foreach ($this->getBulkActions() as $bulkAction) {
            $definition->addBulkAction($bulkAction);
        }

        return $definition;
    }

    /**
     * Get unique grid identifier
     *
     * @return string
     */
    abstract protected function getIdentifier();

    /**
     * Get translated grid name
     *
     * @return string
     */
    abstract protected function getName();

    /**
     * Get default order by for grid
     *
     * @return string
     */
    abstract protected function getDefaultOrderBy();

    /**
     * Get default order way for grid
     *
     * @return string
     */
    abstract protected function getDefaultOrderWay();

    /**
     * Get defined columns for grid
     *
     * @return array|Column[]
     */
    abstract protected function getColumns();

    /**
     * Get row actions for grid.
     * Override this method to add row actions for grid.
     *
     * @return array|RowAction[]
     */
    protected function getRowActions()
    {
        return [];
    }

    /**
     * Get bulk actions for grid.
     * Override this methods to add bulk actions for grid.
     *
     * @return array|BulkActionInterface[]
     */
    protected function getBulkActions()
    {
        return [];
    }
}
