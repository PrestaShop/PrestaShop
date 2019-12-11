<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use Behat\Behat\Tester\Exception\PendingException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class ContactFeatureContext extends AbstractDomainFeatureContext
{
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
     * @Given the last contact is with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function theLastContactIsWithId(int $contactId)
    {
        $this->getQueryBus()->handle(new GetContactForEditing($contactId));
    }

    /**
     * @When I add new contact with title :title and messages saving is enabled
     *
     * @param string $title
     *
     * @throws ContactConstraintException
     */
    public function iAddNewContactWithTitleAndMessagesSavingIsEnabled(string $title)
    {
        $this->getQueryBus()->handle(new AddContactCommand([$this->defaultLangId => $title], true));
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
