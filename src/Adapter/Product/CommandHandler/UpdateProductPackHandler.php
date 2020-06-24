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

use Pack;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductPackHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;

final class UpdateProductPackHandler extends AbstractProductHandler implements UpdateProductPackHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductPackCommand $command): void
    {
        $productsForPacking = $command->getProducts();
        $pack = $this->getProduct($command->getPackId());
        $packId = (int) $pack->id;

        // validate all products first
        foreach ($productsForPacking as $productForPacking) {
            $this->assertProductIsAvailableForPacking($productForPacking->getProductId()->getValue());
        }

        if (false === Pack::deleteItems($packId)) {
            throw new ProductPackException(
                sprintf('Failed deleting previous products from pack #%s before adding new ones', $packId),
                ProductPackException::FAILED_DELETING_PREVIOUS_PACKS
            );
        }

        //reset cache_default_attribute
        $pack->setDefaultAttribute(0);

        foreach ($productsForPacking as $productForPacking) {
            $productId = $productForPacking->getProductId()->getValue();
            $combinationId = $productForPacking->getCombinationId();

            if (null === $combinationId) {
                $combinationId = CombinationId::NO_COMBINATION;
            }

            try {
                $packed = Pack::addItem($packId, $productId, $productForPacking->getQuantity(), $combinationId);

                if (false === $packed) {
                    throw new ProductPackException(
                        $this->appendIdsToMessage('Failed adding product to pack.', $productForPacking, $packId),
                        ProductPackException::FAILED_ADDING_TO_PACK
                    );
                }
            } catch (PrestaShopException $e) {
                throw new ProductException(
                    $this->appendIdsToMessage('Error occurred when trying to add product to pack.', $productForPacking, $packId),
                    0,
                    $e
                );
            } finally {
                Pack::resetStaticCache();
            }
        }
    }

    /**
     * @param int $productId
     *
     * @throws ProductPackException
     */
    private function assertProductIsAvailableForPacking(int $productId): void
    {
        if (Pack::isPack($productId)) {
            throw new ProductPackException(
                sprintf('Product #%s is a pack itself. It cannot be packed', $productId),
                ProductPackException::CANNOT_ADD_PACK_INTO_PACK
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
        return sprintf(
            "$messageBody. [packId #%s; productId #%s; combinationId #%s]",
            $packId,
            $product->getProductId()->getValue(),
            $product->getCombinationId()->getValue()
        );
    }
}
