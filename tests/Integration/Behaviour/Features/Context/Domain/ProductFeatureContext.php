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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\AddCustomizationFieldCommand;
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
     * @When I add following customization field to product :productReference:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function addCustomizationField(string $productReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $type = CustomizationSettings::TYPE_TEXT === $data['type'] ? CustomizationSettings::TYPE_TEXT : CustomizationSettings::TYPE_FILE;

        $addedByModule = isset($data['is added by module']) ? PrimitiveUtils::castStringBooleanIntoBoolean($data['is added by module']) : false;
        $deleted = isset($data['is deleted']) ? PrimitiveUtils::castStringBooleanIntoBoolean($data['is deleted']) : false;

        $command = new AddCustomizationFieldCommand(
            $this->getSharedStorage()->get($productReference),
            $type,
            PrimitiveUtils::castStringBooleanIntoBoolean($data['is required']),
            $this->parseLocalizedArray($data['name']),
            $addedByModule,
            $deleted
        );

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->lastException = $e;
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
    private function assertBoolProperty(ProductForEditing $productForEditing, array $data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = PrimitiveUtils::castStringBooleanIntoBoolean($data[$propertyName]);
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);
            Assert::assertEquals(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    private function assertStringProperty(ProductForEditing $productForEditing, array $data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = $data[$propertyName];
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

            Assert::assertEquals(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );
        }
    }

    /**
     * @param array $data
     * @param ProductForEditing $productForEditing
     */
    private function assertTaxRulesGroup(array $data, ProductForEditing $productForEditing)
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
    }

    /**
     * @param array $data
     * @param ProductPricesInformation $pricesInfo
     */
    private function assertPriceFields(array $data, ProductPricesInformation $pricesInfo): void
    {
        if (isset($data['on_sale'])) {
            $expectedOnSale = PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']);
            $onSaleInWords = $expectedOnSale ? 'to be on sale' : 'not to be on sale';

            Assert::assertEquals(
                $expectedOnSale,
                $pricesInfo->isOnSale(),
                sprintf('Expected product %s', $onSaleInWords)
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
}
