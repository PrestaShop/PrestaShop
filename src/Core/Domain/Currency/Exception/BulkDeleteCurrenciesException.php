<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Exception;

use Currency;
use Exception;

/**
 * Exception is thrown on currencies bulk delete failure
 */
class BulkDeleteCurrenciesException extends CurrencyException
{
    /**
     * @var int[]
     */
    private $currenciesIds;

    /**
     * @param int[] $currenciesIds
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(array $currenciesIds, $message = '', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->currenciesIds = $currenciesIds;
    }

    /**
     * @return int[]
     */
    public function getCurrenciesIds(): array
    {
        return $this->currenciesIds;
    }

    /**
     * @return string[]
     */
    public function getCurrenciesNames(): array
    {
        $names = [];
        foreach ($this->getCurrenciesIds() as $id) {
            $names[] = (new Currency((int) $id))->getName();
        }

        return $names;
    }
}
