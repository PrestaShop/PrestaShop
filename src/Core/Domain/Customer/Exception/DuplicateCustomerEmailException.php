<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Exception is thrown when email which already exists is being used to create or update other customer
 */
class DuplicateCustomerEmailException extends CustomerException
{
    /**
     * @var int Code is used when the check fails during adding the customer
     */
    public const ADD = 1;

    /**
     * @var int Code is used when the check fails during editing the customer
     */
    public const EDIT = 2;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param Email $email
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct(Email $email, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
