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

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\CopyProductToShop;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class ProductShopFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference is not associated to shop :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function checkNoShopAssociation(string $productReference, string $shopReference): void
    {
        $shopId = $this->getSharedStorage()->get($shopReference);

        $caughtException = null;
        try {
            $this->getProductForEditing($productReference, $shopId);
        } catch (ShopAssociationNotFound $e) {
            $caughtException = $e;
        }

        Assert::assertNotNull($caughtException);
    }

    /**
     * @Then product :productReference is associated to shop :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function checkShopAssociation(string $productReference, string $shopReference): void
    {
        $shopId = $this->getSharedStorage()->get($shopReference);

        $caughtException = null;
        try {
            $this->getProductForEditing($productReference, $shopId);
        } catch (ShopAssociationNotFound $e) {
            $caughtException = $e;
        }

        Assert::assertNull($caughtException);
    }

    /**
     * @Then default shop for product :productReference is :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function checkDefaultShop(string $productReference, string $shopReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $shopId = $this->getSharedStorage()->get($shopReference);

        /** @var ProductMultiShopRepository $productRepository */
        $productRepository = CommonFeatureContext::getContainer()->get('prestashop.adapter.product.repository.product_multi_shop_repository');
        $defaultShopId = $productRepository->getProductDefaultShopId(new ProductId($productId));
        Assert::assertEquals($shopId, $defaultShopId->getValue());
    }

    /**
     * @When I copy product :productReference from shop :shopSourceReference to shop :shopTargetReference
     *
     * @param string $productReference
     * @param string $shopSourceReference
     * @param string $shopTargetReference
     */
    public function copyProductToShop(string $productReference, string $shopSourceReference, string $shopTargetReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $shopSourceId = $this->getSharedStorage()->get($shopSourceReference);
        $shopTargetId = $this->getSharedStorage()->get($shopTargetReference);

        $this->getCommandBus()->handle(new CopyProductToShop(
            $productId,
            $shopSourceId,
            $shopTargetId
        ));
    }
}
