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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\RemoveAllProductsFromPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;

class UpdatePackFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update pack :packReference with following product quantities:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function updateProductPack(string $packReference, TableNode $table): void
    {
        $data = $table->getColumnsHash();

        $products = [];
        foreach ($data as $row) {
            $products[] = [
                'product_id' => $this->getSharedStorage()->get($row['product']),
                'quantity' => (int) $row['quantity'],
                'combination_id' => $this->getExpectedCombinationId($row),
            ];
        }

        $packId = $this->getSharedStorage()->get($packReference);
        try {
            $this->getCommandBus()->handle(new SetPackProductsCommand($packId, $products));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all products from pack :packReference
     *
     * @param string $packReference
     */
    public function removeAllProductsFromPack(string $packReference): void
    {
        $packId = $this->getSharedStorage()->get($packReference);

        try {
            $this->getCommandBus()->handle(new RemoveAllProductsFromPackCommand($packId));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When pack :packReference should be empty
     *
     * @param string $packReference
     */
    public function assertPackEmptyForDefaultShop(string $packReference): void
    {
        $this->assertPackEmpty($packReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When pack :packReference should be empty for shop(s) :shopReferences
     *
     * @param string $packReference
     * @param string $shopReferences
     */
    public function assertPackEmptyForShops(string $packReference, string $shopReferences): void
    {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $this->assertPackEmpty($packReference, ShopConstraint::shop((int) $this->getSharedStorage()->get($shopReference)));
        }
    }

    /**
     * @Then pack :packReference should contain products with following details:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function assertPackContentsForDefaultShop(string $packReference, TableNode $table): void
    {
        $this->assertPackContents($packReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @Then pack :packReference should contain products with following details for shop(s) :shopReferences:
     *
     * @param string $packReference
     * @param TableNode $table
     * @param string $shopReferences
     */
    public function assertPackContentsForShops(string $packReference, TableNode $table, string $shopReferences): void
    {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $this->assertPackContents($packReference, $table, ShopConstraint::shop((int) $this->getSharedStorage()->get($shopReference)));
        }
    }

    /**
     * @Then I should get error that product for packing quantity is invalid
     */
    public function assertPackProductQuantityError(): void
    {
        $this->assertLastErrorIs(
            ProductPackConstraintException::class,
            ProductPackConstraintException::INVALID_QUANTITY
        );
    }

    /**
     * @Then I should get error that I cannot add pack into a pack
     */
    public function assertAddingPackToPackError(): void
    {
        $this->assertLastErrorIs(
            ProductPackConstraintException::class,
            ProductPackConstraintException::CANNOT_ADD_PACK_INTO_PACK
        );
    }

    /**
     * @param array<string, string> $dataRow
     *
     * @return int
     */
    private function getExpectedCombinationId(array $dataRow): int
    {
        if (isset($dataRow['combination']) && '' !== $dataRow['combination']) {
            return $this->getSharedStorage()->get($dataRow['combination']);
        }

        return CombinationId::NO_COMBINATION;
    }

    private function assertPackContents(string $packReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $table->getColumnsHash();
        $packId = $this->getSharedStorage()->get($packReference);
        /** @var array<int, PackedProductDetails> $packedProducts */
        $packedProducts = $this->getQueryBus()->handle(
            new GetPackedProducts(
                $packId,
                $this->getDefaultLangId(),
                $shopConstraint
            )
        );
        if (count($data) !== count($packedProducts)) {
            throw new RuntimeException(sprintf(
                'Incorrect number of product for shop %d expected %d but got %d instead',
                $shopConstraint->getShopId()->getValue(),
                count($data),
                count($packedProducts)
            ));
        }

        $notExistingProducts = [];

        foreach ($data as $expectedPackedProduct) {
            $productReference = $expectedPackedProduct['product'];
            $expectedQuantity = (int) $expectedPackedProduct['quantity'];
            $expectedName = $expectedPackedProduct['name'];
            $expectedCombination = $expectedPackedProduct['combination'];
            $expectedPackedProductId = $this->getSharedStorage()->get($productReference);
            $expectedCombinationId = !empty($expectedPackedProduct['combination']) ? $this->getSharedStorage()->get($expectedPackedProduct['combination']) : 0;
            $foundProduct = false;

            foreach ($packedProducts as $key => $packedProduct) {
                if ($packedProduct->getProductId() === $expectedPackedProductId) {
                    $foundProduct = true;

                    Assert::assertEquals(
                        $expectedName,
                        $packedProduct->getProductName(),
                        sprintf('Unexpected name of packed product "%s"', $productReference)
                    );

                    if ($expectedCombination !== '') {
                        Assert::assertEquals(
                            $expectedCombinationId,
                            $packedProduct->getCombinationId(),
                            sprintf('Unexpected combination (%s) of packed product "%s"', $expectedCombinationId, $productReference)
                        );
                    }

                    Assert::assertEquals(
                        $expectedQuantity,
                        $packedProduct->getQuantity(),
                        sprintf('Unexpected quantity of packed product "%s"', $productReference)
                    );

                    $realImageUrl = $this->getRealImageUrl($expectedPackedProduct['image url']);
                    Assert::assertEquals(
                        $realImageUrl,
                        $packedProduct->getImageUrl(),
                        sprintf(
                            'Invalid product image url, expected %s but got %s instead.',
                            $realImageUrl,
                            $packedProduct->getImageUrl()
                        )
                    );

                    if (isset($expectedPackedProduct['reference'])) {
                        Assert::assertEquals(
                            $expectedPackedProduct['reference'],
                            $packedProduct->getReference(),
                            sprintf('Unexpected reference of packed product "%s"', $productReference)
                        );
                    }

                    //unset asserted product to check if there was any excessive actual products after loops
                    unset($packedProducts[$key]);
                    break;
                }
            }

            if (!$foundProduct) {
                if ($expectedCombinationId) {
                    $notExistingProducts[$productReference][$expectedPackedProduct['combination']] = $expectedQuantity;
                } else {
                    $notExistingProducts[$productReference] = $expectedQuantity;
                }
            }
        }

        if (!empty($notExistingProducts)) {
            throw new RuntimeException(sprintf(
                'Failed to find following packed products for shop %d: %s',
                $shopConstraint->getShopId()->getValue(),
                var_export($notExistingProducts, true)
            ));
        }

        if (!empty($packedProducts)) {
            throw new RuntimeException(sprintf(
                'Following packed products were not expected for shop %d: %s',
                $shopConstraint->getShopId()->getValue(),
                var_export($packedProducts, true)
            ));
        }
    }

    private function assertPackEmpty(string $packReference, ShopConstraint $shopConstraint): void
    {
        $packId = $this->getSharedStorage()->get($packReference);

        $packedProducts = $this->getQueryBus()->handle(
            new GetPackedProducts(
                $packId,
                $this->getDefaultLangId(),
                $shopConstraint
            )
        );
        Assert::assertEmpty($packedProducts);
    }
}
