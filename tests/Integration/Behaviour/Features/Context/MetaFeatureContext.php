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
}
