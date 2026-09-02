<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;

class DeleteContactCommand
{
    /**
     * @var ContactId
     */
    private $contactId;

    public function __construct(
        int $contactId
    ) {
        $this->contactId = new ContactId($contactId);
    }

    /**
     * @return ContactId
     */
    public function getContactId(): ContactId
    {
        return $this->contactId;
    }
}
