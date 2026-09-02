<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateCombinationStockFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update combination ":combinationReference" stock with following details:
     */
    public function updateStockForDefaultShop(
        string $combinationReference,
        TableNode $tableNode
    ): void {
        $this->updateStockAvailable(
            $combinationReference,
            $tableNode->getRowsHash(),
            ShopConstraint::shop($this->getDefaultShopId())
        );
    }

    /**
     * @When I update combination :combinationReference stock for shop :shopReference with following details:
     */
    public function updateStockForShop(
        string $combinationReference,
        string $shopReference,
        TableNode $tableNode
    ): void {
        $this->updateStockAvailable(
            $combinationReference,
            $tableNode->getRowsHash(),
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference))
        );
    }

    /**
     * @When I update combination :combinationReference stock for shops :shopReferences with following details:
     */
    public function updateStockForShopCollection(
        string $combinationReference,
        string $shopReferences,
        TableNode $tableNode
    ): void {
        $this->updateStockAvailable(
            $combinationReference,
            $tableNode->getRowsHash(),
            ShopCollection::shops($this->referencesToIds($shopReferences))
        );
    }

    /**
     * @When I update combination ":combinationReference" stock for all shops with following details:
     */
    public function updateStockForAllShops(
        string $combinationReference,
        TableNode $tableNode
    ): void {
        $this->updateStockAvailable(
            $combinationReference,
            $tableNode->getRowsHash(),
            ShopConstraint::allShops()
        );
    }

    private function updateStockAvailable(string $combinationReference, array $dataRows, ShopConstraint $shopConstraint): void
    {
        if (!isset($dataRows['delta quantity'])
            && !isset($dataRows['fixed quantity'])
            && !isset($dataRows['location'])) {
            return;
        }

        try {
            $command = new UpdateCombinationStockAvailableCommand(
                (int) $this->getSharedStorage()->get($combinationReference),
                $shopConstraint
            );
            if (isset($dataRows['delta quantity'])) {
                $command->setDeltaQuantity((int) $dataRows['delta quantity']);
            }
            if (isset($dataRows['fixed quantity'])) {
                $command->setFixedQuantity((int) $dataRows['fixed quantity']);
            }
            if (isset($dataRows['location'])) {
                $command->setLocation($dataRows['location']);
            }

            $this->getCommandBus()->handle($command);
        } catch (ProductStockConstraintException $e) {
            $this->setLastException($e);
        }
    }
}
