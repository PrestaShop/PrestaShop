<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use Behat\Behat\Tester\Exception\PendingException;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given :arg1 shop context is loaded
     */
    public function shopContextIsLoaded($arg1)
    {
        throw new PendingException();
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
