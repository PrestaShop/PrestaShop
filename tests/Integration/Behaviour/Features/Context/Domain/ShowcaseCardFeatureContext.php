<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I close :cardName showcase card as employee :employeeId
     *
     * @param string $cardName
     * @param int $employeeId
     */
    public function closeShowcaseCardAsEmployee(string $cardName, int $employeeId)
    {
        $this->getCommandBus()->handle(new CloseShowcaseCardCommand($employeeId, $cardName));
    }

    /**
     * @Then showcase card :cardName for employee :employeeId should be closed
     *
     * @param string $cardName
     * @param int $employeeId
     */
    public function showcaseCardForEmployeeShouldBeClosed(string $cardName, int $employeeId)
    {
        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $employeeId, $cardName)
        );
        PHPUnit_Framework_Assert::assertTrue($showcaseCardIsClosed);
    }
}
