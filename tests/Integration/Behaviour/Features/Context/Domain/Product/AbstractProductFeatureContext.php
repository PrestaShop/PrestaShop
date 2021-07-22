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

use Configuration;
use DateTime;
use DateTimeInterface;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Transform url from behat test into a proper one, expected value looks like this:
     *   http://myshop.com/img/p/{image1}-small_default.jpg
     *
     * Where image1 is the reference to the image id in the shared storage, it allows to get the image
     * id and correctly rebuild the url into something like this:
     *  http://myshop.com/img/p/4/5/45-small_default.jpg
     *
     * @param string $imageUrl
     *
     * @return string
     */
    protected function getRealImageUrl(string $imageUrl): string
    {
        // Get image reference which is integrated in image url
        preg_match('_\{(.+)\}_', $imageUrl, $matches);
        $imageReference = $matches[1];

        if ('no_picture' === $imageReference) {
            $defaultIso = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
            $realImageUrl = str_replace(
                '{' . $imageReference . '}',
                $defaultIso . '-default',
                $imageUrl
            );
        } else {
            // Now rebuild the image folder with image id appended
            $imageId = $this->getSharedStorage()->get($imageReference);
            $imageFolder = implode('/', str_split((string) $imageId)) . '/' . $imageId;
            $realImageUrl = str_replace(
                '{' . $imageReference . '}',
                $imageFolder,
                $imageUrl
            );
        }

        return $realImageUrl;
    }

    /**
     * Transform url from behat test into a proper one, expected value looks like this:
     *   http://myshop.com/img/c/{men}.jpg
     *
     * Where men is the reference to the category id in the shared storage, it allows to get the category
     * id and correctly rebuild the url into something like this:
     *  http://myshop.com/img/c/4.jpg
     *
     * @param string $imageUrl
     *
     * @return string
     */
    protected function getRealCategoryImageUrl(string $imageUrl): string
    {
        // Get image reference which is integrated in image url
        preg_match('_\{(.+)\}_', $imageUrl, $matches);
        $categoryReference = $matches[1];

        // Now rebuild the image folder with image id appended
        $categoryId = $this->getSharedStorage()->get($categoryReference);
        $realImageUrl = str_replace(
            '{' . $categoryReference . '}',
            (string) $categoryId,
            $imageUrl
        );

        return $realImageUrl;
    }

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
    protected function assertDateTimeProperty(ProductForEditing $productForEditing, array &$data, string $propertyName): void
    {
        if (!isset($data[$propertyName])) {
            return;
        }

        $actualValue = $this->extractValueFromProductForEditing($productForEditing, $propertyName);

        if ('' === $data[$propertyName]) {
            Assert::assertEquals(
                null,
                $actualValue,
                sprintf('Unexpected available_date. Expected NULL, got "%s"', var_export($actualValue, true))
            );
        } else {
            $expectedDateTime = new DateTime($data[$propertyName]);
            if (!($actualValue instanceof DateTimeInterface)) {
                throw new RuntimeException(sprintf('Unexpected type %s, expected DateTimeInterface', get_class($actualValue)));
            }

            $formattedExpectedDate = $expectedDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $formattedActualDate = $actualValue->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            Assert::assertSame(
                $formattedExpectedDate,
                $formattedActualDate,
                sprintf('Expected %s "%s". Got "%s".', $propertyName, $formattedExpectedDate, $formattedActualDate)
            );
        }

        // Unset the checked field from array so we can validate they havel all been asserted
        unset($data[$propertyName]);
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
            'tags' => 'basicInformation.localizedTags',
            'active' => 'options.active',
            'visibility' => 'options.visibility',
            'available_for_order' => 'options.availableForOrder',
            'online_only' => 'options.onlineOnly',
            'show_price' => 'options.showPrice',
            'condition' => 'options.condition',
            'isbn' => 'details.isbn',
            'upc' => 'details.upc',
            'ean13' => 'details.ean13',
            'mpn' => 'details.mpn',
            'reference' => 'details.reference',
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
            'low_stock_alert' => 'stockInformation.isLowStockAlertEnabled',
            'available_now_labels' => 'stockInformation.localizedAvailableNowLabels',
            'available_later_labels' => 'stockInformation.localizedAvailableLaterLabels',
            'available_date' => 'stockInformation.availableDate',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($productForEditing, $pathsByNames[$propertyName]);
    }
}
