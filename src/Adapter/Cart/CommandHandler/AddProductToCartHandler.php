<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddProductToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateProductQuantityInCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;

/**
 * Handles add product to cart command
 */
final class AddProductToCartHandler implements AddProductToCartHandlerInterface
{
    /**
     * @var AddCustomizationFieldsHandlerInterface
     */
    private $addCustomizationFieldsHandler;

    /**
     * @var UpdateProductQuantityInCartHandlerInterface
     */
    private $updateProductQuantityInCartHandler;

    /**
     * @param AddCustomizationFieldsHandlerInterface $addCustomizationFieldsHandler
     * @param UpdateProductQuantityInCartHandlerInterface $updateProductQuantityInCartHandler
     */
    public function __construct(
        AddCustomizationFieldsHandlerInterface $addCustomizationFieldsHandler,
        UpdateProductQuantityInCartHandlerInterface $updateProductQuantityInCartHandler
    ) {
        $this->addCustomizationFieldsHandler = $addCustomizationFieldsHandler;
        $this->updateProductQuantityInCartHandler = $updateProductQuantityInCartHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToCartCommand $command): void
    {
        $quantity = $command->getQuantity();
        $this->assertQuantityIsPositiveInt($quantity);

        $cartIdValue = $command->getCartId()->getValue();
        $productIdValue = $command->getProductId()->getValue();
        $combinationId = $command->getCombinationId();

        if (!empty($command->getCustomizationsByFieldIds())) {
            $customizationId = $this->addCustomizationFieldsHandler->handle(new AddCustomizationFieldsCommand(
                $cartIdValue,
                $command->getProductId()->getValue(),
                $command->getCustomizationsByFieldIds()
            ));
        }

        $this->updateProductQuantityInCartHandler->handle(new UpdateProductQuantityInCartCommand(
            $cartIdValue,
            $productIdValue,
            $quantity,
            $combinationId ? $combinationId->getValue() : null,
            isset($customizationId) ? $customizationId->getValue() : null
        ));
    }

    /**
     * @param int $quantity
     *
     * @throws CartConstraintException
     */
    private function assertQuantityIsPositiveInt(int $quantity): void
    {
        if (0 > $quantity) {
            throw new CartConstraintException(
                sprintf('Quantity must be positive integer, but %s given.', $quantity),
                CartConstraintException::INVALID_QUANTITY
            );
        }
    }
}
