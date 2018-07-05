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

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;

/**
 * Interface GridDefinitionInterface defines contract for grid definition.
 */
interface DefinitionInterface
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
     * Set grid name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get grid columns.
     *
     * @return ColumnCollectionInterface
     */
    public function getColumns();

    /**
     * Set grid columns.
     *
     * @param ColumnCollectionInterface $columns
     *
     * @return self
     */
    public function setColumns(ColumnCollectionInterface $columns);

    /**
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions();

    /**
     * @param BulkActionCollectionInterface $bulkActions
     *
     * @return self
     */
    public function setBulkActions(BulkActionCollectionInterface $bulkActions);

    /**
     * Get grid actions.
     *
     * @return GridActionCollectionInterface
     */
    public function getGridActions();

    /**
     * Set grid actions.
     *
     * @param GridActionCollectionInterface $gridActions
     *
     * @return self
     */
    public function setGridActions(GridActionCollectionInterface $gridActions);
}
