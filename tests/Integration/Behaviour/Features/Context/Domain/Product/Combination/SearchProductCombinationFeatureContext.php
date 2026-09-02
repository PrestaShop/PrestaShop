<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
