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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductOptionsInput;
use Product;

class ProductOptionsFiller
{
    /**
     * @param Product $product
     * @param ProductOptionsInput $input
     *
     * @return string[]|array<string, int[]> updatable properties
     */
    public function fillUpdatableProperties(Product $product, ProductOptionsInput $input): array
    {
        $updatableProperties = [];

        if (null !== $input->getVisibility()) {
            $product->visibility = $input->getVisibility()->getValue();
            $updatableProperties[] = 'visibility';
        }

        if (null !== $input->isAvailableForOrder()) {
            $product->available_for_order = $input->isAvailableForOrder();
            $updatableProperties[] = 'available_for_order';
        }
        $availableForOrder = $product->available_for_order;

        if (null !== $input->showPrice() && !$availableForOrder) {
            $product->show_price = $input->showPrice();
            $updatableProperties[] = 'show_price';
        } elseif ($availableForOrder && !$product->show_price) {
            $product->show_price = true;
            $updatableProperties[] = 'show_price';
        }

        if (null !== $input->isOnlineOnly()) {
            $product->online_only = $input->isOnlineOnly();
            $updatableProperties[] = 'online_only';
        }

        if (null !== $input->getCondition()) {
            $product->condition = $input->getCondition()->getValue();
            $updatableProperties[] = 'condition';
        }

        if (null !== $input->showCondition()) {
            $product->show_condition = $input->showCondition();
            $updatableProperties[] = 'show_condition';
        }

        $manufacturerId = $input->getManufacturerId();
        if (null !== $manufacturerId) {
            $product->id_manufacturer = $manufacturerId->getValue();
            $updatableProperties[] = 'id_manufacturer';
        }

        return $updatableProperties;
    }
}
