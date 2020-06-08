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
use Context;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use Product;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
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
     * @When I add product :productReference with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function addProduct(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            $productId = $this->getCommandBus()->handle(new AddProductCommand(
                $this->parseLocalizedArray($data['name']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['is_virtual'])
            ));

            $this->getSharedStorage()->set($productReference, $productId->getValue());
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I update product :productReference basic information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductBasicInfo(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);
        $command = new UpdateProductBasicInformationCommand($productId);

        if (isset($data['name'])) {
            $command->setLocalizedNames($this->parseLocalizedArray($data['name']));
        }

        if (isset($data['is_virtual'])) {
            $command->setVirtual(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_virtual']));
        }

        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($this->parseLocalizedArray($data['description']));
        }

        if (isset($data['description_short'])) {
            $command->setLocalizedShortDescriptions($this->parseLocalizedArray($data['description_short']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then /^product "(.+)" localized "(.+)" should be "(.+)"$/
     * @Given /^product "(.+)" localized "(.+)" is "(.+)"$/
     *
     * @param string $productReference
     * @param string $fieldName
     * @param string $localizedValues
     */
    public function assertLocalizedProperty(string $productReference, string $fieldName, string $localizedValues)
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $expectedLocalizedValues = $this->parseLocalizedArray($localizedValues);

        foreach ($expectedLocalizedValues as $langId => $expectedValue) {
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $fieldName)[$langId];

            if ($expectedValue !== $actualValue) {
                $langIso = Language::getIsoById($langId);

                throw new RuntimeException(
                    sprintf(
                        'Expected %s in "%s" language was "%s", but got "%s"',
                        $fieldName,
                        $langIso,
                        $expectedValue,
                        $actualValue
                    )
                );
            }
        }
    }

    /**
     * @Then product :productReference should have following values:
     * @Then product :productReference has following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductFields(string $productReference, TableNode $table)
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $data = $table->getRowsHash();

        if (isset($data['active'])) {
            $status = PrimitiveUtils::castStringBooleanIntoBoolean($data['active']);
            $statusInWords = $status ? 'enabled' : 'disabled';

            if ((bool) $productForEditing->isActive() !== $status) {
                throw new RuntimeException(sprintf('Product expected to be %s', $statusInWords));
            }
        }

        $this->assertPriceFields($data, $productForEditing->getPricesInformation());
    }

    /**
     * @When I update product :productReference prices with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductPrices(string $productReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $command = new UpdateProductPricesCommand($this->getSharedStorage()->get($productReference));

        if (isset($data['price'])) {
            $command->setPrice($data['price']);
        }
        if (isset($data['ecotax'])) {
            $command->setEcotax($data['ecotax']);
        }
        if (isset($data['tax_rules_group_id'])) {
            $command->setTaxRulesGroupId($data['tax_rules_group_id']);
        }
        if (isset($data['on_sale'])) {
            $command->setOnSale(PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']));
        }
        if (isset($data['wholesale_price'])) {
            $command->setWholesalePrice($data['wholesale_price']);
        }
        if (isset($data['unit_price'])) {
            $command->setUnitPrice($data['unit_price']);
        }
        if (isset($data['unity'])) {
            $command->setUnity($data['unity']);
        }

        try {
            $this->getQueryBus()->handle($command);
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @param array $data
     * @param ProductPricesInformation $pricesInfo
     */
    private function assertPriceFields(array $data, ProductPricesInformation $pricesInfo): void
    {
        if (isset($data['on_sale'])) {
            $expectedOnSale = PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']);
            $onSaleInWords = $expectedOnSale ? 'to be on sale' : 'to be not on sale';

            Assert::assertEquals(
                $expectedOnSale,
                $pricesInfo->isOnSale(),
                sprintf('Expected product %s', $onSaleInWords)
            );
        }

        if (isset($data['tax_rules_group_id'])) {
            $expectedGroup = (int) $data['tax_rules_group_id'];
            $actualGroup = $pricesInfo->getTaxRulesGroupId();

            Assert::assertEquals(
                $expectedGroup,
                $actualGroup,
                sprintf('Tax rules group expected to be "%s", but got "%s"', $expectedGroup, $actualGroup)
            );
        }

        if (isset($data['unity'])) {
            $expectedUnity = $data['unity'];
            $actualUnity = $pricesInfo->getUnity();

            Assert::assertEquals(
                $expectedUnity,
                $actualUnity,
                sprintf('Tax rules group expected to be "%s", but got "%s"', $expectedUnity, $actualUnity)
            );
        }

        $this->assertNumberPriceFields($data, $pricesInfo);
    }

    /**
     * @param array $expectedPrices
     * @param ProductPricesInformation $actualPrices
     */
    private function assertNumberPriceFields(array $expectedPrices, ProductPricesInformation $actualPrices)
    {
        $numberPriceFields = [
            'price',
            'ecotax',
            'wholesale_price',
            'unit_price',
            'unit_price_ratio',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberPriceFields as $field) {
            if (isset($expectedPrices[$field])) {
                $expectedNumber = new Number((string) $expectedPrices[$field]);
                $actualNumber = $propertyAccessor->getValue($actualPrices, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"', $field, $expectedNumber, $actualNumber)
                    );
                }
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

        $productForEditing = $this->getProductForEditing($productReference);
        $productCategoriesInfo = $productForEditing->getCategoriesInformation();

        $belongsToDefaultCategory = false;
        foreach ($productCategoriesInfo->getCategoryIds() as $categoryId) {
            if ($categoryId === $defaultCategoryId) {
                $belongsToDefaultCategory = true;

                break;
            }
        }

        if ($productCategoriesInfo->getDefaultCategoryId() !== $defaultCategoryId || !$belongsToDefaultCategory) {
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
        $editableProduct = $this->getProductForEditing($productReference);
        if ($productTypeName !== $editableProduct->getBasicInformation()->getType()->getValue()) {
            throw new RuntimeException(
                sprintf(
                    'Product type is not as expected. Expected %s but go %s instead',
                    $productTypeName,
                    $editableProduct->getBasicInformation()->getType()->getValue()
                )
            );
        }
    }

    /**
     * @Then I should get error that product name is invalid
     */
    public function assertLastErrorIsInvalidNameConstraint()
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            ProductConstraintException::INVALID_NAME
        );
    }

    /**
     * @Then I should get error that product type is invalid
     */
    public function assertLastErrorIsInvalidTypeConstraint()
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            ProductConstraintException::INVALID_PRODUCT_TYPE
        );
    }

    /**
     * @Then I should get error that product price is invalid
     */
    public function assertLastErrorIsInvalidPriceConstraint()
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            ProductConstraintException::INVALID_PRICE
        );
    }

    /**
     * @Then I should get error that product description is invalid
     */
    public function assertLastErrorIsInvalidDescriptionConstraint()
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            ProductConstraintException::INVALID_DESCRIPTION
        );
    }

    /**
     * @Then I should get error that product short description is invalid
     */
    public function assertLastErrorIsInvalidShortDescriptionConstraint()
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            ProductConstraintException::INVALID_SHORT_DESCRIPTION
        );
    }

    /**
     * Extracts corresponding field value from ProductForEditing DTO
     *
     * @param ProductForEditing $productForEditing
     * @param string $propertyName
     *
     * @return mixed
     */
    private function extractValueFromProductForEditing(ProductForEditing $productForEditing, string $propertyName)
    {
        $pathsByNames = [
            'name' => 'basicInformation.localizedNames',
            'description' => 'basicInformation.localizedDescriptions',
            'description_short' => 'basicInformation.localizedShortDescriptions',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($productForEditing, $pathsByNames[$propertyName]);
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
     * @return ProductForEditing
     */
    private function getProductForEditing(string $reference): ProductForEditing
    {
        $productId = $this->getSharedStorage()->get($reference);

        return $this->getQueryBus()->handle(new GetProductForEditing(
            $productId
        ));
    }
}
