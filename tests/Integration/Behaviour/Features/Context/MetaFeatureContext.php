<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Meta;
use RuntimeException;

class MetaFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Then /^meta "([^"]*)" page should be "([^"]*)"$/
     */
    public function assertMetaPageShouldBe($reference, $expectedPageName)
    {
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->page !== $expectedPageName) {
            throw new RuntimeException(
                sprintf('Expected page name "%s" did not matched given %s', $expectedPageName, $meta->page)
            );
        }
    }

    /**
     * @Given /^meta "([^"]*)" page title for default language should be "([^"]*)"$/
     */
    public function AssertMetaPageTitleForDefaultLanguageShouldBe($reference, $expectedTitle)
    {
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->title[$defaultLanguageId] !== $expectedTitle) {
            throw new RuntimeException(
                sprintf(
                    'Expected title "%s" did not matched given %s for language %s',
                    $expectedTitle,
                    $meta->title[$defaultLanguageId],
                    $defaultLanguageId
                )
            );
        }
    }

    /**
     * @Given /^meta "([^"]*)" field "([^"]*)" for default language should be "([^"]*)"$/
     */
    public function metaFieldForDefaultLanguageShouldBe($reference, $field, $expectedValue)
    {
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');
        /** @var Meta $meta */
        $meta = SharedStorage::getStorage()->get($reference);

        if ($meta->{$field}[$defaultLanguageId] !== $expectedValue) {
            throw new RuntimeException(
                sprintf(
                    'Expected value "%s" did not matched given %s for language %s',
                    $expectedValue,
                    $meta->{$field}[$defaultLanguageId],
                    $defaultLanguageId
                )
            );
        }
    }
}
