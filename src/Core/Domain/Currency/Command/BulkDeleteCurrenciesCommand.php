<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Deletes given currencies
 */
class BulkDeleteCurrenciesCommand
{
    /**
     * @var CurrencyId[]
     */
    private $currencyIds = [];

    /**
     * @param int[] $currencyIds
     */
    public function __construct(array $currencyIds)
    {
        $this->setCurrencyIds($currencyIds);
    }

    /**
     * @return CurrencyId[]
     */
    public function getCurrencyIds()
    {
        return $this->currencyIds;
    }

    /**
     * @param int[] $currencyIds
     */
    private function setCurrencyIds(array $currencyIds)
    {
        if (empty($currencyIds)) {
            throw new CurrencyConstraintException('At least one currency must be provided for deleting', CurrencyConstraintException::EMPTY_BULK_DELETE);
        }

        foreach ($currencyIds as $currencyId) {
            $this->currencyIds[] = new CurrencyId($currencyId);
        }
    }
}
