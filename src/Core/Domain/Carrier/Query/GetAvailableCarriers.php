<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Query;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductQuantity;

/**
 * Get available carriers for a product list.
 */
class GetAvailableCarriers
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @var ProductQuantity[]
     */
    private $productQuantities;

    /**
     * @var int|null
     */
    private $currentCarrierId;

    /**
     * The keys are declared optional because the payloads assembled at runtime (by the Admin API in particular) offer
     * no guarantee: each entry is validated to hold both keys, with a strictly positive quantity.
     *
     * @param array<array{productId?: int, quantity?: int}> $productQuantities
     *
     * @throws CarrierConstraintException
     */
    public function __construct(array $productQuantities, int $addressId, ?int $currentCarrierId = null)
    {
        $this->productQuantities = array_map(
            function (array $productQuantity): ProductQuantity {
                if (!isset($productQuantity['productId'], $productQuantity['quantity'])
                    || (int) $productQuantity['quantity'] <= 0
                ) {
                    throw new CarrierConstraintException(
                        'Each product quantity must provide a "productId" and a strictly positive "quantity"',
                        CarrierConstraintException::INVALID_PRODUCT_QUANTITY
                    );
                }

                return new ProductQuantity(
                    new ProductId((int) $productQuantity['productId']),
                    (int) $productQuantity['quantity']
                );
            },
            $productQuantities
        );
        $this->addressId = new AddressId($addressId);
        $this->currentCarrierId = $currentCarrierId;
    }

    /**
     * @return ProductQuantity[]
     */
    public function getProductQuantities(): array
    {
        return $this->productQuantities;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return array_map(
            fn (ProductQuantity $pq) => $pq->getProductId()->getValue(),
            $this->productQuantities
        );
    }

    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    public function setAddressId(int $addressId): void
    {
        $this->addressId = new AddressId($addressId);
    }

    public function getCurrentCarrierId(): ?int
    {
        return $this->currentCarrierId;
    }
}
