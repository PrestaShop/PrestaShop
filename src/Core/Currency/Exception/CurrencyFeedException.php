<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Currency\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when an error occurred while fetching a currency exchange rate (network issue, invalid response, ...)
 *
 * @see ExchangeRateProvider
 */
class CurrencyFeedException extends CoreException
{
}
