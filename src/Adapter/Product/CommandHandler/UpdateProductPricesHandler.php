<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShopException;
use Product;

/**
 * Updates product price information using legacy object models
 */
final class UpdateProductPricesHandler extends AbstractProductHandler implements UpdateProductPricesHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductPricesCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $this->fillProductWithCommandData($product, $command);
        $this->performUpdate($product);
    }

    /**
     * @param Product $product
     * @param UpdateProductPricesCommand $command
     *
     * @throws ProductConstraintException
     */
    private function fillProductWithCommandData(Product $product, UpdateProductPricesCommand $command): void
    {
        if (null !== $command->getPrice()) {
            $product->price = (float) (string) $command->getPrice();
            $this->validateField($product, 'price', ProductConstraintException::INVALID_PRICE);
        }

        if (null !== $command->getEcotax()) {
            $product->ecotax = (float) (string) $command->getEcotax();
            $this->validateField($product, 'ecotax', ProductConstraintException::INVALID_ECOTAX);
        }

        if (null !== $command->getTaxRulesGroupId()) {
            $product->id_tax_rules_group = $command->getTaxRulesGroupId();
            $this->validateField($product, 'id_tax_rules_group', ProductConstraintException::INVALID_TAX_RULES_GROUP_ID);
        }

        if (null !== $command->isOnSale()) {
            $product->on_sale = $command->isOnSale();
        }

        if (null !== $command->getWholesalePrice()) {
            $product->wholesale_price = (float) (string) $command->getWholesalePrice();
            $this->validateField($product, 'wholesale_price', ProductConstraintException::INVALID_WHOLESALE_PRICE);
        }

        if (null !== $command->getUnitPrice()) {
            $product->unit_price = (float) (string) $command->getUnitPrice();
            $this->validateField($product, 'unit_price', ProductConstraintException::INVALID_UNIT_PRICE);
        }

        if (null !== $command->getUnity()) {
            $product->unity = $command->getUnity();
        }
    }

    /**
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     */
    private function performUpdate(Product $product): void
    {
        try {
            if (false === $product->update()) {
                throw new CannotUpdateProductException(
                    sprintf(
                        'Failed to update product #%s prices',
                        $product->id
                    ),
                    CannotUpdateProductException::FAILED_UPDATE_PRICES
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateProductException(
                sprintf(
                    'Error occurred when trying to update product #%s prices',
                    $product->id
                ),
                CannotUpdateProductException::FAILED_UPDATE_PRICES,
                $e
            );
        }
    }
}
