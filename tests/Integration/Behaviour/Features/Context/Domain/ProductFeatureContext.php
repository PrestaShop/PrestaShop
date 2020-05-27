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

use Behat\Gherkin\Node\TableNode;
use Cache;
use Configuration;
use Context;
use Exception;
use Pack;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

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
     * @When I add product :productReference with following basic information in default language:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function addProduct(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');

        try {
            $productId = $this->getCommandBus()->handle(new AddProductCommand(
                [$defaultLangId => $data['name']],
                $this->getProductTypeValueByName($data['type'])
            ));

            $this->getSharedStorage()->set($productReference, $productId->getValue());
        } catch (Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then /^product "(.+)" name should be "(.+)" in default language$/
     *
     * @param string $productReference
     * @param string $name
     */
    public function assertProductNameInDefaultLang(string $productReference, string $name)
    {
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $product = $this->getProductByReference($productReference);

        if ($product->name[$defaultLangId] === $name) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Expected product name to be "%s" in default language, but it is "%s"',
                $name,
                $product->name[$defaultLangId]
            )
        );
    }

    /**
     * @Then product :productReference should have following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductFields(string $productReference, TableNode $table)
    {
        $product = $this->getProductByReference($productReference);
        $data = $table->getRowsHash();

        if (!empty($data['active'])) {
            $status = PrimitiveUtils::castStringBooleanIntoBoolean($data['active']);
            $statusInWords = $status ? 'enabled' : 'disabled';

            if ((bool) $product->active !== $status) {
                throw new RuntimeException(sprintf('Product expected to be %s', $statusInWords));
            }
        }

        if (!empty($data['condition'])) {
            if ($product->condition !== $data['condition']) {
                throw new RuntimeException(sprintf(
                    'Product condition expected to be "%s, but is "%s"',
                    $data['condition'],
                        $product->condition
                    )
                );
            }
        }
    }

    /**
     * @Then product :productReference should be assigned to default category
     *
     * @param string $productReference
     */
    public function assertProductAssignedToDefaultCategory(string $productReference)
    {
        $context = $this->getContainer()->get('prestashop.adapter.legacy.context')->getContext();
        $defaultCategoryId = (int) $context->shop->id_category;
        $product = $this->getProductByReference($productReference);
        $productCategories = $product->getCategories();

        foreach ($productCategories as $categoryId) {
            if ((int) $categoryId === $defaultCategoryId) {
                break;
            }

            throw new RuntimeException('Product categories relation does not contain default category');
        }

        if ((int) $product->id_category_default !== $defaultCategoryId) {
            throw new RuntimeException('Default category is not assigned to product');
        }
    }

    /**
     * @Then product :productReference type should be :productType
     *
     * @param string $productReference
     * @param string $productTypeName
     */
    public function assertProductType(string $productReference, string $productTypeName)
    {
        $product = $this->getProductByReference($productReference);
        $productTypeValue = $this->getProductTypeValueByName($productTypeName);

        $isVirtual = $product->is_virtual;
        $isPack = Pack::isPack($product->id);
        $isStandard = !Pack::isPack($product->id) && !$product->is_virtual;

        if ($isVirtual && $productTypeValue === ProductType::TYPE_VIRTUAL) {
            return;
        } elseif ($isPack && $productTypeValue === ProductType::TYPE_PACK) {
            return;
        } elseif ($isStandard && $productTypeValue === ProductType::TYPE_STANDARD) {
            return;
        }

        throw new RuntimeException(sprintf('Product type is not as expected. Expected %s', $productTypeName));
    }

    /**
     * @param string $typeName
     *
     * @return int
     */
    private function getProductTypeValueByName(string $typeName): int
    {
        $typeValueByName = [
            'standard' => ProductType::TYPE_STANDARD,
            'pack' => ProductType::TYPE_PACK,
            'virtual' => ProductType::TYPE_VIRTUAL,
        ];

        if (!array_key_exists($typeName, $typeValueByName)) {
            throw new RuntimeException(sprintf(
                'Product type "%s" does not exist',
                $typeName
            ));
        }

        return $typeValueByName[$typeName];
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

    /**
     * @param string $reference
     *
     * @return Product
     */
    private function getProductByReference(string $reference): Product
    {
        $productId = $this->getSharedStorage()->get($reference);

        return $this->getProductById($productId);
    }

    /**
     * @param int $productId
     *
     * @return Product
     */
    private function getProductById(int $productId): Product
    {
        $product = new Product($productId);

        if (!$product->id) {
            throw new RuntimeException('Product with id "%s" was not found');
        }

        return $product;
    }
}
