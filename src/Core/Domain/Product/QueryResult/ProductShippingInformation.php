<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
