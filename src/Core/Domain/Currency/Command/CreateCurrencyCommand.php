<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

/**
 * Class CreateCurrencyCommand
 */
class CreateCurrencyCommand
{
    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var float
     */
    private $exchangeRate;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param string $isoCode
     * @param float $exchangeRate
     * @param bool $isEnabled
     */
    public function __construct($isoCode, $exchangeRate, $isEnabled)
    {
        //todo: iso code as object and add validation inside
        $this->isoCode = $isoCode;
        $this->exchangeRate = $exchangeRate;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     *
     * @return self
     */
    public function setShopIds($shopIds)
    {
        $this->shopIds = $shopIds;

        return $this;
    }
}
