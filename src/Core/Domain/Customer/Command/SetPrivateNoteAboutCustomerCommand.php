<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Sets private note about customer that can only be seen in Back Office
 */
class SetPrivateNoteAboutCustomerCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var string
     */
    private $privateNote;

    /**
     * @param int $customerId
     * @param string $privateNote
     */
    public function __construct($customerId, $privateNote)
    {
        $this->assertPrivateNoteIsString($privateNote);

        $this->customerId = new CustomerId($customerId);
        $this->privateNote = $privateNote;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getPrivateNote()
    {
        return $this->privateNote;
    }

    /**
     * @param string $privateNote
     *
     * @throws CustomerConstraintException
     */
    private function assertPrivateNoteIsString($privateNote)
    {
        if (!is_string($privateNote)) {
            throw new CustomerConstraintException('Invalid private note provided. Private note must be a string.', CustomerConstraintException::INVALID_PRIVATE_NOTE);
        }
    }
}
