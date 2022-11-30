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
use Cache;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product ":productReference" for all shops with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductForAllShops(string $productReference, TableNode $table): void
    {
        $shopConstraint = ShopConstraint::allShops();
        $this->updateProduct($productReference, $table, $shopConstraint);
    }

    /**
     * @When I update product ":productReference" with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductForDefaultShop(string $productReference, TableNode $table): void
    {
        $shopConstraint = ShopConstraint::shop($this->getDefaultShopId());
        $this->updateProduct($productReference, $table, $shopConstraint);
    }

    /**
     * @When I update product ":productReference" for shop :shopReference with following values:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->updateProduct($productReference, $table, $shopConstraint);
    }

    /**
     * @When /^I update product "([^"]*)" name \(not using commands\) with following localized values:$/
     *
     * @param string $productReference
     * @param TableNode $table
     *
     * @return void
     */
    public function updateProductName(string $productReference, TableNode $table): void
    {
        $this->updateProductNameManually($productReference, $table);
    }

    /**
     * @When I update product :productReference prices and apply non-existing tax rules group
     *
     * @param string $productReference
     */
    public function updateTaxRulesGroupWithNonExistingGroup(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);

        $command = new UpdateProductCommand($productId, ShopConstraint::shop($this->getDefaultShopId()));
        // this id value does not exist, it is used on purpose.
        $command->setTaxRulesGroupId(50000000);

        try {
            $this->getCommandBus()->handle($command);
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I assign non existing manufacturer to product :productReference
     *
     * @param string $productReference
     */
    public function updateOptionsWithNonExistingManufacturer(string $productReference): void
    {
        // intentional. Mimics id of non-existing manufacturer
        $nonExistingId = 50000;

        try {
            $command = new UpdateProductCommand(
                $this->getSharedStorage()->get($productReference),
                ShopConstraint::shop($this->getDefaultShopId())
            );
            $command->setManufacturerId($nonExistingId);
            $this->getCommandBus()->handle($command);
        } catch (ManufacturerException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateProduct(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        try {
            $command = $this->buildUpdateProductCommand($productReference, $table, $shopConstraint);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     *
     * @return UpdateProductCommand
     */
    private function buildUpdateProductCommand(string $productReference, TableNode $table, ShopConstraint $shopConstraint): UpdateProductCommand
    {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);
        $command = new UpdateProductCommand($productId, $shopConstraint);

        // basic info
        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }
        if (isset($data['description_short'])) {
            $command->setLocalizedShortDescriptions($data['description_short']);
        }
        // details
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
        // options
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
        if (isset($data['show_condition'])) {
            $command->setShowCondition(PrimitiveUtils::castStringBooleanIntoBoolean($data['show_condition']));
        }
        if (isset($data['manufacturer'])) {
            $command->setManufacturerId($this->getManufacturerId($data['manufacturer']));
        }
        // prices
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

        return $command;
    }

    /**
     * @todo: double check if its still needed when we use single command for update
     *
     * This method is created just for specific cases when product name needs to be updated
     * using legacy object model, but not cqrs commands, to avoid some side effects while testing.
     * For example when testing how cqrs command auto-fills link_rewrite in certain cases.
     */
    private function updateProductNameManually(string $productReference, TableNode $table): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId, true);
        $product->name = $this->localizeByRows($table)['name'];

        $product->update();
    }
}
