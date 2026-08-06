<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

/**
 * Contains a valid international call prefix for a country.
 */
class CallPrefix
{
    /**
     * @var int
     */
    protected $callPrefix;

    /**
     * @throws CountryConstraintException
     */
    public function __construct(int $callPrefix)
    {
        if ($callPrefix < 0) {
            throw new CountryConstraintException(
                sprintf('Invalid country call prefix: %d', $callPrefix),
                CountryConstraintException::INVALID_CALL_PREFIX
            );
        }

        $this->callPrefix = $callPrefix;
    }

    public function getValue(): int
    {
        return $this->callPrefix;
    }
}
