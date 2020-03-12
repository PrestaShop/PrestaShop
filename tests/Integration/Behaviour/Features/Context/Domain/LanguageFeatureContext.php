<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageFeatureContext
 */
class LanguageFeatureContext extends AbstractDomainFeatureContext
{
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

        throw new PendingException();
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
