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

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class BulkActionCollection holds bulk action collection available for grid
 *
 * @property BulkActionInterface[] $items
 */
final class BulkActionCollection extends AbstractCollection implements BulkActionCollectionInterface
{
    /**
     * Create bulk collection from metho
     *
     * @param array $data
     *
     * @return BulkActionCollection
     */
    public static function fromArray(array $data)
    {
        $collection = new self();

        foreach ($data as $actionArray) {
            $action = BulkAction::fromArray($actionArray);

            $collection->add($action);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function add(BulkActionInterface $bulkAction)
    {
        $this->items[$bulkAction->getId()] = $bulkAction;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $bulkActionsArray = [];

        foreach ($this->items as $bulkAction) {
            $bulkActionsArray[] = [
                'id' => $bulkAction->getId(),
                'name' => $bulkAction->getName(),
                'icon' => $bulkAction->getIcon(),
            ];
        }

        return $bulkActionsArray;
    }
}
