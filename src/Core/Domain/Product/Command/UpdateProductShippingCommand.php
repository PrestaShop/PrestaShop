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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNotesType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product shipping options
 */
class UpdateProductShippingCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Number|null
     */
    private $width;

    /**
     * @var Number|null
     */
    private $height;

    /**
     * @var Number|null
     */
    private $depth;

    /**
     * @var Number|null
     */
    private $weight;

    /**
     * @var Number|null
     */
    private $additionalShippingCost;

    /**
     * @var int[]|null
     */
    private $carrierReferences;

    /**
     * @var DeliveryTimeNotesType
     */
    private $deliveryTimeNotesType;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeInStockNotes;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeOutOfStockNotes;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return Number|null
     */
    public function getWidth(): ?Number
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return UpdateProductShippingCommand
     */
    public function setWidth(string $width): UpdateProductShippingCommand
    {
        $this->width = new Number($width);

        return $this;
    }

    /**
     * @return Number|null
     */
    public function getHeight(): ?Number
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return UpdateProductShippingCommand
     */
    public function setHeight(string $height): UpdateProductShippingCommand
    {
        $this->height = new Number($height);

        return $this;
    }

    /**
     * @return Number|null
     */
    public function getDepth(): ?Number
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     *
     * @return UpdateProductShippingCommand
     */
    public function setDepth(string $depth): UpdateProductShippingCommand
    {
        $this->depth = new Number($depth);

        return $this;
    }

    /**
     * @return Number|null
     */
    public function getWeight(): ?Number
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     *
     * @return UpdateProductShippingCommand
     */
    public function setWeight(string $weight): UpdateProductShippingCommand
    {
        $this->weight = new Number($weight);

        return $this;
    }

    /**
     * @return Number|null
     */
    public function getAdditionalShippingCost(): ?Number
    {
        return $this->additionalShippingCost;
    }

    /**
     * @param string $additionalShippingCost
     *
     * @return UpdateProductShippingCommand
     */
    public function setAdditionalShippingCost(string $additionalShippingCost): UpdateProductShippingCommand
    {
        $this->additionalShippingCost = new Number($additionalShippingCost);

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getCarrierReferences(): ?array
    {
        return $this->carrierReferences;
    }

    /**
     * @param int[] $carrierReferences
     *
     * @return UpdateProductShippingCommand
     */
    public function setCarrierReferences(array $carrierReferences): UpdateProductShippingCommand
    {
        $this->carrierReferences = array_map('intval', $carrierReferences);

        return $this;
    }

    /**
     * @return DeliveryTimeNotesType|null
     */
    public function getDeliveryTimeNotesType(): ?DeliveryTimeNotesType
    {
        return $this->deliveryTimeNotesType;
    }

    /**
     * @param int $type
     *
     * @return UpdateProductShippingCommand
     */
    public function setDeliveryTimeNotesType(int $type): UpdateProductShippingCommand
    {
        $this->deliveryTimeNotesType = new DeliveryTimeNotesType($type);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeInStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeInStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeInStockNotes
     *
     * @return UpdateProductShippingCommand
     */
    public function setLocalizedDeliveryTimeInStockNotes(array $localizedDeliveryTimeInStockNotes): UpdateProductShippingCommand
    {
        $this->localizedDeliveryTimeInStockNotes = $localizedDeliveryTimeInStockNotes;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDeliveryTimeOutOfStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeOutOfStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     *
     * @return UpdateProductShippingCommand
     */
    public function setLocalizedDeliveryTimeOutOfStockNotes(array $localizedDeliveryTimeOutOfStockNotes)
    {
        $this->localizedDeliveryTimeOutOfStockNotes = $localizedDeliveryTimeOutOfStockNotes;

        return $this;
    }
}
