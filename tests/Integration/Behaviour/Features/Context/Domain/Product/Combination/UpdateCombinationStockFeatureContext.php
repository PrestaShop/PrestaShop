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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
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
