<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;

/**
 * Class ContactId
 */
class ContactId
{
    /**
     * @var int
     */
    private $contactId;

    /**
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function __construct($contactId)
    {
        $this->assertIsIntegerOrMoreThanZero($contactId);

        $this->contactId = $contactId;
    }

    /**
     * @param int $contactId
     *
     * @throws ContactException
     */
    private function assertIsIntegerOrMoreThanZero($contactId)
    {
        if (!is_int($contactId) || 0 >= $contactId) {
            throw new ContactException(sprintf('Invalid Contact id: %s', var_export($contactId, true)));
        }
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->contactId;
    }
}
