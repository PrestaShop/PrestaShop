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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * This abstract class was introduced during UpdateProductCommand unification process,
 * and which idea is to remove multiple sub-commands and use single UpdateProductCommand instead.
 * This abstract context allows sharing assertions which and some other common methods for both implementations during the transition.
 *
 * @see UpdateProductCommand
 * @see UpdateProductHandlerInterface
 *
 * @todo: need to check if this abstract class is still needed when UpdateProductCommand is fully finished,
 *        because one of the contexts that uses it will be deleted, therefore leaving this abstract class useless.
 */
class AbstractUpdatePricesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @param string $productReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    protected function performAssertPriceFieldsForShops(string $productReference, string $shopReferences, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();

        $shopReferences = explode(',', $shopReferences);
        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get(trim($shopReference));
            $pricesInfo = $this->getProductForEditing(
                $productReference,
                $shopId
            )->getPricesInformation();

            $this->assertPricesInfos($pricesInfo, $data, $shopReference);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $tableNode
     */
    protected function performAssertPriceFields(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        $pricesInfo = $this->getProductForEditing($productReference)->getPricesInformation();

        $this->assertPricesInfos($pricesInfo, $data);
    }

    /**
     * @param ProductPricesInformation $pricesInfo
     * @param array $data
     * @param string|null $shopReference
     */
    protected function assertPricesInfos(ProductPricesInformation $pricesInfo, array $data, string $shopReference = null): void
    {
        $shopErrorMessage = !empty($shopReference) ? sprintf(' for shop %s', $shopReference) : '';
        if (isset($data['on_sale'])) {
            $expectedOnSale = PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']);
            $onSaleInWords = $expectedOnSale ? 'to be on sale' : 'not to be on sale';

            Assert::assertEquals(
                $expectedOnSale,
                $pricesInfo->isOnSale(),
                sprintf('Expected product %s%s', $onSaleInWords, $shopErrorMessage)
            );

            unset($data['on_sale']);
        }

        if (isset($data['unity'])) {
            $expectedUnity = $data['unity'];
            $actualUnity = $pricesInfo->getUnity();

            Assert::assertEquals(
                $expectedUnity,
                $actualUnity,
                sprintf('Tax rules group expected to be "%s", but got "%s"%s', $expectedUnity, $actualUnity, $shopErrorMessage)
            );

            unset($data['unity']);
        }

        $this->assertTaxRulesGroup($data, $pricesInfo, $shopErrorMessage);
        $this->assertNumberPriceFields($data, $pricesInfo, $shopErrorMessage);

        Assert::assertEmpty($data, sprintf('Some provided product price fields haven\'t been asserted%s: %s', $shopErrorMessage, var_export($data, true)));
    }

    /**
     * @param array $data
     * @param ProductPricesInformation $pricesInfo
     * @param string $shopErrorMessage
     */
    private function assertTaxRulesGroup(array &$data, ProductPricesInformation $pricesInfo, string $shopErrorMessage): void
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
        $actualId = $pricesInfo->getTaxRulesGroupId();

        if ($expectedId !== $actualId) {
            throw new RuntimeException(
                sprintf(
                    'Expected tax rules group "%s", but got "%s"%s',
                    $expectedName,
                    (new \TaxRulesGroup($actualId))->name,
                    $shopErrorMessage
                )
            );
        }

        unset($data['tax rules group']);
    }

    /**
     * @param array $expectedPrices
     * @param ProductPricesInformation $actualPrices
     * @param string $shopErrorMessage
     */
    protected function assertNumberPriceFields(array &$expectedPrices, ProductPricesInformation $actualPrices, string $shopErrorMessage)
    {
        $numberPriceFields = [
            'price',
            'price_tax_included',
            'ecotax',
            'ecotax_tax_included',
            'wholesale_price',
            'unit_price',
            'unit_price_tax_included',
            'unit_price_ratio',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberPriceFields as $field) {
            if (isset($expectedPrices[$field])) {
                $expectedNumber = new DecimalNumber((string) $expectedPrices[$field]);
                $actualNumber = $propertyAccessor->getValue($actualPrices, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"%s', $field, $expectedNumber, $actualNumber, $shopErrorMessage)
                    );
                }

                unset($expectedPrices[$field]);
            }
        }
    }
}
