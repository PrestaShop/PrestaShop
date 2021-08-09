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

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\Repository;

use Pack;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

class ProductPackRepository
{
    /**
     * @param PackId $packId
     * @param QuantifiedProduct $productForPacking
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function addProductToPack(PackId $packId, QuantifiedProduct $productForPacking): void
    {
        $packIdValue = $packId->getValue();

        try {
            $packed = Pack::addItem(
                $packIdValue,
                $productForPacking->getProductId()->getValue(),
                $productForPacking->getQuantity(),
                $productForPacking->getCombinationId() ?
                    $productForPacking->getCombinationId()->getValue() :
                    CombinationId::NO_COMBINATION
            );
            if (!$packed) {
                throw new ProductPackException(
                    $this->appendIdsToMessage('Failed to add product to pack.', $productForPacking, $packIdValue),
                    ProductPackException::FAILED_ADDING_TO_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                $this->appendIdsToMessage('Error occurred when trying to add product to pack.', $productForPacking, $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * @param PackId $packId
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function removeAllProductsFromPack(PackId $packId): void
    {
        $packIdValue = $packId->getValue();

        try {
            if (!Pack::deleteItems($packIdValue)) {
                throw new ProductPackException(
                    sprintf('Failed to remove products from pack #%d', $packIdValue),
                    ProductPackException::FAILED_DELETING_PRODUCTS_FROM_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to remove pack items from pack #%d', $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * Builds string with ids, that will help to identify objects that was being updated in case of error
     *
     * @param string $messageBody
     * @param QuantifiedProduct $product
     * @param int $packId
     *
     * @return string
     */
    private function appendIdsToMessage(string $messageBody, QuantifiedProduct $product, int $packId): string
    {
        if ($product->getCombinationId()) {
            $combinationId = sprintf(' combinationId #%d', $product->getCombinationId()->getValue());
        }

        return sprintf(
            "$messageBody. [packId #%d; productId #%d;%s]",
            $packId,
            $product->getProductId()->getValue(),
            isset($combinationId) ? $combinationId : ''
        );
    }
}
