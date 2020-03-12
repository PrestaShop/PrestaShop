<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageFeatureContext
 */
class LanguageFeatureContext extends AbstractDomainFeatureContext
{
    private const SHOP_ASSOCIATION = ['TODO'];

    /** @var ContainerInterface */
    private $container;

    public function __construct()
    {
        $this->container = $this->getContainer();
    }

    /**
     * @When I add new language :languageReference with following details:
     *
     * @param $languageReference
     * @param TableNode $table
     */
    public function addNewLanguageWithFollowingDetails($languageReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        $this->getCommandBus()->handle(new AddLanguageCommand(
            $testCaseData['Name'],
            $testCaseData['ISO code'],
            $testCaseData['Language code'],
            $testCaseData['Date format'],
            $testCaseData['Date format (full)'],
            $testCaseData['Flag'],
            $testCaseData['"No-picture" image'],
            $testCaseData['Is RTL language'],
            $testCaseData['Status'],
            self::SHOP_ASSOCIATION
        ));
    }

    /**
     * @Then I should be able to see :languageReference language edit form with following details:
     *
     * @param $languageReference
     * @param TableNode $table
     */
    public function thereIsLanguageWithFollowingDetails($languageReference, TableNode $table)
    {
        throw new PendingException();
    }
}
