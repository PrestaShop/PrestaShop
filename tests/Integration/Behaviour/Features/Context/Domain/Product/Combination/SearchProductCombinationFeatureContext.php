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
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchProductCombinations;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class SearchProductCombinationFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I search product ":productReference" combinations by phrase ":searchPhrase" in language ":langIso" for shop ":shopReference" limited to ":limit" results I should see following results:
     *
     * @param string $productReference
     * @param string $searchPhrase
     * @param string $langIso
     * @param string $shopReference
     * @param int $limit
     * @param ProductCombinationsCollection $expectedResults
     *
     * @return void
     *
     * @see transformProductCombinationsResult for $expectedResults type transformation
     */
    public function searchProductCombinationsForShop(
        string $productReference,
        string $searchPhrase,
        string $langIso,
        string $shopReference,
        int $limit,
        ProductCombinationsCollection $expectedResults
    ): void {
        $this->searchProductCombinations(
            $productReference,
            $searchPhrase,
            $langIso,
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference)),
            $limit,
            $expectedResults
        );
    }

    /**
     * @When I search product ":productReference" combinations by phrase ":searchPhrase" in language ":langIso" for all shops limited to ":limit" results I should see following results:
     *
     * @param string $productReference
     * @param string $searchPhrase
     * @param string $langIso
     * @param int $limit
     * @param ProductCombinationsCollection $expectedResults
     *
     * @return void
     *
     * @see transformProductCombinationsResult for $expectedResults type transformation
     */
    public function searchProductCombinationsForAllShops(
        string $productReference,
        string $searchPhrase,
        string $langIso,
        int $limit,
        ProductCombinationsCollection $expectedResults
    ): void {
        $this->searchProductCombinations(
            $productReference,
            $searchPhrase,
            $langIso,
            ShopConstraint::allShops(),
            $limit,
            $expectedResults
        );
    }

    /**
     * @Transform table:id reference,combination name
     *
     * @return ProductCombinationsCollection
     */
    public function transformProductCombinationsResult(TableNode $tableNode): ProductCombinationsCollection
    {
        $rows = $tableNode->getHash();

        $productCombinations = [];
        foreach ($rows as $row) {
            $productCombinations[] = new ProductCombination(
                $this->getSharedStorage()->get($row['id reference']),
                $row['combination name']
            );
        }

        return new ProductCombinationsCollection($productCombinations);
    }

    /**
     * @When I list product ":productReference" combinations in language ":langIso" for shop ":shopReference" limited to ":limit" results I should see following results:
     *
     * @param string $productReference
     * @param string $langIso
     * @param string $shopReference
     * @param int $limit
     * @param ProductCombinationsCollection $expectedResults
     *
     * @return void
     *
     * @see transformProductCombinationsResult for $expectedResults type transformation
     */
    public function listProductCombinationsForShop(
        string $productReference,
        string $langIso,
        string $shopReference,
        int $limit,
        ProductCombinationsCollection $expectedResults
    ): void {
        $this->searchProductCombinations(
            $productReference,
            '',
            $langIso,
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference)),
            $limit,
            $expectedResults
        );
    }

    /**
     * @param string $productReference
     * @param string $searchPhrase
     * @param string $langIso
     * @param ShopConstraint $shopConstraint
     * @param int $limit
     * @param ProductCombinationsCollection $expectedResults
     *
     * @return void
     *
     * @see transformProductCombinationsResult for $expectedResults type transformation
     */
    private function searchProductCombinations(
        string $productReference,
        string $searchPhrase,
        string $langIso,
        ShopConstraint $shopConstraint,
        int $limit,
        ProductCombinationsCollection $expectedResults
    ): void {
        /** @var ProductCombinationsCollection $productCombinationsResults */
        $productCombinationsResults = $this->getQueryBus()->handle(new SearchProductCombinations(
            $this->getSharedStorage()->get($productReference),
            (int) Language::getIdByIso($langIso),
            $shopConstraint,
            $searchPhrase,
            $limit
        ));

        Assert::assertEquals($expectedResults, $productCombinationsResults);
    }
}
