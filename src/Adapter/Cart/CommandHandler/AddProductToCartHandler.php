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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCustomizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddProductToCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateProductQuantityInCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;

/**
 * Handles add product to cart command
 */
#[AsCommandHandler]
final class AddProductToCartHandler extends AbstractCartHandler implements AddProductToCartHandlerInterface
{
    /**
     * @var AddCustomizationHandlerInterface
     */
    private $addCustomizationHandler;

    /**
     * @var UpdateProductQuantityInCartHandlerInterface
     */
    private $updateProductQuantityInCartHandler;

    /**
     * @param AddCustomizationHandlerInterface $addCustomizationHandler
     * @param UpdateProductQuantityInCartHandlerInterface $updateProductQuantityInCartHandler
     */
    public function __construct(
        AddCustomizationHandlerInterface $addCustomizationHandler,
        UpdateProductQuantityInCartHandlerInterface $updateProductQuantityInCartHandler
    ) {
        $this->addCustomizationHandler = $addCustomizationHandler;
        $this->updateProductQuantityInCartHandler = $updateProductQuantityInCartHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToCartCommand $command): void
    {
        $cartIdValue = $command->getCartId()->getValue();
        $productIdValue = $command->getProductId()->getValue();
        $combinationId = null !== $command->getCombinationId() ? $command->getCombinationId()->getValue() : null;
        $customizationId = null;

        if (!empty($command->getCustomizationsByFieldIds())) {
            $customizationIdVO = $this->addCustomizationHandler->handle(new AddCustomizationCommand(
                $cartIdValue,
                $command->getProductId()->getValue(),
                $command->getCustomizationsByFieldIds()
            ));
            if (null !== $customizationIdVO) {
                $customizationId = $customizationIdVO->getValue();
            }
        }

        $cart = $this->getCart($command->getCartId());
        $product = $cart->getProductQuantity($productIdValue, $combinationId, $customizationId);

        $quantity = $command->getQuantity() + (int) $product['quantity'];
        $this->assertQuantityIsPositiveInt($quantity);

        $this->updateProductQuantityInCartHandler->handle(new UpdateProductQuantityInCartCommand(
            $cartIdValue,
            $productIdValue,
            $quantity,
            $combinationId,
            $customizationId
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
