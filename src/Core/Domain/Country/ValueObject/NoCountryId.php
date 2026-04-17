<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

/**
 * Provides country id value
 */
class NoCountryId implements CountryIdInterface
{
    /**
     * @var int
     */
    public const NO_COUNTRY_ID_VALUE = 0;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return self::NO_COUNTRY_ID_VALUE;
    }
}
