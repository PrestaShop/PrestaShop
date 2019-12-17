<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When employee :employee closes showcase card :cardName
     *
     * @param string $cardName
     * @param string $employeeReference
     */
    public function employeeClosesShowcaseCard(string $cardName, string $employeeReference)
    {
        $employeeId = SharedStorage::getStorage()->get($employeeReference);
        $this->getCommandBus()->handle(new CloseShowcaseCardCommand($employeeId, $cardName));
    }

    /**
     * @Then employee :employeeReference should not see showcase card :cardName
     *
     * @param string $employeeReference
     * @param string $cardName
     */
    public function employeeShouldNotSeeShowcaseCard(string $employeeReference, string $cardName)
    {
        $employeeId = SharedStorage::getStorage()->get($employeeReference);

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $employeeId, $cardName)
        );
        PHPUnit_Framework_Assert::assertTrue($showcaseCardIsClosed);
    }
}
