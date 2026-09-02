<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

use Exception;

/**
 * Is thrown when adding/editing customer with missing required fields
 */
class MissingCustomerRequiredFieldsException extends CustomerException
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
