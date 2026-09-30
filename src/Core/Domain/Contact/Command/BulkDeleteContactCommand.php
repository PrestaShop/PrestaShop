<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;

class BulkDeleteContactCommand
{
    /**
     * @var ContactId[]
     */
    private $contactIds;

    /**
     * @param int[] $contactIds
     */
    public function __construct(array $contactIds)
    {
        foreach ($contactIds as $contactId) {
            $this->contactIds[] = new ContactId($contactId);
        }
    }

    /**
     * @return ContactId[]
     */
    public function getContactIds(): array
    {
        return $this->contactIds;
    }
}
