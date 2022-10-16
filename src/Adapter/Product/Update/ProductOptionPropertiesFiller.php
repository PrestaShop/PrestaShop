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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSubCommandInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;

//@todo: tag these
class ProductOptionPropertiesFiller implements ProductUpdatablePropertiesFillerInterface
{
    /**
     * @param Product $product
     * @param UpdateProductSubCommandInterface $subCommand
     * @param ShopConstraint|null $shopConstraint
     *
     * @return array
     */
    public function fillUpdatableProperties(
        Product $product,
        UpdateProductSubCommandInterface $subCommand,
        ?ShopConstraint $shopConstraint
    ): array {
        if (!($subCommand instanceof UpdateProductOptionsCommand)) {
            //@todo: dedicated exception
            throw new \Exception('Invalid command');
        }

        $updatableProperties = [];

        if (null !== $subCommand->getVisibility()) {
            $product->visibility = $subCommand->getVisibility()->getValue();
            $updatableProperties[] = 'visibility';
        }

        if (null !== $subCommand->isAvailableForOrder()) {
            $product->available_for_order = $subCommand->isAvailableForOrder();
            $updatableProperties[] = 'available_for_order';
        }
        $availableForOrder = $product->available_for_order;

        if (null !== $subCommand->showPrice() && !$availableForOrder) {
            $product->show_price = $subCommand->showPrice();
            $updatableProperties[] = 'show_price';
        } elseif ($availableForOrder && !$product->show_price) {
            $product->show_price = true;
            $updatableProperties[] = 'show_price';
        }

        if (null !== $subCommand->isOnlineOnly()) {
            $product->online_only = $subCommand->isOnlineOnly();
            $updatableProperties[] = 'online_only';
        }

        if (null !== $subCommand->getCondition()) {
            $product->condition = $subCommand->getCondition()->getValue();
            $updatableProperties[] = 'condition';
        }

        if (null !== $subCommand->showCondition()) {
            $product->show_condition = $subCommand->showCondition();
            $updatableProperties[] = 'show_condition';
        }

        $manufacturerId = $subCommand->getManufacturerId();
        if (null !== $manufacturerId) {
            $product->id_manufacturer = $manufacturerId->getValue();
            $updatableProperties[] = 'id_manufacturer';
        }

        return $updatableProperties;
    }
}
