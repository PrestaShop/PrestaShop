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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;

class OrderDetailQuantity
{
    /**
     * @var array<int, array{id_order_detail: int, quantity: int}>
     */
    private array $items;

    /**
     * @param array<int, array{id_order_detail: int, quantity: int}> $items
     *
     * @throws ShipmentException
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!isset($item['id_order_detail']) || !isset($item['quantity'])) {
                throw new ShipmentException(sprintf('Invalid order detail quantity item: %s', json_encode($item)));
            }
        }

        $this->items = $items;
    }

    /**
     * @return array<int, array{id_order_detail: int, quantity: int}>
     */
    public function getValue(): array
    {
        return $this->items;
    }
}
