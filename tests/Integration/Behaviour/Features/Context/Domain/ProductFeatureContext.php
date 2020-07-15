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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Cache;
use Context;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags as LocalizedTagsDto;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\PackedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNotesType;
use Product;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\CarrierFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var CarrierFeatureContext
     */
    private $carrierFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->carrierFeatureContext = $scope->getEnvironment()->getContext(CarrierFeatureContext::class);
    }

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
     * @When I update pack :packReference with following product quantities:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function updateProductPack(string $packReference, TableNode $table)
    {
        $data = $table->getRowsHash();

        $products = [];
        foreach ($data as $productReference => $quantity) {
            $products[] = [
                'product_id' => $this->getSharedStorage()->get($productReference),
                'quantity' => (int) $quantity,
            ];
        }

        $packId = $this->getSharedStorage()->get($packReference);

        $this->upsertPack($packId, $products);
    }

    /**
     * @When I clean pack :packReference
     */
    public function cleanPack(string $packReference)
    {
        $packId = $this->getSharedStorage()->get($packReference);

        try {
            $this->getCommandBus()->handle(UpdateProductPackCommand::cleanPack($packId));
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then pack :packReference should contain products with following quantities:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function assertPackContents(string $packReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $packId = $this->getSharedStorage()->get($packReference);
        $packedProducts = $this->getQueryBus()->handle(new GetPackedProducts($packId));
        $notExistingProducts = [];

        foreach ($data as $productReference => $quantity) {
            $expectedQty = (int) $quantity;
            $expectedPackedProductId = $this->getSharedStorage()->get($productReference);
            $foundProduct = false;

            /**
             * @var int
             * @var PackedProduct $packedProduct
             */
            foreach ($packedProducts as $key => $packedProduct) {
                //@todo: check && combination id when asserting combinations.
                if ($packedProduct->getProductId() === $expectedPackedProductId) {
                    $foundProduct = true;
                    Assert::assertEquals(
                        $expectedQty,
                        $packedProduct->getQuantity(),
                        sprintf('Unexpected quantity of packed product "%s"', $productReference)
                    );

                    //unset asserted product to check if there was any excessive actual products after loops
                    unset($packedProducts[$key]);
                    break;
                }
            }

            if (!$foundProduct) {
                $notExistingProducts[$productReference] = $quantity;
            }
        }

        if (!empty($notExistingProducts)) {
            throw new RuntimeException(sprintf(
                'Failed to find following packed products: %s',
                implode(',', array_keys($notExistingProducts))
            ));
        }

        if (!empty($packedProducts)) {
            throw new RuntimeException(sprintf(
                'Following packed products were not expected: %s',
                implode(',', array_map(function ($packedProduct) {
                    return $packedProduct->name;
                }, $packedProducts))
            ));
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
     * @When I update product :productReference options with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductOptions(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductOptionsCommand($productId);
            $this->setUpdateOptionsCommandData($data, $command);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I update product :productReference shipping information with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductShipping(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductShippingCommand($productId);
            $unhandledData = $this->setUpdateShippingCommandData($data, $command);

            Assert::assertEmpty(
                $unhandledData,
                sprintf('Not all provided values handled in scenario. %s', var_export($unhandledData))
            );

            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @param array $data
     * @param UpdateProductShippingCommand $command
     *
     * @return array values that was provided, but wasn't handled
     */
    private function setUpdateShippingCommandData(array $data, UpdateProductShippingCommand $command): array
    {
        $unhandledValues = $data;

        if (isset($data['width'])) {
            $command->setWidth($data['width']);
            unset($unhandledValues['width']);
        }

        if (isset($data['height'])) {
            $command->setHeight($data['height']);
            unset($unhandledValues['height']);
        }

        if (isset($data['depth'])) {
            $command->setDepth($data['depth']);
            unset($unhandledValues['depth']);
        }

        if (isset($data['weight'])) {
            $command->setWeight($data['weight']);
            unset($unhandledValues['weight']);
        }

        if (isset($data['additional_shipping_cost'])) {
            $command->setAdditionalShippingCost($data['additional_shipping_cost']);
            unset($unhandledValues['additional_shipping_cost']);
        }

        if (isset($data['delivery time notes type'])) {
            $command->setDeliveryTimeNotesType(DeliveryTimeNotesType::ALLOWED_TYPES[$data['delivery time notes type']]);
            unset($unhandledValues['delivery time notes type']);
        }

        if (isset($data['delivery time in stock notes'])) {
            $command->setLocalizedDeliveryTimeInStockNotes(
                $this->parseLocalizedArray($data['delivery time in stock notes'])
            );
            unset($unhandledValues['delivery time in stock notes']);
        }

        if (isset($data['delivery time out of stock notes'])) {
            $command->setLocalizedDeliveryTimeOutOfStockNotes(
                $this->parseLocalizedArray($data['delivery time out of stock notes'])
            );
            unset($unhandledValues['delivery time out of stock notes']);
        }

        if (isset($data['carriers'])) {
            $referenceIds = [];
            foreach (PrimitiveUtils::castStringArrayIntoArray($data['carriers']) as $carrierName) {
                $carrier = $this->carrierFeatureContext->loadCarrierByName($carrierName);
                $referenceIds[] = (int) $carrier->id_reference;
            }
            $command->setCarrierReferences($referenceIds);
            unset($unhandledValues['carriers']);
        }

        return $unhandledValues;
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
     * @Then product :productReference should be assigned to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductCategories(string $productReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $productForEditing = $actualCategoryIds = $this->getProductForEditing($productReference);
        $actualCategoryIds = $productForEditing->getCategoriesInformation()->getCategoryIds();
        sort($actualCategoryIds);

        $expectedCategoriesRef = PrimitiveUtils::castStringArrayIntoArray($data['categories']);
        $expectedCategoryIds = array_map(function (string $categoryReference) {
            return $this->getSharedStorage()->get($categoryReference);
        }, $expectedCategoriesRef);
        sort($expectedCategoryIds);

        $expectedDefaultCategoryId = $this->getSharedStorage()->get($data['default category']);
        $actualDefaultCategoryId = $productForEditing->getCategoriesInformation()->getDefaultCategoryId();

        Assert::assertEquals($expectedDefaultCategoryId, $actualDefaultCategoryId, 'Unexpected default category assigned to product');
        Assert::assertEquals($actualCategoryIds, $expectedCategoryIds, 'Unexpected categories assigned to product');
    }

    /**
     * @When I assign product :productReference to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assignToCategoriesIncludingNonExistingOnes(string $productReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $categoryReferences = PrimitiveUtils::castStringArrayIntoArray($data['categories']);

        // this random number is used on purpose to mimic non existing category id
        $nonExistingCategoryId = 50000;
        $categoryIds = [];
        foreach ($categoryReferences as $categoryReference) {
            if ($this->getSharedStorage()->exists($categoryReference)) {
                $categoryIds[] = $this->getSharedStorage()->get($categoryReference);
            } else {
                $categoryIds[] = $nonExistingCategoryId;
                ++$nonExistingCategoryId;
            }
        }

        if ($this->getSharedStorage()->exists($data['default category'])) {
            $defaultCategoryId = $this->getSharedStorage()->get($data['default category']);
        } else {
            $defaultCategoryId = $nonExistingCategoryId;
        }

        $this->assignProductToCategories(
            $this->getSharedStorage()->get($productReference),
            $defaultCategoryId,
            $categoryIds
        );
    }

    /**
     * @Then I should get error that assigning product to categories failed
     */
    public function assertFailedUpdateCategoriesError()
    {
        $this->assertLastErrorIs(
            CannotUpdateProductException::class,
            CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
        );
    }

    /**
     * Product tags differs from other localized properties, because each locale can have an array of tags
     * (whereas common property will have one value per language)
     * This is why it needs some additional parsing
     *
     * @param array $localizedTagStrings key value pairs where key is language id and value is string representation of array separated by comma
     *                                   e.g. [1 => 'hello,goodbye', 2 => 'bonjour,Au revoir']
     * @param ProductForEditing $productForEditing
     */
    private function assertLocalizedTags(array $localizedTagStrings, ProductForEditing $productForEditing)
    {
        $fieldName = 'tags';
        /** @var LocalizedTagsDto[] $actualLocalizedTags */
        $actualLocalizedTagsList = $this->extractValueFromProductForEditing($productForEditing, $fieldName);

        foreach ($localizedTagStrings as $langId => $tagsString) {
            $langIso = Language::getIsoById($langId);

            if (empty($tagsString)) {
                // if tags string is empty, then we should not have any actual value in this language
                /** @var LocalizedTagsDto $actualLocalizedTags */
                foreach ($actualLocalizedTagsList as $actualLocalizedTags) {
                    if ($actualLocalizedTags->getLanguageId() === $langId) {
                        throw new RuntimeException(sprintf(
                                'Expected no tags in %s language, but got "%s"',
                                $langIso,
                                var_export($actualLocalizedTags->getTags(), true))
                        );
                    }
                }

                // if above code passed it means tags in this lang is empty as expected and we can continue
                continue;
            }

            // convert filled tags to array
            $expectedTags = array_map('trim', explode(',', $tagsString));
            $valueInLangExists = false;
            foreach ($actualLocalizedTagsList as $actualLocalizedTags) {
                if ($actualLocalizedTags->getLanguageId() !== $langId) {
                    continue;
                }

                Assert::assertEquals(
                    $expectedTags,
                    $actualLocalizedTags->getTags(),
                    sprintf(
                        'Expected %s in "%s" language was "%s", but got "%s"',
                        $fieldName,
                        $langIso,
                        var_export($expectedTags, true),
                        var_export($actualLocalizedTags->getTags(), true)
                    )
                );
                $valueInLangExists = true;
            }

            // All empty values have ben filtered out above,
            // so if this lang value doesn't exist, it means it didn't meet the expectations
            if (!$valueInLangExists) {
                throw new RuntimeException(sprintf(
                    'Expected localized tags value "%s" is not set in %s language',
                    var_export($expectedTags, true),
                    $langIso
                ));
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
        $this->assertShippingInformation($data, $productForEditing->getShippingInformation());

        // Assertions checking isset() which can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided fields haven\'t been asserted: %s', implode(',', $data)));
    }

    /**
     * @When I update product :productReference tags with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductTags(string $productReference, TableNode $table)
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $data = $table->getRowsHash();

        $localizedTagStrings = $this->parseLocalizedArray($data['tags']);
        $localizedTagsList = [];

        foreach ($localizedTagStrings as $langId => $localizedTagString) {
            $localizedTagsList[$langId] = explode(',', $localizedTagString);
        }

        try {
            $this->getCommandBus()->handle(new UpdateProductTagsCommand($productId, $localizedTagsList));
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
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
        if (isset($data['tax rules group'])) {
            $taxRulesGroupId = (int) TaxRulesGroupFeatureContext::getTaxRulesGroupByName($data['tax rules group'])->id;
            $command->setTaxRulesGroupId($taxRulesGroupId);
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
            $this->lastException = $e;
        }
    }

    /**
     * @When I update product :productReference with following customization fields:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateCustomizationFields(string $productReference, TableNode $table)
    {
        $customizationFields = $table->getColumnsHash();
        $fieldsForUpdate = [];
        $fieldReferences = [];

        foreach ($customizationFields as $customizationField) {
            $addedByModule = isset($customizationField['added by module']) ?
                PrimitiveUtils::castStringBooleanIntoBoolean($customizationField['added by module']) :
                false
            ;
            $fieldReference = $customizationField['reference'];
            $id = $this->getSharedStorage()->exists($fieldReference) ? $this->getSharedStorage()->get($fieldReference) : null;

            $fieldReferences[] = $fieldReference;
            $fieldsForUpdate[] = [
                'id' => $id,
                'type' => $customizationField['type'] === 'file' ? CustomizationFieldType::TYPE_FILE : CustomizationFieldType::TYPE_TEXT,
                'localized_names' => $this->parseLocalizedArray($customizationField['name']),
                'is_required' => PrimitiveUtils::castStringBooleanIntoBoolean($customizationField['is required']),
                'added_by_module' => $addedByModule,
            ];
        }

        $this->updateProductCustomizationFields(
            $productReference,
            $fieldReferences,
            $fieldsForUpdate
        );
    }

    /**
     * @When I update product :productReference customization field name with text containing :nameLength symbols
     *
     * @param string $productReference
     * @param int $nameLength
     */
    public function addCustomizationFieldWithTooLongName(string $productReference, int $nameLength)
    {
        $fieldsForUpdate = [];
        foreach (Language::getIDs() as $langId) {
            $langId = (int) $langId;
            $fieldsForUpdate[] = [
                'id' => null,
                'type' => CustomizationFieldType::TYPE_TEXT,
                'is_required' => false,
                'added_by_module' => false,
                'localized_names' => [
                    $langId => PrimitiveUtils::generateRandomString($nameLength),
                ],
            ];
        }

        try {
            $this->updateProductCustomizationFields($productReference, ['name'], $fieldsForUpdate);
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I delete all customization fields from product :productReference
     *
     * @param string $productReference
     */
    public function updateCustomizationFieldsWithEmptyArray(string $productReference)
    {
        $this->updateProductCustomizationFields($productReference, [], []);
    }

    /**
     * @Then product :productReference should have following customization fields:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertCustomizationFields(string $productReference, TableNode $table)
    {
        $data = $table->getColumnsHash();
        /** @var CustomizationField[] $actualFields */
        $actualFields = $this->getProductCustomizationFields($productReference);
        $notFoundExpectedFields = [];

        foreach ($data as $expectedField) {
            $expectedId = $this->getSharedStorage()->get($expectedField['reference']);
            $foundExpectedField = false;

            foreach ($actualFields as $key => $actualField) {
                if ($expectedId === $actualField->getCustomizationFieldId()) {
                    $foundExpectedField = true;
                    $expectedType = $expectedField['type'] === 'file' ? CustomizationFieldType::TYPE_FILE : CustomizationFieldType::TYPE_TEXT;
                    $expectedLocalizedNames = $this->parseLocalizedArray($expectedField['name']);
                    $expectedRequired = PrimitiveUtils::castStringBooleanIntoBoolean($expectedField['is required']);
                    Assert::assertEquals($expectedType, $actualField->getType(), 'Unexpected customization type');
                    Assert::assertEquals(
                        $expectedLocalizedNames,
                        $actualField->getLocalizedNames(),
                        sprintf('Unexpected product "%s" customization field name', $productReference)
                    );

                    if ($expectedRequired !== $actualField->isRequired()) {
                        throw new RuntimeException(
                            sprintf(
                                'Expected customization field #%d to be %s',
                                $expectedId,
                                $expectedRequired ? 'required' : 'not required'
                            )
                        );
                    }

                    if (isset($expectedField['added by module'])) {
                        $expectedByModule = PrimitiveUtils::castStringBooleanIntoBoolean($expectedField['added by module']);
                        if ($expectedByModule !== $actualField->isAddedByModule()) {
                            throw new RuntimeException(
                                sprintf(
                                    'Expected customization field #%d to be added %s',
                                    $actualField->getCustomizationFieldId(),
                                    $expectedByModule ? 'by module' : 'not by module'
                                )
                            );
                        }
                    }
                    //unset this asserted customization field so we can check if there any left after loop
                    unset($actualFields[$key]);

                    continue;
                }
            }

            if (!$foundExpectedField) {
                $notFoundExpectedFields[] = $expectedField;
            }
        }

        if (!empty($notFoundExpectedFields)) {
            throw new RuntimeException(sprintf(
                'Following customization fields were not found for product %s: %s',
                $productReference,
                var_export($notFoundExpectedFields)
            ));
        }

        if (!empty($actualFields)) {
            throw new RuntimeException(sprintf(
                'Product "%s" contains unexpected customization fields: %s',
                $productReference,
                var_export($actualFields)
            ));
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
            throw new RuntimeException('Product is not assigned to default category');
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
     * @Then /^product "(.+)" should (not be customizable|allow customization|require customization)$/
     *
     * @param string $productReference
     * @param string $customizability
     */
    public function assertCustomizability(string $productReference, string $customizability)
    {
        $customizationOptions = $this->getProductForEditing($productReference)->getCustomizationOptions();

        switch ($customizability) {
            case 'not be customizable':
                Assert::assertTrue(
                    $customizationOptions->isNotCustomizable(),
                    sprintf('Expected product "%s" to be not customizable', $productReference)
                );

                break;
            case 'allow customization':
                Assert::assertTrue(
                    $customizationOptions->allowsCustomization(),
                    sprintf('Expected product "%s" to allow customization', $productReference)
                );

                break;
            case 'require customization':
                Assert::assertTrue(
                    $customizationOptions->requiresCustomization(),
                    sprintf('Expected product "%s" to require customization', $productReference)
                );

                break;
            default:
                throw new RuntimeException(sprintf('Invalid customizability "%s" provided in test scenario', $customizability));
        }
    }

    /**
     * @Then product :productReference should have :expectedCount customizable :customizationType field(s)
     *
     * @param string $productReference
     * @param int $expectedCount
     * @param string $customizationType
     */
    public function assertCustomizationOptions(string $productReference, int $expectedCount, string $customizationType)
    {
        if (!in_array($customizationType, array_keys(CustomizationFieldType::AVAILABLE_TYPES))) {
            throw new RuntimeException(sprintf('Invalid customization type "%s" provided in test scenario', $customizationType));
        }

        $productForEditing = $this->getProductForEditing($productReference);

        if ('file' === $customizationType) {
            Assert::assertEquals(
                $expectedCount,
                $productForEditing->getCustomizationOptions()->getAvailableFileCustomizationsCount(),
                'Unexpected customizable file fields count'
            );
        } else {
            Assert::assertEquals(
                $expectedCount,
                $productForEditing->getCustomizationOptions()->getAvailableTextCustomizationsCount(),
                'Unexpected customizable text fields count'
            );
        }
    }

    /**
     * @Then product :productReference should have no carriers assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoCarriers(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEmpty(
            $productForEditing->getShippingInformation()->getCarrierReferences(),
            sprintf('Expected product "%s" to have no carriers assigned', $productReference)
        );
    }

    /**
     * @Then I should get error that product for packing quantity is invalid
     */
    public function assertPackProductQuantityError()
    {
        $this->assertLastErrorIs(
            ProductPackException::class,
            ProductPackException::INVALID_QUANTITY
        );
    }

    /**
     * @Then I should get error that I cannot add pack into a pack
     */
    public function assertAddingPackToPackError()
    {
        $this->assertLastErrorIs(
            ProductPackException::class,
            ProductPackException::CANNOT_ADD_PACK_INTO_PACK
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
     * @Then I should get error that product customization field name is invalid
     */
    public function assertCustomizationFieldNameError(): void
    {
        $this->assertLastErrorIs(
            CustomizationFieldConstraintException::class,
            CustomizationFieldConstraintException::INVALID_NAME
        );
    }

    /**
     * @param string $productReference
     * @param array $fieldReferences
     * @param array $fieldsForUpdate
     */
    private function updateProductCustomizationFields(string $productReference, array $fieldReferences, array $fieldsForUpdate): void
    {
        try {
            $newCustomizationFields = $this->getCommandBus()->handle(new UpdateProductCustomizationFieldsCommand(
                $this->getSharedStorage()->get($productReference),
                $fieldsForUpdate
            ));

            Assert::assertSameSize(
                $fieldReferences,
                $newCustomizationFields,
                'Cannot set references in shared storage. References and actual customization fields doesn\'t match.'
            );

            /** @var CustomizationField $customizationField */
            foreach ($newCustomizationFields as $key => $customizationField) {
                $this->getSharedStorage()->set($fieldReferences[$key], $customizationField->getCustomizationFieldId());
            }
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @param array $data
     * @param UpdateProductOptionsCommand $command
     */
    private function setUpdateOptionsCommandData(array $data, UpdateProductOptionsCommand $command): void
    {
        if (isset($data['visibility'])) {
            $command->setVisibility($data['visibility']);
        }

        if (isset($data['available_for_order'])) {
            $command->setAvailableForOrder(PrimitiveUtils::castStringBooleanIntoBoolean($data['available_for_order']));
        }

        if (isset($data['online_only'])) {
            $command->setOnlineOnly(PrimitiveUtils::castStringBooleanIntoBoolean($data['online_only']));
        }

        if (isset($data['show_price'])) {
            $command->setShowPrice(PrimitiveUtils::castStringBooleanIntoBoolean($data['show_price']));
        }

        if (isset($data['condition'])) {
            $command->setCondition($data['condition']);
        }

        if (isset($data['isbn'])) {
            $command->setIsbn($data['isbn']);
        }

        if (isset($data['upc'])) {
            $command->setUpc($data['upc']);
        }

        if (isset($data['ean13'])) {
            $command->setEan13($data['ean13']);
        }

        if (isset($data['mpn'])) {
            $command->setMpn($data['mpn']);
        }

        if (isset($data['reference'])) {
            $command->setReference($data['reference']);
        }

        if (isset($data['mpn'])) {
            $command->setMpn($data['mpn']);
        }
    }

    /**
     * @param int $packId
     * @param array $products
     */
    private function upsertPack(int $packId, array $products): void
    {
        try {
            $this->getCommandBus()->handle(UpdateProductPackCommand::upsertPack(
                $packId,
                $products
            ));
        } catch (ProductException $e) {
            $this->lastException = $e;
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
     * @param array $data
     * @param ProductShippingInformation $productShippingInformation
     */
    private function assertShippingInformation(array &$data, ProductShippingInformation $productShippingInformation): void
    {
        if (isset($data['carriers'])) {
            $expectedReferences = [];
            foreach (PrimitiveUtils::castStringArrayIntoArray($data['carriers']) as $carrierName) {
                $carrier = $this->carrierFeatureContext->loadCarrierByName($carrierName);
                $expectedReferences[] = (int) $carrier->id_reference;
            }
            $actualReferences = $productShippingInformation->getCarrierReferences();
            Assert::assertEquals($expectedReferences, $actualReferences, 'Unexpected carrier references in product shipping information');
            unset($data['carriers']);
        }

        $this->assertNumberShippingFields($data, $productShippingInformation);
        $this->assertDeliveryTimeNotes($data, $productShippingInformation);
    }

    /**
     * @param array $expectedValues
     * @param ProductShippingInformation $actualValues
     */
    private function assertNumberShippingFields(array &$expectedValues, ProductShippingInformation $actualValues)
    {
        $numberShippingFields = [
            'width',
            'height',
            'depth',
            'weight',
            'additional_shipping_cost',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberShippingFields as $field) {
            if (isset($expectedValues[$field])) {
                $expectedNumber = new Number((string) $expectedValues[$field]);
                $actualNumber = $propertyAccessor->getValue($actualValues, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"', $field, $expectedNumber, $actualNumber)
                    );
                }

                unset($expectedValues[$field]);
            }
        }
    }

    /**
     * @param array $data
     * @param ProductShippingInformation $productShippingInformation
     */
    private function assertDeliveryTimeNotes(array &$data, ProductShippingInformation $productShippingInformation)
    {
        $notesTypeNamedValues = [
            'none' => DeliveryTimeNotesType::TYPE_NONE,
            'default' => DeliveryTimeNotesType::TYPE_DEFAULT,
            'specific' => DeliveryTimeNotesType::TYPE_SPECIFIC,
        ];

        if (isset($data['delivery time notes type'])) {
            $expectedType = $notesTypeNamedValues[$data['delivery time notes type']];
            $actualType = $productShippingInformation->getDeliveryTimeNotesType();
            Assert::assertEquals($expectedType, $actualType, 'Unexpected delivery time notes type value');

            unset($data['delivery time notes type']);
        }

        if (isset($data['delivery time in stock notes'])) {
            $expectedLocalizedOutOfStockNotes = $this->parseLocalizedArray($data['delivery time in stock notes']);
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeInStockNotes();
            Assert::assertEquals(
                $expectedLocalizedOutOfStockNotes,
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time in stock notes'
            );

            unset($data['delivery time in stock notes']);
        }

        if (isset($data['delivery time out of stock notes'])) {
            $expectedLocalizedOutOfStockNotes = $this->parseLocalizedArray($data['delivery time out of stock notes']);
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeOutOfStockNotes();
            Assert::assertEquals(
                $expectedLocalizedOutOfStockNotes,
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time out of stock notes'
            );

            unset($data['delivery time out of stock notes']);
        }
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
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
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
            'active' => 'active',
            'visibility' => 'options.visibility',
            'available_for_order' => 'options.availableForOrder',
            'online_only' => 'options.onlineOnly',
            'show_price' => 'options.showPrice',
            'condition' => 'options.condition',
            'isbn' => 'options.isbn',
            'upc' => 'options.upc',
            'ean13' => 'options.ean13',
            'mpn' => 'options.mpn',
            'reference' => 'options.reference',
            'tags' => 'options.localizedTags',
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

    /**
     * @param int $productId
     * @param int $defaultCategoryId
     * @param array $categoryIds
     */
    private function assignProductToCategories(int $productId, int $defaultCategoryId, array $categoryIds): void
    {
        try {
            $this->getCommandBus()->handle(new UpdateProductCategoriesCommand(
                $productId,
                $defaultCategoryId,
                $categoryIds
            ));
        } catch (ProductException $e) {
            $this->lastException = $e;
        }
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
