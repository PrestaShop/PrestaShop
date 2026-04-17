<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;
use Configuration;
use Currency;
use InvalidArgumentException;

/**
 * Currency related transformations
 */
class CurrencyTransformContext implements Context
{
    /**
     * Transforms currency iso code to instance
     *
     * @Transform /^currency "([^"]+)"$/
     */
    public function transformIsoToCurrency(string $currencyIso)
    {
        $currency = new Currency(Currency::getIdByIsoCode($currencyIso, (int) Configuration::get('PS_SHOP_DEFAULT')));

        if (!$currency->id) {
            throw new InvalidArgumentException(sprintf('Currency not found by iso code "%s"', $currencyIso));
        }

        return $currency;
    }
}
