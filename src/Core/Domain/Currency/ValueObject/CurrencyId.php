<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;

/**
 * Class CurrencyId is responsible for providing currency id data.
 */
class CurrencyId implements CurrencyIdInterface
{
    /**
     * @var int
     */
    private $currencyId;

    /**
     * @param int $currencyId
     *
     * @throws CurrencyException
     */
    public function __construct(int $currencyId)
    {
        if ($currencyId <= 0) {
            throw new CurrencyConstraintException(
                sprintf('Invalid Currency id: %d', $currencyId),
                CurrencyConstraintException::INVALID_ID
            );
        }

        $this->currencyId = $currencyId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->currencyId;
    }
}
