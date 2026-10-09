<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Contact\Repository;

use Contact;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\CannotDeleteContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Methods to access data storage for ContactValue
 */
class ContactRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
    ) {
    }

    public function delete(ContactId $contactId): void
    {
        $this->deleteObjectModel($this->get($contactId), CannotDeleteContactException::class);
    }

    public function get(ContactId $contactId): Contact
    {
        /** @var Contact $contact */
        $contact = $this->getObjectModel(
            $contactId->getValue(),
            Contact::class,
            ContactNotFoundException::class
        );

        return $contact;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByLangId($langId)
    {
        return Contact::getContacts($langId);
    }
}
