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
 * Enables/disables currencies status
 */
class BulkToggleCurrenciesStatusCommand
{
    /**
     * @var CurrencyId[]
     */
    private $currencyIds = [];

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int[] $currencyIds
     * @param bool $expectedStatus
     */
    public function __construct(array $currencyIds, bool $expectedStatus)
    {
        $this->setCurrencies($currencyIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return CurrencyId[]
     */
    public function getCurrencyIds()
    {
        return $this->currencyIds;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @param int[] $currencyIds
     */
    private function setCurrencies(array $currencyIds)
    {
        if (empty($currencyIds)) {
            throw new CurrencyConstraintException('Currencies must be provided in order to toggle their status', CurrencyConstraintException::EMPTY_BULK_TOGGLE);
        }

        foreach ($currencyIds as $currencyId) {
            $this->currencyIds[] = new CurrencyId($currencyId);
        }
    }
}
