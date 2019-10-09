<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;

class CartShipping
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
    private $shippingPrice;
    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @param int $carrierId
     * @param string $carrierName
     * @param string $carrierDelay
     * @param string $shippingPrice
     * @param bool $freeShipping
     */
    public function __construct(
        int $carrierId,
        string $carrierName,
        string $carrierDelay,
        string $shippingPrice,
        bool $freeShipping
    ) {
        $this->carrierId = $carrierId;
        $this->carrierName = $carrierName;
        $this->carrierDelay = $carrierDelay;
        $this->shippingPrice = $shippingPrice;
        $this->freeShipping = $freeShipping;
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
}
