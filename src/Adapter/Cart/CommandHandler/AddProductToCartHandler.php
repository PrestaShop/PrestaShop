<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
