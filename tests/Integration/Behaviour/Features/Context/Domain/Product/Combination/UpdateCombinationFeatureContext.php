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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * This feature context is based on the unified command UpdateCombinationCommand, it will include all the other steps
 * implemented in other contexts based on specified command until everything is unified. Once it's done the steps should
 * be simplified into a single unified step usable in all the behat scenarios.
 */
class UpdateCombinationFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update combination ":combinationReference" with following values:
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updateCombinationForDefaultShop(string $combinationReference, TableNode $tableNode): void
    {
        $this->updateCombination($combinationReference, $tableNode, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I update combination ":combinationReference" with following values for shop ":shopReference":
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updateCombinationForShop(string $combinationReference, TableNode $tableNode, string $shopReference): void
    {
        $this->updateCombination(
            $combinationReference,
            $tableNode,
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference))
        );
    }

    /**
     * @When I update combination ":combinationReference" with following values for all shops:
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updateCombinationForAllShops(string $combinationReference, TableNode $tableNode): void
    {
        $this->updateCombination(
            $combinationReference,
            $tableNode,
            ShopConstraint::allShops()
        );
    }

    /**
     * @When I set combination ":combinationReference" as default
     *
     * @param string $combinationReference
     */
    public function setDefaultCombinationForDefaultShop(string $combinationReference): void
    {
        $this->setDefaultCombination($combinationReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I set combination ":combinationReference" as default for shop ":shopReference"
     *
     * @param string $combinationReference
     * @param string $shopReference
     */
    public function setDefaultCombinationForShop(string $combinationReference, string $shopReference): void
    {
        $this->setDefaultCombination(
            $combinationReference,
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference))
        );
    }

    /**
     * @When I set combination ":combinationReference" as default for all shops
     *
     * @param string $combinationReference
     */
    public function setDefaultCombinationForAllShops(string $combinationReference): void
    {
        $this->setDefaultCombination($combinationReference, ShopConstraint::allShops());
    }

    private function setDefaultCombination(string $combinationReference, ShopConstraint $shopConstraint): void
    {
        $command = new UpdateCombinationCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            $shopConstraint
        );
        $command->setIsDefault(true);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @param UpdateCombinationCommand $command
     * @param array $dataRows
     */
    private function fillCommand(UpdateCombinationCommand $command, array $dataRows): void
    {
        // Is default
        if (isset($dataRows['is default'])) {
            $command->setIsDefault(PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['is default']));
        }
        // References
        if (isset($dataRows['ean13'])) {
            $command->setEan13($dataRows['ean13']);
        }
        if (isset($dataRows['isbn'])) {
            $command->setIsbn($dataRows['isbn']);
        }
        if (isset($dataRows['mpn'])) {
            $command->setMpn($dataRows['mpn']);
        }
        if (isset($dataRows['reference'])) {
            $command->setReference($dataRows['reference']);
        }
        if (isset($dataRows['upc'])) {
            $command->setUpc($dataRows['upc']);
        }
        // Prices
        if (isset($dataRows['impact on weight'])) {
            $command->setImpactOnWeight($dataRows['impact on weight']);
        }
        if (isset($dataRows['eco tax'])) {
            $command->setEcoTax($dataRows['eco tax']);
        }
        if (isset($dataRows['impact on price'])) {
            $command->setImpactOnPrice($dataRows['impact on price']);
        }
        if (isset($dataRows['impact on unit price'])) {
            $command->setImpactOnUnitPrice($dataRows['impact on unit price']);
        }
        if (isset($dataRows['wholesale price'])) {
            $command->setWholesalePrice($dataRows['wholesale price']);
        }
        // Stock information
        if (isset($dataRows['minimal quantity'])) {
            $command->setMinimalQuantity((int) $dataRows['minimal quantity']);
        }
        if (isset($dataRows['low stock threshold'])) {
            $command->setLowStockThreshold((int) $dataRows['low stock threshold']);
        }
        if (isset($dataRows['available date'])) {
            $command->setAvailableDate(new DateTime($dataRows['available date']));
        }
        if (isset($dataRows['available now labels'])) {
            $command->setLocalizedAvailableNowLabels($dataRows['available now labels']);
            unset($dataRows['available now labels']);
        }
        if (isset($dataRows['available later labels'])) {
            $command->setLocalizedAvailableLaterLabels($dataRows['available later labels']);
            unset($dataRows['available later labels']);
        }
    }

    private function updateCombination(string $combinationReference, TableNode $tableNode, ShopConstraint $shopConstraint): void
    {
        $command = new UpdateCombinationCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            $shopConstraint
        );

        $this->fillCommand($command, $tableNode->getRowsHash());
        $this->getCommandBus()->handle($command);
    }
}
