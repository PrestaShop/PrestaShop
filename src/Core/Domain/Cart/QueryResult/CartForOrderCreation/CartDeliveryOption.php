<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds data of cart delivery option
 */
class CartDeliveryOption
{
    /**
     * @var int
     */
    private $carrierId;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @var string
     */
    private $carrierDelay;

    /**
     * @var string
     */
    private $extraContent;

    /**
     * @param int $carrierId
     * @param string $carrierName
     * @param string $carrierDelay
     */
    public function __construct(int $carrierId, string $carrierName, string $carrierDelay, string $extraContent = '')
    {
        $this->carrierId = $carrierId;
        $this->carrierName = $carrierName;
        $this->carrierDelay = $carrierDelay;
        $this->extraContent = $extraContent;
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    /**
     * @return string
     */
    public function getCarrierName(): string
    {
        return $this->carrierName;
    }

    /**
     * @return string
     */
    public function getCarrierDelay(): string
    {
        return $this->carrierDelay;
    }

    /**
     * Markup a carrier's own module contributed for this delivery option, empty when the
     * carrier belongs to no module or that module is not allowed to emit back office HTML.
     */
    public function getExtraContent(): string
    {
        return $this->extraContent;
    }
}
