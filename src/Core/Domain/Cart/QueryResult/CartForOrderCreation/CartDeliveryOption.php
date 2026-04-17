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
     * @param int $carrierId
     * @param string $carrierName
     * @param string $carrierDelay
     */
    public function __construct(int $carrierId, string $carrierName, string $carrierDelay)
    {
        $this->carrierId = $carrierId;
        $this->carrierName = $carrierName;
        $this->carrierDelay = $carrierDelay;
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
}
