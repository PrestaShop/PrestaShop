<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_AssertionFailedError;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\InvalidShowcaseCardNameException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\ShowcaseCardException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShopException;
use Shop;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given single shop context is loaded
     *
     * @throws PrestaShopException
     */
    public function singleShopContextIsLoaded()
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
     * @When I close :cardName showcase card as employee with id :employeeId
     *
     * @param string $cardName
     * @param int $employeeId
     *
     * @throws InvalidShowcaseCardNameException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws ShowcaseCardException
     */
    public function iCloseShowcaseCardAsEmployeeWithId(string $cardName, int $employeeId)
    {
        $this->getCommandBus()->handle(new CloseShowcaseCardCommand($employeeId, $cardName));
    }

    /**
     * @Then showcase card :cardName for employee with id :employeeId should be closed
     *
     * @param string $cardName
     * @param int $employeeId
     *
     * @throws InvalidShowcaseCardNameException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws ShowcaseCardException
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function showcaseCardForEmployeeWithIdShouldBeClosed(string $cardName, int $employeeId)
    {
        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $employeeId, $cardName)
        );
        assertTrue($showcaseCardIsClosed);
    }
}
