<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;

/**
 * Interface GridDefinitionInterface defines contract for grid definition.
 */
interface GridDefinitionInterface
{
    /**
     * Get unique grid identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get grid name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get grid columns.
     *
     * @return ColumnCollectionInterface
     */
    public function getColumns();

    /**
     * @param string $id
     *
     * @return ColumnInterface
     */
    public function getColumnById(string $id): ColumnInterface;

    /**
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions();

    /**
     * Get grid actions.
     *
     * @return GridActionCollectionInterface
     */
    public function getGridActions();

    /**
     * @return ViewOptionsCollectionInterface
     */
    public function getViewOptions();

    /**
     * Get filters.
     *
     * @return FilterCollectionInterface
     */
    public function getFilters();
}
