<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderShippingForViewing
{
    /**
     * @var OrderCarrierForViewing[]
     */
    private $carriers = [];

    /**
     * @var bool
     */
    private $isRecycledPackaging;

    /**
     * @var bool
     */
    private $isGiftWrapping;

    /**
     * @var string|null
     */
    private $carrierModuleInfo;

    /**
     * @var string|null
     */
    private $giftMessage;

    /**
     * @param OrderCarrierForViewing[] $carriers
     * @param bool $isRecycledPackaging
     * @param bool $isGiftWrapping
     * @param string|null $giftMessage
     * @param string|null $carrierModuleInfo
     */
    public function __construct(
        array $carriers,
        bool $isRecycledPackaging,
        bool $isGiftWrapping,
        ?string $giftMessage,
        ?string $carrierModuleInfo
    ) {
        foreach ($carriers as $carrier) {
            $this->addCarrier($carrier);
        }

        $this->isRecycledPackaging = $isRecycledPackaging;
        $this->isGiftWrapping = $isGiftWrapping;
        $this->carrierModuleInfo = $carrierModuleInfo;
        $this->giftMessage = $giftMessage;
    }

    /**
     * hint - collection of OrderCarrierForViewing objects would be better
     *
     * @return OrderCarrierForViewing[]
     */
    public function getCarriers(): array
    {
        return $this->carriers;
    }

    /**
     * @return bool
     */
    public function isRecycledPackaging(): bool
    {
        return $this->isRecycledPackaging;
    }

    /**
     * @return bool
     */
    public function isGiftWrapping(): bool
    {
        return $this->isGiftWrapping;
    }

    /**
     * @return string|null
     */
    public function getCarrierModuleInfo(): ?string
    {
        return $this->carrierModuleInfo;
    }

    /**
     * @return string|null
     */
    public function getGiftMessage(): ?string
    {
        return $this->giftMessage;
    }

    /**
     * @param OrderCarrierForViewing $carrier
     */
    private function addCarrier(OrderCarrierForViewing $carrier): void
    {
        $this->carriers[] = $carrier;
    }
}
