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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

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
     * @var DecimalNumber|null
     */
    private $width;

    /**
     * @var DecimalNumber|null
     */
    private $height;

    /**
     * @var DecimalNumber|null
     */
    private $depth;

    /**
     * @var DecimalNumber|null
     */
    private $weight;

    /**
     * @var DecimalNumber|null
     */
    private $additionalShippingCost;

    /**
     * @var CarrierReferenceId[]|null
     */
    private $carrierReferenceIds;

    /**
     * @var DeliveryTimeNoteType
     */
    private $deliveryTimeNoteType;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeInStockNotes;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeOutOfStockNotes;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        int $productId,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getWidth(): ?DecimalNumber
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
        $width = new DecimalNumber($width);
        $this->assertPackageDimensionIsPositiveOrZero($width, 'width');
        $this->width = $width;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getHeight(): ?DecimalNumber
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
        $height = new DecimalNumber($height);
        $this->assertPackageDimensionIsPositiveOrZero($height, 'height');
        $this->height = $height;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getDepth(): ?DecimalNumber
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
        $depth = new DecimalNumber($depth);
        $this->assertPackageDimensionIsPositiveOrZero($depth, 'depth');
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getWeight(): ?DecimalNumber
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
        $weight = new DecimalNumber($weight);
        $this->assertPackageDimensionIsPositiveOrZero($weight, 'weight');
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getAdditionalShippingCost(): ?DecimalNumber
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
        $this->additionalShippingCost = new DecimalNumber($additionalShippingCost);

        return $this;
    }

    /**
     * @return CarrierReferenceId[]|null
     */
    public function getCarrierReferenceIds(): ?array
    {
        return $this->carrierReferenceIds;
    }

    /**
     * @param int[] $carrierReferenceIds
     *
     * @return UpdateProductShippingCommand
     */
    public function setCarrierReferenceIds(array $carrierReferenceIds): UpdateProductShippingCommand
    {
        foreach (array_unique($carrierReferenceIds) as $carrierReferenceId) {
            $this->carrierReferenceIds[] = new CarrierReferenceId((int) $carrierReferenceId);
        }

        return $this;
    }

    /**
     * @return DeliveryTimeNoteType|null
     */
    public function getDeliveryTimeNoteType(): ?DeliveryTimeNoteType
    {
        return $this->deliveryTimeNoteType;
    }

    /**
     * @param int $type
     *
     * @return UpdateProductShippingCommand
     */
    public function setDeliveryTimeNoteType(int $type): UpdateProductShippingCommand
    {
        $this->deliveryTimeNoteType = new DeliveryTimeNoteType($type);

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
    public function setLocalizedDeliveryTimeOutOfStockNotes(array $localizedDeliveryTimeOutOfStockNotes): UpdateProductShippingCommand
    {
        $this->localizedDeliveryTimeOutOfStockNotes = $localizedDeliveryTimeOutOfStockNotes;

        return $this;
    }

    /**
     * @todo: dimensions deserves dedicated VO and might be worth reusing in Carriers page.
     *
     * @todo Check https://github.com/PrestaShop/PrestaShop/issues/19666#issuecomment-756088706
     *
     * @param DecimalNumber $value
     * @param string $dimensionName
     *
     * @throws ProductConstraintException
     */
    private function assertPackageDimensionIsPositiveOrZero(DecimalNumber $value, string $dimensionName): void
    {
        if ($value->isGreaterOrEqualThanZero()) {
            return;
        }

        $codeByDimension = [
            'width' => ProductConstraintException::INVALID_WIDTH,
            'height' => ProductConstraintException::INVALID_HEIGHT,
            'depth' => ProductConstraintException::INVALID_DEPTH,
            'weight' => ProductConstraintException::INVALID_WEIGHT,
        ];

        throw new ProductConstraintException(
            sprintf('Invalid product %s, it must be positive number or zero', $dimensionName),
            $codeByDimension[$dimensionName]
        );
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
