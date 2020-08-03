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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Cache;
use Context;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use Product;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ProductFeatureContext extends AbstractProductFeatureContext
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

        if ('tags' === $fieldName) {
            $this->assertLocalizedTags($expectedLocalizedValues, $productForEditing);

            return;
        }

        foreach ($expectedLocalizedValues as $langId => $expectedValue) {
            $actualValues = $this->extractValueFromProductForEditing($productForEditing, $fieldName);
            $langIso = Language::getIsoById($langId);

            if (!isset($actualValues[$langId])) {
                throw new RuntimeException(sprintf(
                    'Expected localized %s value is not set in %s language',
                    $fieldName,
                    $langIso
                ));
            }

            $actualValue = $actualValues[$langId];

            if ($expectedValue !== $actualValue) {
                throw new RuntimeException(
                    sprintf(
                        'Expected %s in "%s" language was "%s", but got "%s"',
                        $fieldName,
                        $langIso,
                        var_export($expectedValue, true),
                        var_export($actualValue, true)
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

        $this->assertBoolProperty($productForEditing, $data, 'available_for_order');
        $this->assertBoolProperty($productForEditing, $data, 'online_only');
        $this->assertBoolProperty($productForEditing, $data, 'show_price');
        $this->assertBoolProperty($productForEditing, $data, 'active');
        $this->assertStringProperty($productForEditing, $data, 'visibility');
        $this->assertStringProperty($productForEditing, $data, 'condition');
        $this->assertStringProperty($productForEditing, $data, 'isbn');
        $this->assertStringProperty($productForEditing, $data, 'upc');
        $this->assertStringProperty($productForEditing, $data, 'ean13');
        $this->assertStringProperty($productForEditing, $data, 'mpn');
        $this->assertStringProperty($productForEditing, $data, 'reference');

        $this->assertTaxRulesGroup($data, $productForEditing);
        $this->assertPriceFields($data, $productForEditing->getPricesInformation());

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided fields haven\'t been asserted: %s', implode(',', $data)));
    }

    /**
     * @When I update product :productReference prices and apply non-existing tax rules group
     *
     * @param string $productReference
     */
    public function updateTaxRulesGroupWithNonExistingGroup(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);

        $command = new UpdateProductPricesCommand($productId);
        // this id value does not exist, it is used on purpose.
        $command->setTaxRulesGroupId(50000000);

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
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
        Assert::assertEquals(
            $productTypeName,
            $editableProduct->getBasicInformation()->getType()->getValue(),
            sprintf(
                'Product type is not as expected. Expected %s but got %s instead',
                $productTypeName,
                $editableProduct->getBasicInformation()->getType()->getValue()
            )
        );
    }

    /**
     * @Then I should get error that product :fieldName is invalid
     */
    public function assertConstraintError(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            $this->getConstraintErrorCode($fieldName)
        );
    }

    /**
     * @param array $data
     * @param ProductPricesInformation $pricesInfo
     */
    private function assertPriceFields(array &$data, ProductPricesInformation $pricesInfo): void
    {
        if (isset($data['on_sale'])) {
            $expectedOnSale = PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']);
            $onSaleInWords = $expectedOnSale ? 'to be on sale' : 'not to be on sale';

            Assert::assertEquals(
                $expectedOnSale,
                $pricesInfo->isOnSale(),
                sprintf('Expected product %s', $onSaleInWords)
            );

            unset($data['on_sale']);
        }

        if (isset($data['unity'])) {
            $expectedUnity = $data['unity'];
            $actualUnity = $pricesInfo->getUnity();

            Assert::assertEquals(
                $expectedUnity,
                $actualUnity,
                sprintf('Tax rules group expected to be "%s", but got "%s"', $expectedUnity, $actualUnity)
            );

            unset($data['unity']);
        }

        $this->assertNumberPriceFields($data, $pricesInfo);
    }

    /**
     * @param array $expectedPrices
     * @param ProductPricesInformation $actualPrices
     */
    private function assertNumberPriceFields(array &$expectedPrices, ProductPricesInformation $actualPrices)
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

                unset($expectedPrices[$field]);
            }
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    private function assertBoolProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = PrimitiveUtils::castStringBooleanIntoBoolean($data[$propertyName]);
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);
            Assert::assertEquals(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );

            unset($data[$propertyName]);
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    private function assertStringProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = $data[$propertyName];
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

            Assert::assertEquals(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );

            unset($data[$propertyName]);
        }
    }

    /**
     * @param array $data
     * @param ProductForEditing $productForEditing
     */
    private function assertTaxRulesGroup(array &$data, ProductForEditing $productForEditing)
    {
        if (!isset($data['tax rules group'])) {
            return;
        }

        $expectedName = $data['tax rules group'];

        if ('' === $expectedName) {
            $expectedId = 0;
        } else {
            $expectedId = (int) TaxRulesGroupFeatureContext::getTaxRulesGroupByName($expectedName)->id;
        }
        $actualId = $productForEditing->getPricesInformation()->getTaxRulesGroupId();

        if ($expectedId !== $actualId) {
            throw new RuntimeException(
                sprintf(
                    'Expected tax rules group "%s", but got "%s"',
                    $expectedName,
                    TaxRulesGroupFeatureContext::getTaxRulesGroupByName($actualId)->name
                )
            );
        }

        unset($data['tax rules group']);
    }

    /**
     * @param string $fieldName
     *
     * @return int
     */
    private function getConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'type' => ProductConstraintException::INVALID_PRODUCT_TYPE,
            'name' => ProductConstraintException::INVALID_NAME,
            'description' => ProductConstraintException::INVALID_DESCRIPTION,
            'description_short' => ProductConstraintException::INVALID_SHORT_DESCRIPTION,
            'visibility' => ProductConstraintException::INVALID_VISIBILITY,
            'condition' => ProductConstraintException::INVALID_CONDITION,
            'isbn' => ProductConstraintException::INVALID_ISBN,
            'upc' => ProductConstraintException::INVALID_UPC,
            'ean13' => ProductConstraintException::INVALID_EAN_13,
            'mpn' => ProductConstraintException::INVALID_MPN,
            'reference' => ProductConstraintException::INVALID_REFERENCE,
            'price' => ProductConstraintException::INVALID_PRICE,
            'ecotax' => ProductConstraintException::INVALID_ECOTAX,
            'wholesale_price' => ProductConstraintException::INVALID_WHOLESALE_PRICE,
            'unit_price' => ProductConstraintException::INVALID_UNIT_PRICE,
            'tax rules group' => ProductConstraintException::INVALID_TAX_RULES_GROUP_ID,
            'tag' => ProductConstraintException::INVALID_TAG,
            'width' => ProductConstraintException::INVALID_WIDTH,
            'height' => ProductConstraintException::INVALID_HEIGHT,
            'depth' => ProductConstraintException::INVALID_DEPTH,
            'weight' => ProductConstraintException::INVALID_WEIGHT,
            'additional_shipping_cost' => ProductConstraintException::INVALID_ADDITIONAL_SHIPPING_COST,
            'delivery_in_stock' => ProductConstraintException::INVALID_DELIVERY_TIME_IN_STOCK_NOTES,
            'delivery_out_stock' => ProductConstraintException::INVALID_DELIVERY_TIME_OUT_OF_STOCK_NOTES,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
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
     * @param string $productReference
     *
     * @return CustomizationField[]
     */
    private function getProductCustomizationFields(string $productReference): array
    {
        return $this->getQueryBus()->handle(new GetProductCustomizationFields(
            $this->getSharedStorage()->get($productReference)
        ));
    }
}
