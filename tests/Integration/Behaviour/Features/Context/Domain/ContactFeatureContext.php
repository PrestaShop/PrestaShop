<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\BulkDeleteContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\DeleteContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ContactFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_LOCALE_ID = 1; // EN locale
    private const DUMMY_CONTACT_ID = 1;

    /**
     * @When I add new contact :reference with the following details:
     *
     * @param TableNode $table
     * @param string $reference
     */
    public function addNewContactWithTheFollowingDetails(TableNode $table, string $reference)
    {
        $data = $table->getRowsHash();
        /** @var EditableContact $editableContact */
        $editableContact = $this->mapToEditableContact(self::DUMMY_CONTACT_ID, $data);

        $addContactCommand = new AddContactCommand(
            $editableContact->getLocalisedTitles(),
            $editableContact->isMessagesSavingEnabled()
        );
        $addContactCommand->setEmail($editableContact->getEmail()->getValue())
            ->setLocalisedDescription($editableContact->getLocalisedDescription())
            ->setShopAssociation($editableContact->getShopAssociation());

        /** @var ContactId $contactId */
        $contactId = $this->getCommandBus()->handle($addContactCommand);
        SharedStorage::getStorage()->set($reference, $contactId);
    }

    /**
     * @When contact :reference should have the following details:
     *
     * @param string $reference
     * @param TableNode $table
     */
    public function contactShouldHaveTheFollowingDetails(string $reference, TableNode $table)
    {
        $data = $table->getRowsHash();

        /** @var ContactId $contactIdObject */
        $contactIdObject = SharedStorage::getStorage()->get($reference);
        $contactId = $contactIdObject->getValue();
        $expectedEditableContact = $this->mapToEditableContact($contactId, $data);

        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(new GetContactForEditing($contactId));

        Assert::assertEquals($expectedEditableContact, $editableContact);
    }

    /**
     * @When I update contact :contactId with the following details:
     *
     * @param string $reference
     * @param TableNode $table
     */
    public function updateContactWithTheFollowingDetails(string $reference, TableNode $table)
    {
        $data = $table->getRowsHash();

        /** @var ContactId $contactIdObject */
        $contactIdObject = SharedStorage::getStorage()->get($reference);
        $contactId = $contactIdObject->getValue();

        $editableContact = $this->mapToEditableContact($contactId, $data);

        $editContactCommand = new EditContactCommand($contactId);
        $editContactCommand->setLocalisedTitles($editableContact->getLocalisedTitles());
        $editContactCommand->setShopAssociation($editableContact->getShopAssociation());
        $editContactCommand->setLocalisedDescription($editableContact->getLocalisedDescription());
        $editContactCommand->setEmail($editableContact->getEmail()->getValue());
        $editContactCommand->setIsMessagesSavingEnabled($editableContact->isMessagesSavingEnabled());

        $this->getCommandBus()->handle($editContactCommand);
    }

    /**
     * @param int $contactId
     * @param array $data
     *
     * @return EditableContact
     */
    private function mapToEditableContact(int $contactId, array $data): EditableContact
    {
        return new EditableContact(
            $contactId,
            [self::DEFAULT_LOCALE_ID => $data['title']],
            $data['email_address'],
            $isMessageSavingEnabled = PrimitiveUtils::castStringBooleanIntoBoolean($data['is_message_saving_enabled']),
            [self::DEFAULT_LOCALE_ID => $data['description']],
            [(int) $data['shop_id_association']]
        );
    }

    /**
     * @When I delete contact :reference
     */
    public function deleteContactUsingCommand(string $reference): void
    {
        /** @var ContactId $contactId */
        $contactId = $this->getSharedStorage()->get($reference);

        $this->getCommandBus()->handle(
            new DeleteContactCommand($contactId->getValue())
        );
    }

    /**
     * @When I bulk delete contacts :references
     */
    public function bulkDeleteContactsUsingCommand(string $references): void
    {
        $contactIds = [];

        /** @var ContactId $contactId */
        foreach ($this->referencesToIds($references) as $contactId) {
            $contactIds[] = $contactId->getValue();
        }
        $this->getCommandBus()->handle(new BulkDeleteContactCommand($contactIds));
    }

    /**
     * @When contact :reference should not exist
     */
    public function assertContactDoesNotExist(string $reference): void
    {
        $coughtException = null;

        try {
            $this->getContactForEditing($reference);
        } catch (ContactNotFoundException $e) {
            $coughtException = $e;
        }

        if (null === $coughtException) {
            throw new RuntimeException(sprintf('Contact %s doesn\'t exists', $reference));
        }
    }

    /**
     * @When contact :reference should exist
     */
    public function assertContactDoesExist(string $reference): void
    {
        $editableContact = $this->getContactForEditing($reference);

        /** @var ContactId $contactId */
        $contactId = $this->getSharedStorage()->get($reference);

        Assert::assertSame(
            $contactId->getValue(),
            $editableContact->getContactId()->getValue()
        );
    }

    private function getContactForEditing(string $reference): EditableContact
    {
        /** @var ContactId $contactId */
        $contactId = $this->getSharedStorage()->get($reference);

        return $this->getQueryBus()->handle(
            new GetContactForEditing($contactId->getValue())
        );
    }
}
