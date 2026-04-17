<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

/**
 * Indicates that no currency was specified
 */
class NoCurrencyId implements CurrencyIdInterface
{
    /**
     * Value when no currency is specified
     */
    public const NO_CURRENCY_ID = 0;

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return self::NO_CURRENCY_ID;
    }
}
