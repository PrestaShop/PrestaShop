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

namespace PrestaShop\PrestaShop\Adapter\Cart\Comparator;

use Cart;

/**
 * This class saves a cart's products when it's created, you can then ask for the difference
 * that happened on this cart.
 */
class CartProductsComparator
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var array
     */
    private $savedProducts;

    /**
     * @var CartProductUpdate[]
     */
    private $knownUpdates = [];

    /**
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        $this->savedProducts = $cart->getProducts(true);
    }

    /**
     * You can add a known update so that it won't be counted in the report
     *
     * @param CartProductUpdate $cartProductUpdate
     */
    public function addKnownUpdate(CartProductUpdate $cartProductUpdate): void
    {
        foreach ($this->knownUpdates as $knownUpdate) {
            if ($knownUpdate->productMatches($cartProductUpdate)) {
                $knownUpdate->setDeltaQuantity(
                    $knownUpdate->getDeltaQuantity() + $cartProductUpdate->getDeltaQuantity()
                );

                return;
            }
        }

        $this->knownUpdates[] = $cartProductUpdate;
    }

    /**
     * Returns a list of products that were updated compared to the creation of this object
     * except it removes the already known updated products.
     *
     * @return CartProductUpdate[]
     */
    public function getUpdatedProducts(): array
    {
        $filteredUpdates = [];
        $allUpdateProducts = $this->getAllUpdatedProducts();
        foreach ($allUpdateProducts as $updateProduct) {
            foreach ($this->knownUpdates as $knownUpdate) {
                if ($knownUpdate->productMatches($updateProduct)) {
                    $updateProduct->setDeltaQuantity(
                        $updateProduct->getDeltaQuantity() - $knownUpdate->getDeltaQuantity()
                    );

                    break;
                }
            }
            if (0 !== $updateProduct->getDeltaQuantity()) {
                $filteredUpdates[] = $updateProduct;
            }
        }

        return $filteredUpdates;
    }

    /**
     * Returns a list of all products that were updated compared to the creation of this object.
     *
     * @return CartProductUpdate[]
     */
    public function getAllUpdatedProducts(): array
    {
        $newProducts = $this->cart->getProducts(true);
        $updatedProducts = [];
        foreach ($this->savedProducts as $oldProduct) {
            // Then try and find the product in new products
            $newProduct = $this->getMatchingProduct($newProducts, $oldProduct);
            if (null === $newProduct) {
                $deltaQuantity = -(int) $oldProduct['cart_quantity'];
            } else {
                $deltaQuantity = (int) $newProduct['cart_quantity'] - (int) $oldProduct['cart_quantity'];
            }

            if ($deltaQuantity) {
                $updatedProducts[] = new CartProductUpdate(
                    (int) $oldProduct['id_product'],
                    (int) $oldProduct['id_product_attribute'],
                    $deltaQuantity
                );
            }
        }

        foreach ($newProducts as $newProduct) {
            // Then try and find the product in new products
            $oldProduct = $this->getMatchingProduct($this->savedProducts, $newProduct);
            if (null === $oldProduct) {
                $updatedProducts[] = new CartProductUpdate(
                    (int) $newProduct['id_product'],
                    (int) $newProduct['id_product_attribute'],
                    (int) $newProduct['cart_quantity']
                );
            }
        }

        return $updatedProducts;
    }

    /**
     * @param array $products
     * @param array $searchedProduct
     *
     * @return array|null
     */
    private function getMatchingProduct(array $products, array $searchedProduct): ?array
    {
        return array_reduce($products, function ($carry, $item) use ($searchedProduct) {
            if (null !== $carry) {
                return $carry;
            }

            $productMatch = $item['id_product'] == $searchedProduct['id_product'];
            $combinationMatch = $item['id_product_attribute'] == $searchedProduct['id_product_attribute'];

            return $productMatch && $combinationMatch ? $item : null;
        });
    }
}
