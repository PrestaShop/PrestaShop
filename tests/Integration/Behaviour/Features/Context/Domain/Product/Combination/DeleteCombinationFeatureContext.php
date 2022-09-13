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
        $this->deleteSingleCombination($combinationReference, $this->getDefaultShopId());
    }

    /**
     * @When I delete following combinations of product :productReference:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function bulkDeleteCombinationsInDefaultShop(string $productReference, TableNode $tableNode): void
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
            ShopConstraint::shop($this->getDefaultShopId()))
        );
    }

    /**
     * @When I delete combination :combinationReference from shop :shopReference
     *
     * @param string $combinationReference
     * @param string $shopReference
     */
    public function deleteCombinationInShop(string $combinationReference, string $shopReference): void
    {
        $this->deleteSingleCombination($combinationReference, $this->getSharedStorage()->get($shopReference));
    }

    /**
     * @param string $combinationReference
     * @param int|null $shopId
     */
    private function deleteSingleCombination(string $combinationReference, ?int $shopId): void
    {
        $this->getCommandBus()->handle(new DeleteCombinationCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            ShopConstraint::shop($shopId)
        ));
    }
}
