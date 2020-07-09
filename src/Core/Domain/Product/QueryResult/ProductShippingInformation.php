<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Transfers product shipping information data
 */
class ProductShippingInformation
{
    /**
     * @var Number
     */
    private $width;

    /**
     * @var Number
     */
    private $height;

    /**
     * @var Number
     */
    private $depth;

    /**
     * @var Number
     */
    private $weight;

    /**
     * @var Number
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
     * @param Number $width
     * @param Number $height
     * @param Number $depth
     * @param Number $weight
     * @param Number $additionalShippingCost
     * @param int[] $carrierReferences
     * @param int $deliveryTimeNotesType
     * @param string[] $localizedDeliveryTimeInStockNotes
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     */
    public function __construct(
        Number $width,
        Number $height,
        Number $depth,
        Number $weight,
        Number $additionalShippingCost,
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
     * @return Number
     */
    public function getWidth(): Number
    {
        return $this->width;
    }

    /**
     * @return Number
     */
    public function getHeight(): Number
    {
        return $this->height;
    }

    /**
     * @return Number
     */
    public function getDepth(): Number
    {
        return $this->depth;
    }

    /**
     * @return Number
     */
    public function getWeight(): Number
    {
        return $this->weight;
    }

    /**
     * @return Number
     */
    public function getAdditionalShippingCost(): Number
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
    public function getDeliveryTimeNotesType(): int
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
