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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

class GetCombinationIdsFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @Then I should see following paginated combination ids of product ":productReference":
     * @Then I should see following filtered combination ids of product ":productReference":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertPaginatedCombinationIdsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertCombinationIds($productReference,
            $tableNode->getColumnsHash(),
            $this->getDefaultShopId()
        );
    }

    /**
     * @Then I should see following paginated combination ids of product ":productReference" for shops ":shopReferences":
     * @Then I should see following filtered combination ids of product ":productReference" for shops ":shopReferences":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     * @param string $shopReferences
     */
    public function assertPaginatedCombinationIdsForShops(string $productReference, TableNode $tableNode, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->assertCombinationIds(
                $productReference,
                $tableNode->getColumnsHash(),
                $shopId
            );
        }
    }

    /**
     * @Then product ":productReference" should have the following combination ids:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertAllCombinationIdsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertCombinationIds(
            $productReference,
            $tableNode->getColumnsHash(),
            $this->getDefaultShopId(),
            true
        );
    }

    /**
     * @Given product ":productReference" combination ids search criteria is set to defaults
     *
     * @param string $productReference
     */
    public function cleanSearchCriteriaForDefaultShop(string $productReference): void
    {
        $this->getSharedStorage()->clear($this->getSearchCriteriaKey($productReference, $this->getDefaultShopId()));
    }

    /**
     * @Given product ":productReference" combination ids search criteria is set to defaults for shops ":shopReferences"
     *
     * @param string $productReference
     */
    public function cleanSearchCriteriaForShops(string $productReference, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->getSharedStorage()->clear($this->getSearchCriteriaKey($productReference, $shopId));
        }
    }

    /**
     * @When I search product ":productReference" combination ids by following search criteria:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function storeSearchCriteriaForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $combinationFilters = $this->buildProductCombinationFiltersForShop(
            (int) $this->getSharedStorage()->get($productReference),
            $tableNode,
            $this->getDefaultShopId()
        );

        $this->getSharedStorage()->set($this->getSearchCriteriaKey($productReference, $this->getDefaultShopId()), $combinationFilters);
    }

    /**
     * @When I search product ":productReference" combination ids by following search criteria for shop ":shopReference":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function storeSearchCriteriaForShop(string $productReference, TableNode $tableNode, string $shopReference): void
    {
        $combinationFilters = $this->buildProductCombinationFiltersForShop(
            (int) $this->getSharedStorage()->get($productReference),
            $tableNode,
            $this->getSharedStorage()->get($shopReference)
        );

        $this->getSharedStorage()->set(
            $this->getSearchCriteriaKey($productReference, $this->getSharedStorage()->get($shopReference)),
            $combinationFilters
        );
    }

    /**
     * @Then combination ids list of product ":productReference" should be empty
     *
     * @param string $productReference
     */
    public function assertNoCombinationsInPageForDefaultShop(string $productReference): void
    {
        $this->assertCombinationIds($productReference, [], $this->getDefaultShopId());
    }

    /**
     * @Then combination ids list of product ":productReference" should be empty for shops ":shopReferences"
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function assertNoCombinationsInPageForShops(string $productReference, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->assertCombinationIds($productReference, [], $shopId);
        }
    }

    /**
     * @param string $productReference
     * @param int $shopId
     *
     * @return string
     */
    private function getSearchCriteriaKey(string $productReference, int $shopId): string
    {
        return sprintf('combination_ids_search_criteria_%s_%s', $productReference, $shopId);
    }

    /**
     * @param string $productReference
     * @param array $dataRows
     * @param int $shopId
     * @param bool $wholeList if true then search criteria won't be applied
     */
    private function assertCombinationIds(
        string $productReference,
        array $dataRows,
        int $shopId,
        bool $wholeList = false
    ): void {
        $searchCriteriaKey = $this->getSearchCriteriaKey($productReference, $shopId);
        if ($wholeList) {
            $combinationFilters = null;
        } elseif ($this->getSharedStorage()->exists($searchCriteriaKey)) {
            $combinationFilters = $this->getSharedStorage()->get($searchCriteriaKey);
        } else {
            $combinationFilters = ProductCombinationFilters::buildDefaults();
        }

        $combinationsIds = $this->getCombinationIds($productReference, $shopId, $combinationFilters);

        Assert::assertEquals(
            count($dataRows),
            count($combinationsIds),
            sprintf('Unexpected combination ids count for product %s and shop with id %d', $productReference, $shopId)
        );

        foreach ($combinationsIds as $key => $combinationId) {
            $reference = $dataRows[$key]['id reference'];
            Assert::assertSame(
                $this->getSharedStorage()->get($reference),
                $combinationId->getValue(),
                'Unexpected combination id'
            );

            $this->getSharedStorage()->set($reference, $combinationId->getValue());
        }
    }
}
