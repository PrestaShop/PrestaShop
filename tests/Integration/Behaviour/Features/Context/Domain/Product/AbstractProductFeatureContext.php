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

use DateTimeInterface;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @param string $reference
     *
     * @return ProductForEditing
     */
    protected function getProductForEditing(string $reference): ProductForEditing
    {
        $productId = $this->getSharedStorage()->get($reference);

        return $this->getQueryBus()->handle(new GetProductForEditing(
            $productId
        ));
    }

    /**
     * @param string $productReference
     *
     * @return CustomizationField[]
     */
    protected function getProductCustomizationFields(string $productReference): array
    {
        return $this->getQueryBus()->handle(new GetProductCustomizationFields(
            $this->getSharedStorage()->get($productReference)
        ));
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    protected function assertBoolProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = PrimitiveUtils::castStringBooleanIntoBoolean($data[$propertyName]);
            // Don't cast on purpose, the value should already be typed as bool
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

            // Use assertSame (not assertEquals) to check value AND type
            Assert::assertSame(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );

            // Unset the checked field from array so we can validate they havel all been asserted
            unset($data[$propertyName]);
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    protected function assertStringProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = $data[$propertyName];
            // Don't cast on purpose, the value should already be typed as string
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

            // Use assertSame (not assertEquals) to check value AND type
            Assert::assertSame(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );

            // Unset the checked field from array so we can validate they havel all been asserted
            unset($data[$propertyName]);
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    protected function assertIntegerProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = (int) $data[$propertyName];
            // Don't cast on purpose, the value should already be typed as int
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

            // Use assertSame (not assertEquals) to check value AND type
            Assert::assertSame(
                $expectedValue,
                $actualValue,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $expectedValue, $actualValue)
            );

            // Unset the checked field from array so we can validate they havel all been asserted
            unset($data[$propertyName]);
        }
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param array $data
     * @param string $propertyName
     */
    protected function assertDateProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (isset($data[$propertyName])) {
            $expectedValue = PrimitiveUtils::castElementInType($data[$propertyName], PrimitiveUtils::TYPE_DATETIME);
            $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);
            if (!($actualValue instanceof DateTimeInterface)) {
                throw new RuntimeException(sprintf('Unexpected type %s, expected DateTimeInterface', get_class($actualValue)));
            }

            $formattedExpectedDate = $expectedValue->format('Y-m-d');
            $formattedActualDate = $actualValue->format('Y-m-d');
            Assert::assertSame(
                $formattedExpectedDate,
                $formattedActualDate,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $formattedExpectedDate, $formattedActualDate)
            );

            // Unset the checked field from array so we can validate they havel all been asserted
            unset($data[$propertyName]);
        }
    }

    /**
     * Extracts corresponding field value from ProductForEditing DTO
     *
     * @param ProductForEditing $productForEditing
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function extractValueFromProductForEditing(ProductForEditing $productForEditing, string $propertyName)
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
            'meta_title' => 'productSeoOptions.localizedMetaTitles',
            'meta_description' => 'productSeoOptions.localizedMetaDescriptions',
            'link_rewrite' => 'productSeoOptions.localizedLinkRewrites',
            'redirect_type' => 'productSeoOptions.redirectType',
            'redirect_target' => 'productSeoOptions.redirectTarget',
            'use_advanced_stock_management' => 'stockInformation.useAdvancedStockManagement',
            'depends_on_stock' => 'stockInformation.dependsOnStock',
            'pack_stock_type' => 'stockInformation.packStockType',
            'out_of_stock_type' => 'stockInformation.outOfStockType',
            'quantity' => 'stockInformation.quantity',
            'minimal_quantity' => 'stockInformation.minimalQuantity',
            'location' => 'stockInformation.location',
            'low_stock_threshold' => 'stockInformation.lowStockThreshold',
            'low_stock_alert' => 'stockInformation.lowStockAlert',
            'available_now_labels' => 'stockInformation.localizedAvailableNowLabels',
            'available_later_labels' => 'stockInformation.localizedAvailableLaterLabels',
            'available_date' => 'stockInformation.availableDate',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($productForEditing, $pathsByNames[$propertyName]);
    }
}
