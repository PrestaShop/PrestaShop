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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductToPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddProductToPackHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackingException;
use PrestaShopException;

final class AddProductToPackHandler extends AbstractProductHandler implements AddProductToPackHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToPackCommand $command): void
    {
        //@todo: ref AdminProductsController::3117 updatePackItems()
        // Product that is being edited becomes the Pack.
        $pack = $this->getProduct($command->getPackId());
        $productId = $command->getProductId()->getValue();
        $combinationId = $command->getCombinationId();

        if (null === $combinationId) {
            $combinationId = CombinationId::NO_COMBINATION;
        }

        try {
            $this->assertProductIsAvailableForPacking($productId);

            if (false === Pack::deleteItems($pack->id)) {
                throw new ProductPackingException(
                    $this->buildMessageWithCommandInputs('Failed adding product to pack.', $command),
                    ProductPackingException::FAILED_DELETING_PREVIOUS_PACKS
                );
            }
            //reset cache_default_attribute
            $pack->setDefaultAttribute(0);
            $packed = Pack::addItem($pack->id, $productId, $command->getQuantity(), $combinationId);
            Pack::resetStaticCache();

            if (false === $packed) {
                throw new ProductPackingException(
                    $this->buildMessageWithCommandInputs('Failed adding product to pack.', $command),
                    ProductPackingException::FAILED_ADDING_TO_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                $this->buildMessageWithCommandInputs('Error occurred when trying to add product to pack.', $command),
                0,
                $e
            );
        }
    }

    /**
     * @param int $productId
     *
     * @throws ProductPackingException
     */
    private function assertProductIsAvailableForPacking(int $productId): void
    {
        if (Pack::isPack($productId)) {
            throw new ProductPackingException(
                sprintf('Product #%s is a pack itself. It cannot be packed', $productId),
                ProductPackingException::CANNOT_ADD_PACK_INTO_PACK
            );
        }
    }

    /**
     * Builds string with ids from command, that will help to identify objects that was being updated in case of error
     *
     * @param string $messageBody
     * @param AddProductToPackCommand $command
     *
     * @return string
     */
    private function buildMessageWithCommandInputs(string $messageBody, AddProductToPackCommand $command): string
    {
        return sprintf(
            "$messageBody. [packId #%s; productId #%s; combinationId #%s]",
            $command->getPackId()->getValue(),
            $command->getProductId()->getValue(),
            $command->getCombinationId()->getValue()
        );
    }
}
