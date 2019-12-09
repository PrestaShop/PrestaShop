<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use PrestaShopException;
use Shop;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given single shop context is loaded
     *
     * @throws PrestaShopException
     */
    public function singleleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_SHOP);
    }

    /**
     * @Given multiple shop context is loaded
     *
     * @throws PrestaShopException
     */
    public function multipleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    /**
     * @When I close :arg1 showcase card
     */
    public function iCloseShowcaseCard($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then :arg1 showcase card should be closed
     */
    public function showcaseCardShouldBeClosed($arg1)
    {
        throw new PendingException();
    }
}
