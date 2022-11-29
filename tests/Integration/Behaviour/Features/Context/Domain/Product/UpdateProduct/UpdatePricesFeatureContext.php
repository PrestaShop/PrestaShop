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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Context for updating product prices by using UpdateProductCommand
 *
 * @see UpdateProductCommand
 */
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
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updatePrices(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $data = $table->getRowsHash();
        $command = new UpdateProductCommand(
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
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
