<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use RuntimeException;
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
     * @Given there is contact with id :contactId
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function thereIsContactIsWithId(int $contactId)
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
        $this->getCommandBus()->handle(new AddContactCommand(
                [$this->defaultLangId => $title], true)
        );
    }

    /**
     * @Then I should be able to get contact with id :contactId for editing
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function iShouldBeAbleToGetContactWithIdForEditing(int $contactId)
    {
        $this->getQueryBus()->handle(new GetContactForEditing($contactId));
    }

    /**
     * @Then contact with id :contactId should have title :title
     *
     * @param string $title
     * @param int $contactId
     *
     * @throws ContactException
     * @throws RuntimeException
     */
    public function contactWithIdShouldHaveTitle(string $title, int $contactId)
    {
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(new GetContactForEditing($contactId));
        /** @var string[] $localisedTitles */
        $localisedTitles = $editableContact->getLocalisedTitles();
        foreach ($localisedTitles as $localisedTitle) {
            if ($title == $localisedTitle) {
                return;
            }
        }
        throw new RuntimeException(
            sprintf(
                'No localized title was found for title "%s", instead received %s',
                $title,
                implode(',',$localisedTitles)
            )
        );
    }

    /**
     * @Then contact with id :contactId should have messages saving enabled
     *
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function contactWithIdShouldHaveMessagesSavingEnabled(int $contactId)
    {
        /** @var EditableContact $editableContact */
        $editableContact = $this->getQueryBus()->handle(new GetContactForEditing($contactId));
        PHPUnit_Framework_Assert::assertSame(
            1,
            (int) $editableContact->isMessagesSavingEnabled(),
            'Message saving is disabled'
        );
    }
}
