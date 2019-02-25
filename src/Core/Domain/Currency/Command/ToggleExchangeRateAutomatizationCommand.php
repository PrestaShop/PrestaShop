<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class ToggleExchangeRateAutomatizationCommand is responsible for turning on or off the setting - if its on then
 * in CronJobs module it creates new record with url which points to the script which is being executed at certain time
 * of period. If the setting is off then it removes that record.
 */
class ToggleExchangeRateAutomatizationCommand
{
    /**
     * @var bool
     */
    private $exchangeRateStatus;

    /**
     * @param bool $exchangeRateStatus
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($exchangeRateStatus)
    {
        $this->assertIsBool($exchangeRateStatus);

        $this->exchangeRateStatus = $exchangeRateStatus;
    }

    /**
     * @return bool
     */
    public function exchangeRateStatus()
    {
        return $this->exchangeRateStatus;
    }

    /**
     * @param bool $isExchangeRateEnabled
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsBool($isExchangeRateEnabled)
    {
        if (!is_bool($isExchangeRateEnabled)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Unexpected type of live exchange rates. Expected bool, got "%s"',
                    var_export($isExchangeRateEnabled, true)
                ),
                CurrencyConstraintException::INVALID_LIVE_EXCHANGE_RATES
            );
        }
    }
}
