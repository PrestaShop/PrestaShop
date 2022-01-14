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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllPackedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProductsDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;

class PackedProductsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference should have no packed products
     *
     * @param string $productReference
     */
    public function assertProductHasNoPackedProducts(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $packedProducts = $this->getQueryBus()->handle(new GetPackedProductsDetails($productId));

        Assert::assertEmpty(
            $packedProducts,
            sprintf('Product %s expected to have no packed products', $productReference)
        );
    }

    /**
     * @When I set following packed products to product :productReference:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function setPackedProducts(string $productReference, TableNode $tableNode): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $references = $tableNode->getColumnsHash();

        $quantifiedProducts = [];
        foreach ($references as $key => $reference) {
            $quantifiedProducts[] = [
                'product_id' => $this->getSharedStorage()->get($reference['product']),
                'quantity' => (int) $reference['quantity'],
            ];
        }

        $this->getCommandBus()->handle(new SetPackProductsCommand($productId, $quantifiedProducts));
    }

    /**
     * @Then product :productReference should have following packed products:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertPackedProducts(string $productReference, TableNode $tableNode)
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $actualPackedProducts = $this->getQueryBus()->handle(new GetPackedProductsDetails($productId));
        $expectedPackedProducts = $tableNode->getColumnsHash();

        Assert::assertEquals(count($expectedPackedProducts), count($actualPackedProducts));

        $index = 0;

        foreach ($expectedPackedProducts as $expectedPackedProduct) {
            /** @var PackedProductDetails $actualPackedProduct */
            $actualPackedProduct = $actualPackedProducts[$index];

            $expectedProductId = $this->getSharedStorage()->get($expectedPackedProduct['product']);
            Assert::assertEquals(
                $expectedProductId,
                $actualPackedProduct->getProductId(),
                sprintf(
                    'Invalid product ID, expected %d but got %d instead.',
                    $expectedProductId,
                    $actualPackedProduct->getProductId()
                )
            );

            Assert::assertEquals(
                $expectedPackedProduct['productName'],
                $actualPackedProduct->getProductName(),
                sprintf(
                    'Invalid product name, expected %s but got %s instead.',
                    $expectedPackedProduct['productName'],
                    $actualPackedProduct->getProductName()
                )
            );

            Assert::assertEquals(
                $expectedPackedProduct['quantity'],
                $actualPackedProduct->getQuantity(),
                sprintf(
                    'Invalid product quantity, expected %s but got %s instead.',
                    $expectedPackedProduct['quantity'],
                    $actualPackedProduct->getQuantity()
                )
            );

            ++$index;
        }
    }

    /**
     * @When I remove all packed products from product :productReference
     *
     * @param string $productReference
     */
    public function removeAllPackedProducts(string $productReference)
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $this->getCommandBus()->handle(new RemoveAllPackedProductsCommand($productId));
    }
}
