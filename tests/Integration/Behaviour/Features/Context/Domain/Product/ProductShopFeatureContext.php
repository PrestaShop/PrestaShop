<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class ProductShopFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference is not associated to shop(s) :shopReferences
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function checkNoShopAssociation(string $productReference, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $caughtException = null;
            try {
                $this->getProductForEditing($productReference, $shopId);
            } catch (ProductShopAssociationNotFoundException $e) {
                $caughtException = $e;
            }

            Assert::assertNotNull($caughtException);
        }
    }

    /**
     * @Then product :productReference is associated to shop(s) :shopReferences
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function checkShopAssociation(string $productReference, string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);
        foreach ($shopIds as $shopId) {
            $caughtException = null;
            try {
                $product = $this->getProductForEditing($productReference, $shopId);
                foreach ($shopIds as $checkedShopId) {
                    Assert::assertTrue(in_array($checkedShopId, $product->getShopIds()));
                }
            } catch (ProductShopAssociationNotFoundException $e) {
                $caughtException = $e;
            }

            Assert::assertNull($caughtException);
        }
    }

    /**
     * @Then default shop for product :productReference is :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function checkDefaultShop(string $productReference, string $shopReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $shopId = $this->getSharedStorage()->get($shopReference);

        /** @var ProductRepository $productRepository */
        $productRepository = CommonFeatureContext::getContainer()->get(ProductRepository::class);
        $defaultShopId = $productRepository->getProductDefaultShopId(new ProductId($productId));
        Assert::assertEquals($shopId, $defaultShopId->getValue());
    }

    /**
     * @When I set following shops for product ":productReference":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function setProductShops(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();

        try {
            $this->getCommandBus()->handle(new SetProductShopsCommand(
                $this->getSharedStorage()->get($productReference),
                $this->getSharedStorage()->get($data['source shop']),
                $this->referencesToIds($data['shops'])
            ));
        } catch (InvalidProductShopAssociationException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that I cannot unassociate product from all shops
     */
    public function assertLastExceptionIsEmptyProductShopAssociation(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::EMPTY_SHOPS_ASSOCIATION
        );
    }

    /**
     * @Then I should get error that I cannot unassociate product from source shop
     */
    public function assertLastExceptionIsSourceShopMissingInShopAssociation(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::SOURCE_SHOP_MISSING_IN_SHOP_ASSOCIATION
        );
    }

    /**
     * @Then I should get error that source shop is not associated to product
     */
    public function assertLastExceptionIsSourceShopIsNotAssociated(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::SOURCE_SHOP_NOT_ASSOCIATED
        );
    }
}
