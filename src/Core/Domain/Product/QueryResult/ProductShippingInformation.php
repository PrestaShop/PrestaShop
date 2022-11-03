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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Transfers product shipping information data
 */
class ProductShippingInformation
{
    /**
     * @var DecimalNumber
     */
    private $width;

    /**
     * @var DecimalNumber
     */
    private $height;

    /**
     * @var DecimalNumber
     */
    private $depth;

    /**
     * @var DecimalNumber
     */
    private $weight;

    /**
     * @var DecimalNumber
     */
    private $additionalShippingCost;

    /**
     * @var int[]
     */
    private $carrierReferences;

    /**
     * @var int
     */
    private $deliveryTimeNotesType;

    /**
     * @var string[]
     */
    private $localizedDeliveryTimeInStockNotes;

    /**
     * @var string[]
     */
    private $localizedDeliveryTimeOutOfStockNotes;

    /**
     * @param DecimalNumber $width
     * @param DecimalNumber $height
     * @param DecimalNumber $depth
     * @param DecimalNumber $weight
     * @param DecimalNumber $additionalShippingCost
     * @param int[] $carrierReferences
     * @param int $deliveryTimeNotesType
     * @param string[] $localizedDeliveryTimeInStockNotes
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     */
    public function __construct(
        DecimalNumber $width,
        DecimalNumber $height,
        DecimalNumber $depth,
        DecimalNumber $weight,
        DecimalNumber $additionalShippingCost,
        array $carrierReferences,
        int $deliveryTimeNotesType,
        array $localizedDeliveryTimeInStockNotes,
        array $localizedDeliveryTimeOutOfStockNotes
    ) {
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
        $this->weight = $weight;
        $this->additionalShippingCost = $additionalShippingCost;
        $this->carrierReferences = $carrierReferences;
        $this->deliveryTimeNotesType = $deliveryTimeNotesType;
        $this->localizedDeliveryTimeInStockNotes = $localizedDeliveryTimeInStockNotes;
        $this->localizedDeliveryTimeOutOfStockNotes = $localizedDeliveryTimeOutOfStockNotes;
    }

    /**
     * @return DecimalNumber
     */
    public function getWidth(): DecimalNumber
    {
        return $this->width;
    }

    /**
     * @return DecimalNumber
     */
    public function getHeight(): DecimalNumber
    {
        return $this->height;
    }

    /**
     * @return DecimalNumber
     */
    public function getDepth(): DecimalNumber
    {
        return $this->depth;
    }

    /**
     * @return DecimalNumber
     */
    public function getWeight(): DecimalNumber
    {
        return $this->weight;
    }

    /**
     * @return DecimalNumber
     */
    public function getAdditionalShippingCost(): DecimalNumber
    {
        return $this->additionalShippingCost;
    }

    /**
     * @return int[]
     */
    public function getCarrierReferences(): array
    {
        return $this->carrierReferences;
    }

    /**
     * @return int
     */
    public function getDeliveryTimeNoteType(): int
    {
        return $this->deliveryTimeNotesType;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeInStockNotes(): array
    {
        return $this->localizedDeliveryTimeInStockNotes;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeOutOfStockNotes(): array
    {
        return $this->localizedDeliveryTimeOutOfStockNotes;
    }
}
