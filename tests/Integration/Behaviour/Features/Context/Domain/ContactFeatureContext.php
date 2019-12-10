<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use Behat\Behat\Tester\Exception\PendingException;

class ContactFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given there is no contact with id :arg1
     */
    public function thereIsNoContactWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given the last contact is with id :arg1
     */
    public function theLastContactIsWithId($arg1)
    {
        throw new PendingException();
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
