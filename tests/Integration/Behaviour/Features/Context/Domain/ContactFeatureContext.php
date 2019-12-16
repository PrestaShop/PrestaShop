<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ContactFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_LOCALE_ID = 1; // EN locale
    private const DUMMY_CONTACT_ID = 1;

    /**
     * @var int
     */
    private $defaultLangId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
    }

    /**
     * @When I add new contact with the following details:
     *
     * @param TableNode $table
     */
    public function addNewContactWithTheFollowingDetails(TableNode $table)
    {
        $data = $table->getRowsHash();
        /** @var EditableContact $editablContact */
        $editableContact = $this->mapToEditableContact(self::DUMMY_CONTACT_ID, $data);

        $addContactCommand = new AddContactCommand(
            $editableContact->getLocalisedTitles(),
            $editableContact->isMessagesSavingEnabled()
        );
        $addContactCommand->setEmail($editableContact->getEmail()->getValue())
                          ->setLocalisedDescription($editableContact->getLocalisedDescription())
                          ->setShopAssociation($editableContact->getShopAssociation());

        $this->getCommandBus()->handle($addContactCommand);
    }

    /**
     * @When contact :contactId should have the following details:
     *
     * @param int $contactId
     * @param TableNode $table
     */
    public function contactShouldHaveTheFollowingDetails(int $contactId, TableNode $table)
    {
        $data = $table->getRowsHash();
        $expectedEditableContact = $this->mapToEditableContact($contactId, $data);
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(new GetContactForEditing($contactId));
        PHPUnit_Framework_Assert::assertEquals($expectedEditableContact, $editableContact);
    }

    /**
     * @When I update contact :contactId with the following details:
     *
     * @param int $contactId
     * @param TableNode $table
     */
    public function updateContactWithTheFollowingDetails(int $contactId, TableNode $table)
    {
        $data = $table->getRowsHash();
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
        $title = $data['title'];
        $emailAddress = $data['email_address'];
        $isMessageSavingEnabled = PrimitiveUtils::castStringBooleanIntoBoolean($data['is_message_saving_enabled']);
        $description = $data['description'];
        $shopIdAssociation = (int) $data['shop_id_association'];

        return new EditableContact(
            $contactId,
            [self::DEFAULT_LOCALE_ID => $title],
            $emailAddress,
            $isMessageSavingEnabled,
            [self::DEFAULT_LOCALE_ID => $description],
            [$shopIdAssociation]
        );
    }
}
