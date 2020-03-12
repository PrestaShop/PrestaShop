<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;

class LanguageFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I select language :language and press add button
     *
     * @param string $language
     */
    public function addLanguage(string $language)
    {
        throw new PendingException();
    }

    /**
     * @Then I should be able to modify :language translations
     *
     * @param string $language
     */
    public function getLanguageForEditing(string $language)
    {
        throw new PendingException();
    }
}
