<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\RemoveSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use SpecificPrice;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Resources\DatabaseDump;

class SpecificPricePrioritiesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @AfterFeature @restore-specific-prices-priorities-after-feature
     */
    public static function restoreSpecificPricesPrioritiesAfterFeature(): void
    {
        // Specific price priorities is store in configuration, so we restore it
        DatabaseDump::restoreTables([
            'configuration',
            'configuration_lang',
        ]);
        SpecificPrice::resetStaticCache();
    }

    /**
     * @see transformPriorityList
     *
     * @When I set following custom specific price priorities for product :productReference:
     *
     * @param string $productReference
     * @param PriorityList $priorityList
     */
    public function setPrioritiesForProduct(string $productReference, PriorityList $priorityList): void
    {
        try {
            $this->getCommandBus()->handle(new SetSpecificPricePriorityForProductCommand(
                $this->getSharedStorage()->get($productReference),
                $priorityList->getPriorities()
            ));
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove custom specific price priorities for product ":productReference"
     *
     * @param string $productReference
     */
    public function removePrioritiesForProduct(string $productReference): void
    {
        $this->getCommandBus()->handle(new RemoveSpecificPricePriorityForProductCommand(
            $this->getSharedStorage()->get($productReference)
        ));
    }

    /**
     * @see transformPriorityList
     *
     * @When I set following default specific price priorities:
     *
     * @param PriorityList $priorityList
     */
    public function setDefaultPriorities(PriorityList $priorityList): void
    {
        /** @var SpecificPricePriorityUpdater $priorityUpdater */
        $priorityUpdater = CommonFeatureContext::getContainer()
            ->get(SpecificPricePriorityUpdater::class)
        ;

        try {
            $priorityUpdater->updateDefaultPriorities($priorityList);
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @see transformPriorityList
     *
     * @Given default specific price priorities are set to following:
     * @Then default specific price priorities should be the following:
     *
     * @param PriorityList $priorityList
     */
    public function assertDefaultPriorities(PriorityList $priorityList): void
    {
        // checks legacy method and the newer one introduced in repository
        $this->assertPriorities($priorityList, $this->getUsablePriorities(null));
        $this->assertPriorities($priorityList, $this->getDefaultPriorities());
    }

    /**
     * @see transformPriorityList
     *
     * @Then product :productReference should have following custom specific price priorities:
     *
     * @param string $productReference
     * @param PriorityList $priorityList
     */
    public function assertProductPriorities(string $productReference, PriorityList $priorityList): void
    {
        $actualPriorities = $this->getProductForEditing($productReference)
            ->getPricesInformation()
            ->getSpecificPricePriorities();

        $this->assertPriorities($priorityList, $actualPriorities);
    }

    /**
     * @see transformPriorityList
     *
     * @Then following specific price priorities should be used for product :productReference:
     *
     * @param string $productReference
     * @param PriorityList $priorityList
     */
    public function assertUsablePriorities(string $productReference, PriorityList $priorityList): void
    {
        $this->assertPriorities($priorityList, $this->getUsablePriorities($productReference));
    }

    /**
     * @Then default specific price priorities should be used for product ":productReference"
     *
     * @param string $productReference
     */
    public function assertDefaultPrioritiesAreUsed(string $productReference): void
    {
        $this->assertPriorities(
            $this->getUsablePriorities(null),
            $this->getUsablePriorities($productReference)
        );

        // Makes sure that repository method is returning the same as legacy method
        $this->assertPriorities(
            $this->getDefaultPriorities(),
            $this->getUsablePriorities($productReference)
        );
    }

    /**
     * @Then product ":productReference" should not have custom specific price priorities
     *
     * @param string $productReference
     */
    public function assertProductHasNoCustomPriorities(string $productReference): void
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $priorities = $productForEditing->getPricesInformation()->getSpecificPricePriorities();

        Assert::assertNull($priorities);
    }

    /**
     * @Transform table:priorities
     *
     * @param TableNode $tableNode
     *
     * @return PriorityList
     */
    public function transformPriorityList(TableNode $tableNode): PriorityList
    {
        $prioritiesTable = $tableNode->getColumnsHash();
        $priorities = [];
        foreach ($prioritiesTable as $value) {
            $priorities[] = $value['priorities'];
        }

        return new PriorityList($priorities);
    }

    /**
     * @param PriorityList $expectedPriorities
     * @param PriorityList $actualPriorities
     */
    private function assertPriorities(PriorityList $expectedPriorities, PriorityList $actualPriorities)
    {
        Assert::assertEquals(
            $expectedPriorities,
            $actualPriorities,
            sprintf('Unexpected specific price priorities [%s]', var_export($actualPriorities, true))
        );
    }

    /**
     * Retrieves priority list using object model method which is actually used when prioritizing in FO.
     *
     * @param string|null $productReference gets default priority list if $productReference is null
     *
     * @return PriorityList
     */
    private function getUsablePriorities(?string $productReference): PriorityList
    {
        SpecificPrice::flushCache();
        $productId = $productReference ? $this->getSharedStorage()->get($productReference) : 0;
        $actualPriorities = SpecificPrice::getPriority($productId);

        if ($actualPriorities[0] == 'id_customer') {
            unset($actualPriorities[0]);
        }

        return new PriorityList(array_values($actualPriorities));
    }

    /**
     * @return PriorityList
     *
     * @throws CoreException
     */
    private function getDefaultPriorities(): PriorityList
    {
        SpecificPrice::flushCache();
        $specificPriceRepository = $this->getContainer()->get(SpecificPriceRepository::class);

        return $specificPriceRepository->getDefaultPriorities();
    }
}
