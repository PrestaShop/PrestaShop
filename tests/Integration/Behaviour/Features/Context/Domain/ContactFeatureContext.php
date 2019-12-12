<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShopBundle\Utils\BoolParser;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use function PrestaShopBundle\Utils\BoolParser;

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
     * @Given there is no contact with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function thereIsNoContactWithId(int $contactId)
    {
        try {
            $this->getQueryBus()->handle(new GetContactForEditing($contactId));
        } catch (ContactNotFoundException $exception) {
            return;
        }
    }

    /**
     * @Given there is contact with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function thereIsContactIsWithId(int $contactId)
    {
        $this->getQueryBus()->handle(new GetContactForEditing($contactId));
    }

    /**
     * @Then I should be able to get contact with id :contactId for editing
     *
     * @param int $contactId
     *
     * @throws ContactException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iShouldBeAbleToGetContactWithIdForEditing(int $contactId)
    {
        $this->getQueryBus()->handle(new GetContactForEditing($contactId));
    }

    /**
     * @When I add new contact with the following properties:
     *
     * @param TableNode $table
     *
     * @throws ContactConstraintException
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws DomainConstraintException
     * @throws ContactException
     */
    public function iAddNewContactWithTheFollowingProperties(TableNode $table)
    {
        $data = $this->extractFirstHorizontalRowFromProperties($table);
        /** @var EditableContact $editablContact */
        $editableContact = $this->mapToEditableContact(self::DUMMY_CONTACT_ID, $data);

        $addContactCommand = new AddContactCommand(
            $editableContact->getLocalisedTitles(), $editableContact->isMessagesSavingEnabled()
        );
        $addContactCommand->setEmail($editableContact->getEmail()->getValue());
        $addContactCommand->setLocalisedDescription($editableContact->getLocalisedDescription());
        $addContactCommand->setShopAssociation($editableContact->getShopAssociation());

        $this->getCommandBus()->handle($addContactCommand);
    }

    /**
     * @When contact with id :contactId should have the following properties:
     *
     * @param int $contactId
     * @param TableNode $table
     *
     * @throws ContactException
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws DomainConstraintException
     */
    public function contactWithIdShouldHaveTheFollowingProperties(int $contactId, TableNode $table)
    {
        $data = $this->extractFirstHorizontalRowFromProperties($table);
        $expectedEditableContact = $this->mapToEditableContact($contactId, $data);
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(new GetContactForEditing($contactId));
        PHPUnit_Framework_Assert::assertEquals($expectedEditableContact, $editableContact);
    }

    /**
     * @When I update contact with id :contactId with the following properties:
     *
     * @param int $contactId
     * @param TableNode $table
     *
     * @throws ContactConstraintException
     * @throws ContactException
     * @throws DomainConstraintException
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iUpdateContactWithIdWithTheFollowingProperties(int $contactId, TableNode $table)
    {
        $data = $this->extractFirstHorizontalRowFromProperties($table);
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
     *
     * @throws ContactException
     * @throws DomainConstraintException
     */
    private function mapToEditableContact(int $contactId, array $data): EditableContact
    {
        $title = $data['title'];
        $emailAddress = $data['email_address'];
        $isMessageSavingEnabled = BoolParser::castToBool($data['is_message_saving_enabled']);
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
