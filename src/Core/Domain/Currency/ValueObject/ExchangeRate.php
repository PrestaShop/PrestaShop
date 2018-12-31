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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class ExchangeRate
 */
class ExchangeRate
{
    const DEFAULT_RATE = 1;

    /**
     * @var float
     */
    private $exchangeRate;

    /**
     * @param float $exchangeRate
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($exchangeRate)
    {
        $this->assertIsFloatOrZero($exchangeRate);
        $this->exchangeRate = (float) $exchangeRate;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->exchangeRate;
    }

    /**
     * @param float $exchangeRate
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsFloatOrZero($exchangeRate)
    {
        if (!is_numeric($exchangeRate)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Exchange rate is not valid. Expected to be numeric but got %s',
                    var_export($exchangeRate, true)
                ),
                CurrencyConstraintException::INVALID_EXCHANGE_RATE_TYPE
            );
        }

        if (0 >= $exchangeRate) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Given exchange rate %s is not valid. It must be more then 0',
                    $exchangeRate
                ),
                CurrencyConstraintException::INVALID_EXCHANGE_RATE
            );
        }
    }
}
