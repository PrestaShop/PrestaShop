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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Exception;

/**
 * Is thrown when action associated with currency is done from multi-shop context
 * and that action cannot be done because the currency is set as default in one of shops
 */
class DefaultCurrencyInMultiShopException extends CurrencyException
{
    /**
     * When trying to remove default currency in multi-shop context
     */
    public const CANNOT_REMOVE_CURRENCY = 1;

    /**
     * When trying to disable default currency in multi-shop context
     */
    public const CANNOT_DISABLE_CURRENCY = 2;

    /**
     * @var string
     */
    private $currencyName;

    /**
     * @var string
     */
    private $shopName;

    /**
     * @param string $currencyName
     * @param string $shopName
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($currencyName, $shopName, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->currencyName = $currencyName;
        $this->shopName = $shopName;
    }

    /**
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }
}
