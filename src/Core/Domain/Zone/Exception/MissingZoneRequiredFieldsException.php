<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\Exception;

use Throwable;

/**
 * Is thrown when adding/editing zone with missing required fields
 */
class MissingZoneRequiredFieldsException extends ZoneException
{
    /**
     * @var string[]
     */
    private $missingRequiredFields;

    /**
     * @param array $missingRequiredFields
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $missingRequiredFields, $message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->missingRequiredFields = $missingRequiredFields;
    }

    /**
     * @return string[]
     */
    public function getMissingRequiredFields(): array
    {
        return $this->missingRequiredFields;
    }
}
