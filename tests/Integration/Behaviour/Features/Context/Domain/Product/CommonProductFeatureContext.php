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
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\CombinationDetails;
use Tests\Integration\Behaviour\Features\Context\Util\ProductCombinationFactory;
use Tests\Integration\Behaviour\Features\Transform\LocalizedArrayTransformContext;

class CommonProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Given product :productReference has following combinations:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function addCombinationsToProduct(string $productReference, TableNode $tableNode): void
    {
        $details = $tableNode->getColumnsHash();
        $combinationsDetails = [];

        foreach ($details as $combination) {
            $combinationsDetails[] = new CombinationDetails(
                $combination['reference'],
                (int) $combination['quantity'],
                explode(';', $combination['attributes'])
            );
        }

        $combinations = ProductCombinationFactory::makeCombinations(
            $this->getSharedStorage()->get($productReference),
            $combinationsDetails
        );

        foreach ($combinations as $combination) {
            $this->getSharedStorage()->set($combination->reference, (int) $combination->id);
        }

        // Product class has a lot of cache that is set as soon as the product is created, including for prices
        // which are cached for each combinations. Since it was cached when the combinations did not exist we need
        // to clear it so that the newly created combinations' prices are correctly computed next time they are needed.
        Product::resetStaticCache();
    }

    /**
     * @Then /^product "(.+)" localized "(.+)" should be:$/
     * @Given /^product "(.+)" localized "(.+)" is:$/
     *
     * localizedValues transformation handled by @see LocalizedArrayTransformContext
     *
     * @param string $productReference
     * @param string $fieldName
     * @param array $expectedLocalizedValues
     */
    public function assertLocalizedProperty(string $productReference, string $fieldName, array $expectedLocalizedValues): void
    {
        $productForEditing = $this->getProductForEditing($productReference);

        if ('tags' === $fieldName) {
            UpdateTagsFeatureContext::assertLocalizedTags(
                $expectedLocalizedValues,
                $this->extractValueFromProductForEditing($productForEditing, $fieldName)
            );

            return;
        }

        $htmlEncodedProperties = ['description', 'description_short'];

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

            $actualValue = in_array($fieldName, $htmlEncodedProperties) ?
                html_entity_decode($actualValues[$langId]) :
                $actualValues[$langId]
            ;

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
     * @Then product :reference should not exist anymore
     *
     * @param string $reference
     */
    public function assertProductDoesNotExistAnymore(string $reference): void
    {
        try {
            $this->getProductForEditing($reference);
            throw new RuntimeException(sprintf('Product "%s" was not expected to exist, but it was found', $reference));
        } catch (ProductNotFoundException $e) {
            // intentional. Means product is not found and test should pass
        }
    }

    /**
     * @Then product :productReference type should be :productType
     *
     * @param string $productReference
     * @param string $productTypeName
     */
    public function assertProductType(string $productReference, string $productTypeName): void
    {
        $editableProduct = $this->getProductForEditing($productReference);
        Assert::assertEquals(
            $productTypeName,
            $editableProduct->getType(),
            sprintf(
                'Product type is not as expected. Expected %s but got %s instead',
                $productTypeName,
                $editableProduct->getType()
            )
        );
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);
        Assert::assertEquals($productTypeName === ProductType::TYPE_VIRTUAL, (bool) $product->is_virtual);
        // cache_is_pack is automatically updated by legacy code when removing all pack items so it's not worth testing it for now
        // Assert::assertEquals($productTypeName === ProductType::TYPE_PACK, (bool) $product->cache_is_pack);
        if ($productTypeName !== ProductType::TYPE_COMBINATIONS) {
            Assert::assertEquals(0, $product->cache_default_attribute);
        }
    }

    /**
     * @Then product :productReference persisted type should be :productType
     *
     * @param string $productReference
     * @param string $productTypeName
     */
    public function assertPersistedProductType(string $productReference, string $productTypeName): void
    {
        if ('undefined' === $productTypeName) {
            $productTypeName = ProductType::TYPE_UNDEFINED;
        }
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);
        Assert::assertEquals($productTypeName, $product->product_type);
    }

    /**
     * @Then product :productReference dynamic type should be :productType
     *
     * @param string $productReference
     * @param string $productTypeName
     */
    public function assertDynamicProductType(string $productReference, string $productTypeName): void
    {
        if ('undefined' === $productTypeName) {
            $productTypeName = ProductType::TYPE_UNDEFINED;
        }
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);
        Assert::assertEquals($productTypeName, $product->getDynamicProductType());
    }

    /**
     * @Then I should get error that product :fieldName is invalid
     *
     * @param string $fieldName
     */
    public function assertConstraintError(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ProductConstraintException::class,
            $this->getConstraintErrorCode($fieldName)
        );
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
            'tag' => ProductConstraintException::INVALID_TAG,
            'width' => ProductConstraintException::INVALID_WIDTH,
            'height' => ProductConstraintException::INVALID_HEIGHT,
            'depth' => ProductConstraintException::INVALID_DEPTH,
            'weight' => ProductConstraintException::INVALID_WEIGHT,
            'additional_shipping_cost' => ProductConstraintException::INVALID_ADDITIONAL_SHIPPING_COST,
            'delivery_in_stock' => ProductConstraintException::INVALID_DELIVERY_TIME_IN_STOCK_NOTES,
            'delivery_out_stock' => ProductConstraintException::INVALID_DELIVERY_TIME_OUT_OF_STOCK_NOTES,
            'redirect_target' => ProductConstraintException::INVALID_REDIRECT_TARGET,
            'redirect_type' => ProductConstraintException::INVALID_REDIRECT_TYPE,
            'meta_title' => ProductConstraintException::INVALID_META_TITLE,
            'meta_description' => ProductConstraintException::INVALID_META_DESCRIPTION,
            'link_rewrite' => ProductConstraintException::INVALID_LINK_REWRITE,
            'minimal_quantity' => ProductConstraintException::INVALID_MINIMAL_QUANTITY,
            'available_now_labels' => ProductConstraintException::INVALID_AVAILABLE_NOW,
            'available_later_labels' => ProductConstraintException::INVALID_AVAILABLE_LATER,
            'available_date' => ProductConstraintException::INVALID_AVAILABLE_DATE,
            'low_stock_threshold' => ProductConstraintException::INVALID_LOW_STOCK_THRESHOLD,
            'low_stock_alert' => ProductConstraintException::INVALID_LOW_STOCK_ALERT,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    /**
     * @Then product :productReference should be indexed
     *
     * @param string $productReference
     */
    public function assertIsIndexed(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);
        Assert::assertSame(
            1,
            (int) $product->indexed,
            sprintf('Unexpected indexed field value %s for product "%s"', $product->indexed, $productReference)
        );
    }

    /**
     * @Then product :productReference should not be indexed
     *
     * @param string $productReference
     */
    public function assertIsNotIndexed(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);
        Assert::assertSame(
            0,
            (int) $product->indexed,
            sprintf('Unexpected indexed field value %s for product "%s"', $product->indexed, $productReference)
        );
    }
}
