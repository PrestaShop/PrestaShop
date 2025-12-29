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
