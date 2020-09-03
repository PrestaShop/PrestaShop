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

use Attribute;
use Cart;
use Context;
use Customer;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateProductQuantityInCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;

/**
 * @internal
 */
final class UpdateProductQuantityInCartHandler extends AbstractCartHandler implements UpdateProductQuantityInCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductQuantityInCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());
        $previousQty = $this->findPreviousQuantityInCart($cart, $command);
        $qtyDiff = abs($command->getNewQuantity() - $previousQty);

        if ($qtyDiff === 0) {
            throw new CartConstraintException(sprintf('Cart quantity is already %d', $command->getNewQuantity()), CartConstraintException::UNCHANGED_QUANTITY);
        }

        // $cart::updateQty needs customer context
        $customer = new Customer($cart->id_customer);
        Context::getContext()->customer = $customer;

        $this->assertOrderDoesNotExistForCart($cart);

        $product = $this->getProductObject($command->getProductId());
        $combinationIdValue = $command->getCombinationId() ? $command->getCombinationId()->getValue() : 0;
        $customizationId = $command->getCustomizationId();

        $this->assertProductIsInStock($product, $command);
        $this->assertProductCustomization($product, $command);

        if ($previousQty < $command->getNewQuantity()) {
            $action = 'up';
        } else {
            $action = 'down';
        }

        $updateResult = $cart->updateQty(
            $qtyDiff,
            $command->getProductId()->getValue(),
            $combinationIdValue,
            $customizationId ? $customizationId->getValue() : false,
            $action
        );

        if (!$updateResult) {
            throw new CartException('Failed to update product quantity in cart');
        }

        // It seems that $updateResult can be -1,
        // when adding product with less quantity than minimum required.
        if ($updateResult < 0) {
            $minQuantity = $command->getCustomizationId() ?
                Attribute::getAttributeMinimalQty($combinationIdValue) :
                $product->minimal_quantity;

            throw new CartException(sprintf('Minimum quantity of %d must be added to cart.', $minQuantity));
        }
    }

    /**
     * @param Cart $cart
     *
     * @throws CartException
     */
    private function assertOrderDoesNotExistForCart(Cart $cart)
    {
        if ($cart->orderExists()) {
            throw new CartException(sprintf('Order for cart with id "%s" already exists.', $cart->id));
        }
    }

    /**
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    private function getProductObject(ProductId $productId)
    {
        $product = new Product($productId->getValue(), true);

        if ($product->id !== $productId->getValue()) {
            throw new ProductNotFoundException(sprintf('Product with id "%s" was not found', $productId->getValue()));
        }

        return $product;
    }

    /**
     * @param Product $product
     * @param UpdateProductQuantityInCartCommand $command
     *
     * @throws ProductOutOfStockException
     */
    private function assertProductIsInStock(Product $product, UpdateProductQuantityInCartCommand $command)
    {
        if (null !== $command->getCombinationId()) {
            $isAvailableWhenOutOfStock = Product::isAvailableWhenOutOfStock($product->out_of_stock);
            $isEnoughQuantity = Attribute::checkAttributeQty(
                $command->getCombinationId()->getValue(),
                $command->getNewQuantity()
            );

            if (!$isAvailableWhenOutOfStock && !$isEnoughQuantity) {
                throw new ProductOutOfStockException(sprintf('Product with id "%s" is out of stock, thus cannot be added to cart', $product->id));
            }

            return;
        }

        if (!$product->checkQty($command->getNewQuantity())) {
            throw new ProductOutOfStockException(sprintf('Product with id "%s" is out of stock, thus cannot be added to cart', $product->id));
        }
    }

    /**
     * If product is customizable and customization is not provided,
     * then exception is thrown.
     *
     * @param Product $product
     * @param UpdateProductQuantityInCartCommand $command
     *
     * @throws ProductException
     */
    private function assertProductCustomization(Product $product, UpdateProductQuantityInCartCommand $command)
    {
        if (null === $command->getCustomizationId() && !$product->hasAllRequiredCustomizableFields()) {
            throw new ProductException(sprintf('Missing customization for product with id "%s"', $product->id));
        }
    }

    /**
     * @param Cart $cart
     * @param UpdateProductQuantityInCartCommand $command
     *
     * @return int
     */
    private function findPreviousQuantityInCart(Cart $cart, UpdateProductQuantityInCartCommand $command): int
    {
        $products = $cart->getProductsWithSeparatedGifts();

        $isCombination = ($command->getCombinationId() !== null);
        $isCustomization = ($command->getCustomizationId() !== null);

        foreach ($products as $cartProduct) {
            if (!empty($cartProduct['is_gift'])) {
                continue;
            }
            $equalProductId = (int) $cartProduct['id_product'] === $command->getProductId()->getValue();
            if ($isCombination) {
                if ($equalProductId && (int) $cartProduct['id_product_attribute'] === $command->getCombinationId()->getValue()) {
                    return (int) $cartProduct['quantity'];
                }
            } elseif ($isCustomization) {
                if ($equalProductId && (int) $cartProduct['id_customization'] === $command->getCustomizationId()->getValue()) {
                    return (int) $cartProduct['quantity'];
                }
            } elseif ($equalProductId) {
                return (int) $cartProduct['quantity'];
            }
        }

        return 0;
    }
}
