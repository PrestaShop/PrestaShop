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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Query;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;

/**
 * Get displayable currency data from database, this can be customized official currencies or even
 * custom ones. The result is exposed with a DisplayableCurrency object, and if the currency doesn't
 * exist a CurrencyNotFoundException is thrown.
 */
class GetDisplayableCurrency
{
    /**
     * @var AlphaIsoCode
     */
    private $isoCode;

    /**
     * @param string $isoCode
     *
     * @throws CurrencyException
     */
    public function __construct(string $isoCode)
    {
        $this->isoCode = new AlphaIsoCode($isoCode);
    }

    /**
     * @return AlphaIsoCode
     */
    public function getIsoCode(): AlphaIsoCode
    {
        return $this->isoCode;
    }
}
