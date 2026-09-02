<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Exception;

use Exception;

/**
 * Is thrown when adding/editing order state with missing required fields
 */
class MissingOrderStateRequiredFieldsException extends OrderStateException
{
    /**
     * @var string[]
     */
    private $missingRequiredFields;

    /**
     * @param string[] $missingRequiredFields
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $missingRequiredFields, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->missingRequiredFields = $missingRequiredFields;
    }

    /**
     * @return string[]
     */
    public function getMissingRequiredFields()
    {
        return $this->missingRequiredFields;
    }
}
