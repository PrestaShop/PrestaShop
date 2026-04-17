<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;
use RuntimeException;
use Search;

class SearchProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I search for products with locale :localeReference matching :search I should get following results:
     *
     * @param string $localeReference
     * @param string $search
     * @param TableNode $tableNode
     */
    public function assertSearchProducts(string $localeReference, string $search, TableNode $tableNode): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        /** @var ProductForAssociation[] $foundProducts */
        $foundProducts = $this->getQueryBus()->handle(new SearchProductsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT')
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
     * @When I search for products with locale :localeReference matching :search I should get no results
     *
     * @param string $localeReference
     * @param string $search
     */
    public function assertNoProductsFound(string $localeReference, string $search): void
    {
        $language = $this->getSharedStorage()->get($localeReference);
        /** @var ProductForAssociation[] $foundProducts */
        $foundProducts = $this->getQueryBus()->handle(new SearchProductsForAssociation(
            $search,
            (int) $language->id,
            (int) Configuration::get('PS_SHOP_DEFAULT')
        ));
        Assert::assertEmpty($foundProducts);
    }

    /**
     * @When I search for products on front office with sentence :searchSentence with locale :locale I should find:
     */
    public function legacySearchProduct(string $searchSentence, string $locale, TableNode $table): void
    {
        $languageId = Language::getIdByLocale($locale, true);
        $search = Search::find($languageId, $searchSentence);

        $expectedProducts = $table->getColumnsHash();
        foreach ($expectedProducts as $expectedProduct) {
            $productId = $this->getSharedStorage()->get($expectedProduct['product_id']);
            $foundProduct = false;
            foreach ($search['result'] as $product) {
                if ((int) $product['id_product'] === $productId) {
                    $foundProduct = true;
                    Assert::assertEquals($expectedProduct['name'], $product['name']);
                    break;
                }
            }

            if (!$foundProduct) {
                throw new RuntimeException(sprintf('Could not find product %s in search result', $expectedProduct['product_id']));
            }
        }
    }

    /**
     * @When I search for products on front office with sentence :searchSentence with locale :locale I should find nothing
     */
    public function legacySearchProductNotFound(string $searchSentence, string $locale): void
    {
        $languageId = Language::getIdByLocale($locale, true);
        $search = Search::find($languageId, $searchSentence);

        Assert::assertEquals(0, (int) $search['total']);
        Assert::assertEmpty($search['result']);
    }
}
