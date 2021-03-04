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
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetGlobalSpecificPricePriorityCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use SpecificPrice;

class SpecificPricePrioritiesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I set following specific price priorities for product :productReference:
     *
     * @param string $productReference
     * @param TableNode $prioritiesTable
     */
    public function setPrioritiesForSingleProduct(string $productReference, TableNode $prioritiesTable): void
    {
        $priorities = $prioritiesTable->getRow(0);

        try {
            $this->getCommandBus()->handle(new SetSpecificPricePriorityForProductCommand(
                $this->getSharedStorage()->get($productReference),
                $priorities
            ));
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I set following specific price priorities for all products:
     *
     * @param TableNode $prioritiesTable
     */
    public function setGlobalPriorities(TableNode $prioritiesTable): void
    {
        $priorities = $prioritiesTable->getRow(0);

        try {
            $this->getCommandBus()->handle(new SetGlobalSpecificPricePriorityCommand($priorities));
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following specific price priorities:
     *
     * @param string $productReference
     * @param TableNode $prioritiesTable
     */
    public function assertProductPriorities(string $productReference, TableNode $prioritiesTable): void
    {
        $expectedPriorities = $prioritiesTable->getRow(0);
        SpecificPrice::flushCache();
        $actualPriorities = SpecificPrice::getPriority($this->getSharedStorage()->get($productReference));

        if ($actualPriorities[0] == 'id_customer') {
            unset($actualPriorities[0]);
        }

        Assert::assertEquals(
            $expectedPriorities,
            array_values($actualPriorities),
            sprintf('Unexpected specific price priorities [%s]', var_export($actualPriorities, true))
        );
    }
}
