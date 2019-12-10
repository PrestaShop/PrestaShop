<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use Behat\Behat\Tester\Exception\PendingException;
use Contact;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;

class ContactFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given there is no contact with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function thereIsNoContactWithId(int $contactId)
    {
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(
            new GetContactForEditing($contactId)
        );

    }

    /**
     * @Given the last contact is with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function theLastContactIsWithId(int $contactId)
    {
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(
            new GetContactForEditing($contactId)
        );
    }

    /**
     * @When I add new contact with title :arg1 and messages saving is enabled
     */
    public function iAddNewContactWithTitleAndMessagesSavingIsEnabled($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I should be able to get contact with id :arg1 for editing
     */
    public function iShouldBeAbleToGetContactWithIdForEditing($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then contact with id :arg2 should have title :arg1
     */
    public function contactWithIdShouldHaveTitle($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then contact with id :arg1 should have messages saving disabled
     */
    public function contactWithIdShouldHaveMessagesSavingDisabled($arg1)
    {
        throw new PendingException();
    }

}
