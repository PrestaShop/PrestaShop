<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;

class RelatedProductsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference should have no related products
     *
     * @param string $productReference
     */
    public function assertProductHasNoRelatedProducts(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $relatedProducts = $this->getQueryBus()->handle(new GetRelatedProducts($productId, $this->getDefaultLangId()));

        Assert::assertEmpty(
            $relatedProducts,
            sprintf('Product %s expected to have no related products', $productReference)
        );
    }

    /**
     * @When I set following related products to product :productReference:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function setRelatedProducts(string $productReference, TableNode $tableNode): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $references = array_keys($tableNode->getRowsHash());
        $relatedProductIds = [];

        foreach ($references as $reference) {
            $relatedProductIds[] = $this->getSharedStorage()->get($reference);
        }

        $this->getCommandBus()->handle(new SetRelatedProductsCommand($productId, $relatedProductIds));
    }

    /**
     * @Then product :productReference should have following related products:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertRelatedProducts(string $productReference, TableNode $tableNode)
    {
        $productId = $this->getSharedStorage()->get($productReference);

        $actualRelatedProducts = $this->getQueryBus()->handle(new GetRelatedProducts($productId, $this->getDefaultLangId()));
        $expectedRelatedProducts = $tableNode->getColumnsHash();

        Assert::assertEquals(count($expectedRelatedProducts), count($actualRelatedProducts));

        $index = 0;
        foreach ($expectedRelatedProducts as $expectedRelatedProduct) {
            /** @var RelatedProduct $actualRelatedProduct */
            $actualRelatedProduct = $actualRelatedProducts[$index];

            $expectedProductId = $this->getSharedStorage()->get($expectedRelatedProduct['product']);
            Assert::assertEquals(
                $expectedProductId,
                $actualRelatedProduct->getProductId(),
                sprintf(
                    'Invalid product ID, expected %d but got %d instead.',
                    $expectedProductId,
                    $actualRelatedProduct->getProductId()
                )
            );

            Assert::assertEquals(
                $expectedRelatedProduct['name'],
                $actualRelatedProduct->getName(),
                sprintf(
                    'Invalid product name, expected %s but got %s instead.',
                    $expectedRelatedProduct['name'],
                    $actualRelatedProduct->getName()
                )
            );

            Assert::assertEquals(
                $expectedRelatedProduct['reference'],
                $actualRelatedProduct->getReference(),
                sprintf(
                    'Invalid product reference, expected %s but got %s instead.',
                    $expectedRelatedProduct['reference'],
                    $actualRelatedProduct->getReference()
                )
            );

            $realImageUrl = $this->getRealImageUrl($expectedRelatedProduct['image url']);
            Assert::assertEquals(
                $realImageUrl,
                $actualRelatedProduct->getImageUrl(),
                sprintf(
                    'Invalid product image url, expected %s but got %s instead.',
                    $realImageUrl,
                    $actualRelatedProduct->getImageUrl()
                )
            );

            ++$index;
        }
    }

    /**
     * @When I remove all related products from product :productReference
     *
     * @param string $productReference
     */
    public function removeAllRelatedProducts(string $productReference)
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $this->getCommandBus()->handle(new RemoveAllRelatedProductsCommand($productId));
    }
}
