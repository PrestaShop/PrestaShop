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
