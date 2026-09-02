<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds data for cart shipping information
 */
class CartShipping
{
    /**
     * @var ?int
     */
    private $selectedCarrierId;

    /**
     * @var string
     */
    private $shippingPrice;

    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var CartDeliveryOption[]
     */
    private $deliveryOptions;

    /**
     * @var bool
     */
    private $isRecycledPackaging;

    /**
     * @var bool
     */
    private $isGift;

    /**
     * @var string
     */
    private $giftMessage;

    /**
     * @var bool
     */
    private $isVirtual;

    /**
     * @param string $shippingPrice
     * @param bool $freeShipping
     * @param CartDeliveryOption[] $deliveryOptions
     * @param int|null $selectedCarrierId
     * @param bool $isGift
     * @param bool $isRecycledPackaging
     * @param string $giftMessage
     * @param bool $isVirtual
     */
    public function __construct(
        string $shippingPrice,
        bool $freeShipping,
        array $deliveryOptions,
        ?int $selectedCarrierId,
        bool $isGift,
        bool $isRecycledPackaging,
        string $giftMessage,
        bool $isVirtual
    ) {
        $this->shippingPrice = $shippingPrice;
        $this->freeShipping = $freeShipping;
        $this->deliveryOptions = $deliveryOptions;
        $this->selectedCarrierId = $selectedCarrierId;
        $this->isGift = $isGift;
        $this->isRecycledPackaging = $isRecycledPackaging;
        $this->giftMessage = $giftMessage;
        $this->isVirtual = $isVirtual;
    }

    /**
     * @return string
     */
    public function getShippingPrice(): string
    {
        return $this->shippingPrice;
    }

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @return CartDeliveryOption[]
     */
    public function getDeliveryOptions(): array
    {
        return $this->deliveryOptions;
    }

    /**
     * @return mixed
     */
    public function getSelectedCarrierId()
    {
        return $this->selectedCarrierId;
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
    public function isGift(): bool
    {
        return $this->isGift;
    }

    /**
     * @return string
     */
    public function getGiftMessage(): string
    {
        return $this->giftMessage;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
