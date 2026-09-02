<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
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
     * @When I update combination ":combinationReference" with following values for shops ":shopReferences":
     *
     * @param string $combinationReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    public function updateCombinationForShopCollection(string $combinationReference, TableNode $tableNode, string $shopReferences): void
    {
        $this->updateCombination(
            $combinationReference,
            $tableNode,
            ShopCollection::shops($this->referencesToIds($shopReferences))
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
     * @When I set combination ":combinationReference" as default for shops ":shopReference"
     *
     * @param string $combinationReference
     * @param string $shopReferences
     */
    public function setDefaultCombinationForShopCollection(string $combinationReference, string $shopReferences): void
    {
        $this->setDefaultCombination(
            $combinationReference,
            ShopCollection::shops($this->referencesToIds($shopReferences))
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
            $command->setGtin($dataRows['ean13']);
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
