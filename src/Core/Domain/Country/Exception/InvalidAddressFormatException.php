<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

use Throwable;

/**
 * Thrown when a country's address format is rejected by AddressFormatChecker.
 * Carries the list of (already translated) error messages so the Admin API or
 * any non-form CQRS consumer can surface them.
 */
final class InvalidAddressFormatException extends CountryConstraintException
{
    /** @var string[] */
    private array $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message !== '' ? $message : 'Invalid address format', $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
