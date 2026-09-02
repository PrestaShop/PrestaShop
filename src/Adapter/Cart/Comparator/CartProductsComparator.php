<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param array $knownUpdates
     *
     * @return CartProductsComparator
     */
    public function setKnownUpdates(array $knownUpdates): self
    {
        $this->knownUpdates = $knownUpdates;

        return $this;
    }

    /**
     * Returns a list of products that were strictly updated (not created) compared to the state of the cart
     * when this object was created, it removes the already known modified products provided as argument.
     *
     * @return CartProductUpdate[]
     */
    public function getUpdatedProducts(): array
    {
        $newProducts = $this->cart->getProducts(true);
        $allUpdateProducts = $this->getAllUpdatedProducts($newProducts);

        return $this->filterKnownUpdates($allUpdateProducts);
    }

    /**
     * Returns a list of products that were strictly created (not updated) compared to the state of the cart
     * when this object was created, it removes the already known modified products provided as argument.
     *
     * @return CartProductUpdate[]
     */
    public function getAdditionalProducts(): array
    {
        $newProducts = $this->cart->getProducts(true);
        $allAdditionalProducts = $this->getAllAdditionalProducts($newProducts);

        return $this->filterKnownUpdates($allAdditionalProducts);
    }

    /**
     * Returns a list of products that were modified (created and/or updated) compared to the state of the cart
     * when this object was created, it removes the already known modified products provided as argument.
     *
     * @return CartProductUpdate[]
     */
    public function getModifiedProducts(): array
    {
        $newProducts = $this->cart->getProducts(true);
        $modifiedProducts = array_merge($this->getAllUpdatedProducts($newProducts), $this->getAllAdditionalProducts($newProducts));

        return $this->filterKnownUpdates($modifiedProducts);
    }

    /**
     * Returns the list of updates for products that were not in the cart previously
     *
     * @param array[] $newProducts
     *
     * @return array
     */
    private function getAllAdditionalProducts(array $newProducts): array
    {
        $additionalProducts = [];
        foreach ($newProducts as $newProduct) {
            // Then try and find the product in new products
            $oldProduct = $this->getMatchingProduct($this->savedProducts, $newProduct);
            if (null === $oldProduct) {
                $additionalProducts[] = new CartProductUpdate(
                    (int) $newProduct['id_product'],
                    (int) $newProduct['id_product_attribute'],
                    (int) $newProduct['cart_quantity'],
                    true,
                    (int) $newProduct['id_customization']
                );
            }
        }

        return $additionalProducts;
    }

    /**
     * Returns a list of all products that were updated compared to the creation of this object.
     *
     * @param array[] $newProducts
     *
     * @return CartProductUpdate[]
     */
    private function getAllUpdatedProducts(array $newProducts): array
    {
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
                    $deltaQuantity,
                    false,
                    (int) $oldProduct['id_customization']
                );
            }
        }

        return $updatedProducts;
    }

    /**
     * @param CartProductUpdate[] $updates
     *
     * @return CartProductUpdate[]
     */
    private function filterKnownUpdates(array $updates): array
    {
        $filteredUpdates = [];
        foreach ($updates as $updateProduct) {
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
            $customizationMatch = $item['id_customization'] == $searchedProduct['id_customization'];

            return $productMatch && $combinationMatch && $customizationMatch ? $item : null;
        });
    }
}
