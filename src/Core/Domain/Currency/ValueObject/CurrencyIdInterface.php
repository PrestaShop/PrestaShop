<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

/**
 * Defines contract for currency identification value.
 * This interface allows to explicitly define whether the currency relation is optional or required.
 *
 * @see CurrencyId
 * @see NoCurrencyId
 */
interface CurrencyIdInterface
{
    /**
     * @return int
     */
    public function getValue(): int;
}
