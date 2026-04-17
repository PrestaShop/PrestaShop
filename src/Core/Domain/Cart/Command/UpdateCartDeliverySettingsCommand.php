<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

class UpdateCartDeliverySettingsCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var bool
     */
    private $allowFreeShipping;

    /**
     * @var bool
     */
    private $isAGift;

    /**
     * @var bool
     */
    private $useRecycledPackaging;

    /**
     * @var string
     */
    private $giftMessage;

    /**
     * @param int $cartId
     * @param bool $allowFreeShipping
     * @param bool|null $isAGift
     * @param bool|null $useRecycledPackaging
     * @param string|null $giftMessage
     */
    public function __construct(
        int $cartId,
        bool $allowFreeShipping,
        ?bool $isAGift = null,
        ?bool $useRecycledPackaging = null,
        ?string $giftMessage = null
    ) {
        $this->cartId = new CartId($cartId);
        $this->allowFreeShipping = $allowFreeShipping;
        $this->isAGift = $isAGift;
        $this->useRecycledPackaging = $useRecycledPackaging;
        $this->giftMessage = $giftMessage;
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return bool
     */
    public function allowFreeShipping(): bool
    {
        return $this->allowFreeShipping;
    }

    /**
     * @return bool|null
     */
    public function isAGift(): ?bool
    {
        return $this->isAGift;
    }

    /**
     * @return bool|null
     */
    public function useRecycledPackaging(): ?bool
    {
        return $this->useRecycledPackaging;
    }

    /**
     * @return string|null
     */
    public function getGiftMessage(): ?string
    {
        return $this->giftMessage;
    }
}
