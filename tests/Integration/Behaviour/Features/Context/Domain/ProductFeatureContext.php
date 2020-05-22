<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Cache;
use Context;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then I set tax rule group :taxRulesGroupReference to product :productReference
     *
     * @param string $taxRulesGroupReference
     * @param string $productName
     */
    public function setProductTaxRulesGroup(string $taxRulesGroupReference, string $productName)
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);
        $productId = $this->getProductIdByName($productName);

        $product = new Product($productId);
        $product->id_tax_rules_group = $taxRulesGroupId;
        $product->save();

        // Important to clean this cache or Product::getIdTaxRulesGroupByIdProduct still returns the initial value
        Cache::clean('product_id_tax_rules_group_*');
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductIdByName(string $productName): int
    {
        /** @var FoundProduct[] */
        $products = $this->getQueryBus()->handle(new SearchProducts($productName, 1, Context::getContext()->currency->iso_code));

        if (empty($products)) {
            throw new RuntimeException(sprintf('Product with name "%s" was not found', $productName));
        }

        /** @var FoundProduct $product */
        $product = reset($products);

        return $product->getProductId();
    }
}
