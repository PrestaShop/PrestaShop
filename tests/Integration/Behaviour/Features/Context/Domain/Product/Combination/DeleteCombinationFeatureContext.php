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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkDeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class DeleteCombinationFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I delete combination :combinationReference
     *
     * @param string $combinationReference
     */
    public function deleteCombinationInDefaultShop(string $combinationReference): void
    {
        $this->deleteSingleCombination($combinationReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I delete following combinations of product :productReference:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function bulkDeleteCombinationsInDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->bulkDeleteCombinations($productReference, $tableNode, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I delete following combinations of product :productReference from shop :shopReference:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $tableNode
     */
    public function bulkDeleteCombinationsFromShop(string $productReference, string $shopReference, TableNode $tableNode): void
    {
        $this->bulkDeleteCombinations($productReference, $tableNode, ShopConstraint::shop($this->getSharedStorage()->get($shopReference)));
    }

    /**
     * @When I delete following combinations of product ":productReference" from all shops:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function bulkDeleteCombinationsFromAllShops(string $productReference, TableNode $tableNode): void
    {
        $this->bulkDeleteCombinations($productReference, $tableNode, ShopConstraint::allShops());
    }

    /**
     * @When I delete combination :combinationReference from shops ":shopReferences"
     *
     * @param string $combinationReference
     * @param string $shopReferences
     */
    public function deleteCombinationFromShops(string $combinationReference, string $shopReferences): void
    {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $this->deleteSingleCombination($combinationReference, ShopConstraint::shop($this->getSharedStorage()->get($shopReference)));
        }
    }

    /**
     * @When I delete combination :combinationReference from all shops
     *
     * @param string $combinationReference
     */
    public function deleteCombinationFromAllShops(string $combinationReference): void
    {
        $this->deleteSingleCombination($combinationReference, ShopConstraint::allShops());
    }

    /**
     * @param string $combinationReference
     * @param ShopConstraint $shopConstraint
     */
    private function deleteSingleCombination(string $combinationReference, ShopConstraint $shopConstraint): void
    {
        $this->getCommandBus()->handle(new DeleteCombinationCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            $shopConstraint
        ));
    }

    /**
     * @param string $productReference
     * @param TableNode $tableNode
     * @param ShopConstraint $shopConstraint
     */
    private function bulkDeleteCombinations(string $productReference, TableNode $tableNode, ShopConstraint $shopConstraint): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $combinationIds = [];
        foreach ($tableNode->getColumnsHash() as $column) {
            $combinationIdReference = $column['id reference'];
            $combinationIds[] = $this->getSharedStorage()->get($combinationIdReference);
        }

        $this->getCommandBus()->handle(new BulkDeleteCombinationCommand(
            $productId,
            $combinationIds,
            $shopConstraint
        ));
    }
}
