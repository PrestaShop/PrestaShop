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
use Cache;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdatePricesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference prices for shop :shopReference with following information:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function updateProductPricesForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->updatePrices($productReference, $table, $shopConstraint);
    }

    /**
     * @When I update product :productReference prices for all shops with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductPricesForAllShops(string $productReference, TableNode $table): void
    {
        $this->updatePrices($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @When I update product :productReference prices with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductPrices(string $productReference, TableNode $table): void
    {
        $this->updatePrices($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updatePrices(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $table->getRowsHash();
        $command = new UpdateProductPricesCommand(
            $this->getSharedStorage()->get($productReference),
            $shopConstraint
        );

        if (isset($data['price'])) {
            $command->setPrice($data['price']);
        }
        if (isset($data['ecotax'])) {
            $command->setEcotax($data['ecotax']);
        }
        if (isset($data['tax rules group'])) {
            $taxRulesGroupId = (int) TaxRulesGroupFeatureContext::getTaxRulesGroupByName($data['tax rules group'])->id;
            $command->setTaxRulesGroupId($taxRulesGroupId);
            Cache::clean('product_id_tax_rules_group_*');
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
            $this->setLastException($e);
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

        $command = new UpdateProductPricesCommand($productId, ShopConstraint::shop($this->getDefaultShopId()));
        // this id value does not exist, it is used on purpose.
        $command->setTaxRulesGroupId(50000000);

        try {
            $this->getCommandBus()->handle($command);
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following prices information for shops :shopReference:
     *
     * @param string $productReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    public function assertPriceFieldsForShops(string $productReference, string $shopReferences, TableNode $tableNode): void
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
     * @Then product :productReference should have following prices information:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertPriceFields(string $productReference, TableNode $tableNode): void
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
    private function assertPricesInfos(ProductPricesInformation $pricesInfo, array $data, string $shopReference = null): void
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
    private function assertNumberPriceFields(array &$expectedPrices, ProductPricesInformation $actualPrices, string $shopErrorMessage)
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
