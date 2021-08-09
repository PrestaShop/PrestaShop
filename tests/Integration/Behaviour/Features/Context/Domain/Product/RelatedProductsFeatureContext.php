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

        $expectedReferences = array_keys($tableNode->getRowsHash());
        $actualRelatedProducts = $this->getQueryBus()->handle(new GetRelatedProducts($productId, $this->getDefaultLangId()));

        $expectedIds = array_map(function (string $reference): int {
            return $this->getSharedStorage()->get($reference);
        }, $expectedReferences);

        $actualIds = array_map(function (RelatedProduct $relatedProduct): int {
            return $relatedProduct->getProductId();
        }, $actualRelatedProducts);

        Assert::assertEquals($expectedIds, $actualIds, 'Unexpected related products');
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
