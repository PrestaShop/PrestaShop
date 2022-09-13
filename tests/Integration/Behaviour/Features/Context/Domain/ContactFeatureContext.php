<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
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
}
