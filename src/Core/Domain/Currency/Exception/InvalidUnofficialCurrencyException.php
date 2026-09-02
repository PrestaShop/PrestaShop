<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Exception;

use Throwable;

/**
 * Class InvalidUnofficialCurrencyException is thrown when an invalid currency
 * is being added (matching an ISO code from CLDR database)
 */
class InvalidUnofficialCurrencyException extends CurrencyException
{
    /** @var string */
    private $isoCode;

    /**
     * @param string $message the Exception message to throw
     * @param string $isoCode Invalid currency ISO code
     * @param int $code [optional] The Exception code
     * @param Throwable $previous [optional] The previous throwable used for the exception chaining
     */
    public function __construct($message, $isoCode, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->isoCode = $isoCode;
    }

    /**
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }
}
