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
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShopBundle\Install\DatabaseDump;
use SpecificPrice;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

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
     * @When I set following specific price priorities for product :productReference:
     *
     * @param string $productReference
     * @param PriorityList $priorityList
     */
    public function setPrioritiesForSingleProduct(string $productReference, PriorityList $priorityList): void
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
            ->get('prestashop.adapter.product.specific_price.update.specific_price_priority_updater')
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
        $this->assertPriorities($priorityList, null);
    }

    /**
     * @see transformPriorityList
     *
     * @Then product :productReference should have following specific price priorities:
     *
     * @param string $productReference
     * @param PriorityList $priorityList
     */
    public function assertProductPriorities(string $productReference, PriorityList $priorityList): void
    {
        $this->assertPriorities($priorityList, $productReference);
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
     * @param PriorityList $expectedPriorityList
     * @param string|null $productReference when null it checks default priorities list
     */
    private function assertPriorities(PriorityList $expectedPriorityList, ?string $productReference)
    {
        SpecificPrice::flushCache();
        $productId = $productReference ? $this->getSharedStorage()->get($productReference) : 0;
        $actualPriorities = SpecificPrice::getPriority($productId);

        if ($actualPriorities[0] == 'id_customer') {
            unset($actualPriorities[0]);
        }

        Assert::assertEquals(
            $expectedPriorityList->getPriorities(),
            array_values($actualPriorities),
            sprintf('Unexpected specific price priorities [%s]', var_export($actualPriorities, true))
        );
    }
}
