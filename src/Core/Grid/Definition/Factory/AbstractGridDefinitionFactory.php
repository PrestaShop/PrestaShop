<?php
/**
 * 2007-2018 PrestaShop.
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

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Definition;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherAwareTrait;
use PrestaShopBundle\Translation\TranslatorAwareTrait;

/**
 * Class AbstractGridDefinitionFactory implements grid definition creation.
 */
abstract class AbstractGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    use TranslatorAwareTrait, HookDispatcherAwareTrait;

    /**
     * {@inheritdoc}
     */
    final public function create()
    {
        $definition = new Definition(
            $this->getId(),
            $this->getName(),
            $this->getColumns(),
            $this->getFilters(),
            $this->getGridActions(),
            $this->getBulkActions()
        );

        $this->dispatcher->dispatchForParameters('modifyGridDefinition', [
            'definition' => $definition,
        ]);

        return $definition;
    }

    /**
     * Get unique grid identifier.
     *
     * @return string
     */
    abstract protected function getId();

    /**
     * Get translated grid name.
     *
     * @return string
     */
    abstract protected function getName();

    /**
     * Get defined columns for grid.
     *
     * @return ColumnCollectionInterface
     */
    abstract protected function getColumns();

    /**
     * Get defined grid actions.
     * Override this method to define custom grid actions collection.
     *
     * @return GridActionCollectionInterface
     */
    protected function getGridActions()
    {
        return new GridActionCollection();
    }

    /**
     * Get defined bulk actions.
     * Override this method to define custom bulk actions collection.
     *
     * @return BulkActionCollectionInterface
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection();
    }

    /**
     * Get defined filters.
     * Override this method to define custom filters collection.
     *
     * @return FilterCollectionInterface
     */
    protected function getFilters()
    {
        return new FilterCollection();
    }
}
