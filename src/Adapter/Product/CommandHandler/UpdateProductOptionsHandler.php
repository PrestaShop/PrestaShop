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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Product;

/**
 * Handles UpdateProductOptionsCommand using legacy object models
 */
final class UpdateProductOptionsHandler extends AbstractProductHandler implements UpdateProductOptionsHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductOptionsCommand $command): void
    {
        $productId = $command->getProductId();

        $product = $this->getProduct($productId);
        $this->fillUpdatableFieldsWithCommandData($product, $command);
        $product->setFieldsToUpdate($this->fieldsToUpdate);

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_OPTIONS);
    }

    /**
     * @param Product $product
     * @param UpdateProductOptionsCommand $command
     */
    private function fillUpdatableFieldsWithCommandData(Product $product, UpdateProductOptionsCommand $command): void
    {
        if (null !== $command->getVisibility()) {
            $product->visibility = $command->getVisibility()->getValue();
            $this->fieldsToUpdate['visibility'] = true;
        }

        if (null !== $command->isAvailableForOrder()) {
            $product->available_for_order = $command->isAvailableForOrder();
            $this->fieldsToUpdate['available_for_order'] = true;
        }

        if (null !== $command->isOnlineOnly()) {
            $product->online_only = $command->isOnlineOnly();
            $this->fieldsToUpdate['online_only'] = true;
        }

        if (null !== $command->showPrice()) {
            $product->show_price = $command->showPrice();
            $this->fieldsToUpdate['show_price'] = true;
        }

        if (null !== $command->getCondition()) {
            $product->condition = $command->getCondition()->getValue();
            $this->fieldsToUpdate['condition'] = true;
        }

        if (null !== $command->getEan13()) {
            $product->ean13 = $command->getEan13()->getValue();
            $this->fieldsToUpdate['ean13'] = true;
        }

        if (null !== $command->getIsbn()) {
            $product->isbn = $command->getIsbn()->getValue();
            $this->fieldsToUpdate['isbn'] = true;
        }

        if (null !== $command->getMpn()) {
            $product->mpn = $command->getMpn();
            $this->validateField($product, 'mpn', ProductConstraintException::INVALID_MPN);
            $this->fieldsToUpdate['mpn'] = true;
        }

        if (null !== $command->getReference()) {
            $product->reference = $command->getReference()->getValue();
            $this->fieldsToUpdate['reference'] = true;
        }

        if (null !== $command->getUpc()) {
            $product->upc = $command->getUpc()->getValue();
            $this->fieldsToUpdate['upc'] = true;
        }
    }
}
