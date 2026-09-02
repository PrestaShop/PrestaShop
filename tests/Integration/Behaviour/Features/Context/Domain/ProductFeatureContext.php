<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Cache;
use Category;
use Customer;
use GroupReduction;
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
     * @Given The default category of product :productName has a group reduction of :reductionPercent% for the customer :customerReference
     */
    public function addGroupReductionOnProductDefaultCategoryForCustomerGroup(string $productName, float $reductionPercent, string $customerReference)
    {
        $productId = $this->getProductIdByName($productName);

        $product = new Product($productId);
        if (!Category::categoryExists($product->id_category_default)) {
            throw new RuntimeException('The product doesn\'t have default category');
        }

        $customerId = SharedStorage::getStorage()->get($customerReference);
        if (!Customer::customerIdExistsStatic($customerId)) {
            throw new RuntimeException('The customer doesn\'t exist');
        }

        $customer = new Customer((int) $customerId);

        $groupReduction = new GroupReduction();
        $groupReduction->id_category = $product->id_category_default;
        $groupReduction->id_group = $customer->id_default_group;
        $groupReduction->reduction = $reductionPercent / 100;

        if (!$groupReduction->add()) {
            throw new RuntimeException('Cannot add group reduction to database');
        }

        GroupReduction::resetStaticCache();
    }

    /**
     * @Given The default category of product :productName has no group reduction
     */
    public function deleteGroupReductionOnProductDefaultCategory(string $productName)
    {
        $productId = $this->getProductIdByName($productName);

        GroupReduction::deleteProductReduction($productId);

        $product = new Product($productId);

        GroupReduction::deleteCategory($product->id_category_default);

        GroupReduction::resetStaticCache();
    }

    /**
     * @Then there is a product :productReference with name :productName
     *
     * @param string $productReference
     * @param string $productName
     */
    public function storeProductReferenceByName(string $productReference, string $productName): void
    {
        $productId = $this->getProductIdByName($productName);
        $this->getSharedStorage()->set($productReference, $productId);
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductIdByName(string $productName): int
    {
        /** @var FoundProduct[] */
        $products = $this->getQueryBus()->handle(new SearchProducts($productName, 1, $this->getDefaultCurrencyIsoCode()));

        if (empty($products)) {
            throw new RuntimeException(sprintf('Product with name "%s" was not found', $productName));
        }

        /** @var FoundProduct $product */
        $product = reset($products);

        return $product->getProductId();
    }
}
