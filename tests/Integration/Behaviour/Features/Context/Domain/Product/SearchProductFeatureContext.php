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
use Configuration;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;

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
}
