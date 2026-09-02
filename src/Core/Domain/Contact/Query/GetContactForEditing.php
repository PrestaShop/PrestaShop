<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Query;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;

/**
 * Class GetContactForEditing is responsible for getting the data related with contact entity.
 */
class GetContactForEditing
{
    /** @var ContactId */
    private $contactId;

    /**
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function __construct($contactId)
    {
        $this->contactId = new ContactId($contactId);
    }

    /**
     * @return ContactId
     */
    public function getContactId()
    {
        return $this->contactId;
    }
}
