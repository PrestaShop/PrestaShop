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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class DeleteProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I delete product :reference
     *
     * @param string $reference
     */
    public function deleteProductFromDefaultShop(string $reference): void
    {
        $this->deleteProduct($reference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I bulk delete following products:
     *
     * @param TableNode $productsList
     */
    public function bulkDeleteProductsFromDefaultShop(TableNode $productsList): void
    {
        $this->bulkDeleteProducts($productsList, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I delete product :productReference from shops :shopReferences
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function deleteProductFromShopCollection(string $productReference, string $shopReferences): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand(
                $this->getSharedStorage()->get($productReference),
                ShopCollection::shops($this->referencesToIds($shopReferences))
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete product :productReference from shop :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function deleteProductFromShop(string $productReference, string $shopReference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand(
                $this->getSharedStorage()->get($productReference),
                ShopConstraint::shop($this->referenceToId($shopReference))
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete product ":productReference" from shop group ":shopGroupReference"
     *
     * @param string $productReference
     * @param string $shopGroupReference
     */
    public function deleteProductFromShopGroup(string $productReference, string $shopGroupReference): void
    {
        $this->deleteProduct(
            $productReference,
            ShopConstraint::shopGroup($this->getSharedStorage()->get($shopGroupReference))
        );
    }

    /**
     * @When I delete product ":productReference" from all shops
     *
     * @param string $productReference
     */
    public function deleteProductFromAllShops(string $productReference): void
    {
        $this->deleteProduct($productReference, ShopConstraint::allShops());
    }

    /**
     * @When I bulk delete following products from shop :shopReference:
     *
     * @param TableNode $productsList
     * @param string $shopReference
     */
    public function bulkDeleteProductsFromShop(TableNode $productsList, string $shopReference): void
    {
        $this->bulkDeleteProductsByShopConstraint($productsList, ShopConstraint::shop($this->referenceToId($shopReference)));
    }

    /**
     * @When I bulk delete following products from shops :shopReferences:
     *
     * @param TableNode $productsList
     * @param string $shopReferences
     */
    public function bulkDeleteProductsFromShopCollection(TableNode $productsList, string $shopReferences): void
    {
        $this->bulkDeleteProductsByShopConstraint($productsList, ShopCollection::shops($this->referencesToIds($shopReferences)));
    }

    /**
     * @When I bulk delete following products from shop group :shopGroupReference:
     *
     * @param TableNode $productsList
     * @param string $shopGroupReference
     */
    public function bulkDeleteProductsFromShopGroup(TableNode $productsList, string $shopGroupReference): void
    {
        $this->bulkDeleteProductsByShopConstraint($productsList, ShopConstraint::shopGroup($this->referenceToId($shopGroupReference)));
    }

    /**
     * @When I bulk delete following products from all shops:
     *
     * @param TableNode $productsList
     */
    public function bulkDeleteProductsFromAllShops(TableNode $productsList): void
    {
        $this->bulkDeleteProductsByShopConstraint($productsList, ShopConstraint::allShops());
    }

    private function bulkDeleteProductsByShopConstraint(TableNode $productsList, ShopConstraint $shopConstraint): void
    {
        $productIds = [];
        foreach ($productsList->getColumnsHash() as $productInfo) {
            $productIds[] = $this->getSharedStorage()->get($productInfo['reference']);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteProductCommand(
                $productIds,
                $shopConstraint
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    private function deleteProduct(string $reference, ShopConstraint $shopConstraint): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand(
                $this->getSharedStorage()->get($reference),
                $shopConstraint
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    private function bulkDeleteProducts(TableNode $productsList, ShopConstraint $shopConstraint): void
    {
        $productIds = [];
        foreach ($productsList->getColumnsHash() as $productInfo) {
            $productIds[] = $this->getSharedStorage()->get($productInfo['reference']);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteProductCommand($productIds, $shopConstraint));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
