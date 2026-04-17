<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Query;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
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
     * @param ProductQuantity[] $productQuantities
     */
    public function __construct(array $productQuantities, AddressId $addressId, ?int $currentCarrierId = null)
    {
        $this->productQuantities = $productQuantities;
        $this->addressId = $addressId;
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

    public function setAddressId(AddressId $addressId): void
    {
        $this->addressId = $addressId;
    }

    public function getCurrentCarrierId(): ?int
    {
        return $this->currentCarrierId;
    }
}
