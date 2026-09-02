<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchCombinationsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

class SearchCombinationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I search for combinations with locale :localeReference matching :search I should get following results:
     *
     * @param string $localeReference
     * @param string $search
     * @param TableNode $tableNode
     */
    public function assertSearchCombinations(string $localeReference, string $search, TableNode $tableNode): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        /** @var CombinationForAssociation[] $foundCombinations */
        $foundCombinations = $this->getQueryBus()->handle(new SearchCombinationsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT')
        ));
        $expectedRelatedCombinations = $tableNode->getColumnsHash();

        Assert::assertEquals(count($expectedRelatedCombinations), count($foundCombinations));

        $index = 0;
        foreach ($expectedRelatedCombinations as $expectedRelatedCombination) {
            $foundCombinationForAssociation = $foundCombinations[$index];

            $expectedProductId = $this->getSharedStorage()->get($expectedRelatedCombination['product']);
            Assert::assertEquals(
                $expectedProductId,
                $foundCombinationForAssociation->getProductId(),
                sprintf(
                    'Invalid product ID, expected %d but got %d instead.',
                    $expectedProductId,
                    $foundCombinationForAssociation->getProductId()
                )
            );

            $expectedCombinationId = !empty($expectedRelatedCombination['combination']) ?
                $this->getSharedStorage()->get($expectedRelatedCombination['combination']) :
                NoCombinationId::NO_COMBINATION_ID;
            Assert::assertEquals(
                $expectedCombinationId,
                $foundCombinationForAssociation->getCombinationId(),
                sprintf(
                    'Invalid combination ID, expected %d but got %d instead.',
                    $expectedCombinationId,
                    $foundCombinationForAssociation->getCombinationId()
                )
            );

            Assert::assertEquals(
                $expectedRelatedCombination['name'],
                $foundCombinationForAssociation->getName(),
                sprintf(
                    'Invalid product name, expected %s but got %s instead.',
                    $expectedRelatedCombination['name'],
                    $foundCombinationForAssociation->getName()
                )
            );

            Assert::assertEquals(
                $expectedRelatedCombination['reference'],
                $foundCombinationForAssociation->getReference(),
                sprintf(
                    'Invalid product reference, expected %s but got %s instead.',
                    $expectedRelatedCombination['reference'],
                    $foundCombinationForAssociation->getReference()
                )
            );

            $realImageUrl = $this->getRealImageUrl($expectedRelatedCombination['image url']);
            Assert::assertEquals(
                $realImageUrl,
                $foundCombinationForAssociation->getImageUrl(),
                sprintf(
                    'Invalid product image url, expected %s but got %s instead.',
                    $realImageUrl,
                    $foundCombinationForAssociation->getImageUrl()
                )
            );

            ++$index;
        }
    }

    /**
     * @When I search for combinations with locale :localeReference matching :search I should get no results
     *
     * @param string $localeReference
     * @param string $search
     */
    public function assertNoProductsFound(string $localeReference, string $search): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        /** @var CombinationForAssociation[] $foundProducts */
        $foundProducts = $this->getQueryBus()->handle(new SearchCombinationsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT')
        ));

        Assert::assertEmpty($foundProducts);
    }

    /**
     * @When I search for products with locale :localeReference matching :search for :packedProductReference I should get following results:
     *
     * @param string $localeReference
     * @param string $search
     * @param TableNode $tableNode
     */
    public function assertSearchProductsForPack(string $localeReference, string $search, string $packedProductReference, TableNode $tableNode): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        $filters = [
            'filteredTypes' => [ProductType::TYPE_PACK],
        ];

        /** @var ProductForAssociation[] $foundProducts */
        $foundProducts = $this->getQueryBus()->handle(new SearchCombinationsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT'),
            $filters,
            20
        ));

        $expectedRelatedProducts = $tableNode->getColumnsHash();

        Assert::assertEquals(count($expectedRelatedProducts), count($foundProducts));

        $index = 0;
        foreach ($expectedRelatedProducts as $expectedRelatedProduct) {
            $foundProductForAssociation = $foundProducts[$index];

            $expectedProductId = $this->getSharedStorage()->get($expectedRelatedProduct['product']);
            Assert::assertEquals(
                $expectedProductId,
                $foundProductForAssociation->getProductId(),
                sprintf(
                    'Invalid product ID, expected %d but got %d instead.',
                    $expectedProductId,
                    $foundProductForAssociation->getProductId()
                )
            );

            Assert::assertEquals(
                $expectedRelatedProduct['name'],
                $foundProductForAssociation->getName(),
                sprintf(
                    'Invalid product name, expected %s but got %s instead.',
                    $expectedRelatedProduct['name'],
                    $foundProductForAssociation->getName()
                )
            );

            Assert::assertEquals(
                $expectedRelatedProduct['reference'],
                $foundProductForAssociation->getReference(),
                sprintf(
                    'Invalid product reference, expected %s but got %s instead.',
                    $expectedRelatedProduct['reference'],
                    $foundProductForAssociation->getReference()
                )
            );

            $realImageUrl = $this->getRealImageUrl($expectedRelatedProduct['image url']);
            Assert::assertEquals(
                $realImageUrl,
                $foundProductForAssociation->getImageUrl(),
                sprintf(
                    'Invalid product image url, expected %s but got %s instead.',
                    $realImageUrl,
                    $foundProductForAssociation->getImageUrl()
                )
            );

            ++$index;
        }
    }

    /**
     * @When I search for combinations with locale :localeReference matching :search for packs I should get no results
     *
     * @param string $localeReference
     * @param string $search
     */
    public function assertSearchProductsForPackNoResults(string $localeReference, string $search): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        $filters = [
            'filteredTypes' => [ProductType::TYPE_PACK],
        ];

        /** @var ProductForAssociation[] $foundProducts */
        $foundProducts = $this->getQueryBus()->handle(new SearchCombinationsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT'),
            $filters,
            20
        ));

        Assert::assertEquals(0, count($foundProducts));
    }
}
