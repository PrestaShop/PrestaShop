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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class ProductShopFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference is not associated to shop(s) :shopReferences
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function checkNoShopAssociation(string $productReference, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $caughtException = null;
            try {
                $this->getProductForEditing($productReference, $shopId);
            } catch (ProductShopAssociationNotFoundException $e) {
                $caughtException = $e;
            }

            Assert::assertNotNull($caughtException);
        }
    }

    /**
     * @Then product :productReference is associated to shop(s) :shopReferences
     *
     * @param string $productReference
     * @param string $shopReferences
     */
    public function checkShopAssociation(string $productReference, string $shopReferences): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $caughtException = null;
            try {
                $this->getProductForEditing($productReference, $shopId);
            } catch (ProductShopAssociationNotFoundException $e) {
                $caughtException = $e;
            }

            Assert::assertNull($caughtException);
        }
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

        /** @var ProductRepository $productRepository */
        $productRepository = CommonFeatureContext::getContainer()->get(ProductRepository::class);
        $defaultShopId = $productRepository->getProductDefaultShopId(new ProductId($productId));
        Assert::assertEquals($shopId, $defaultShopId->getValue());
    }

    /**
     * @When I set following shops for product ":productReference":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function setProductShops(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();

        try {
            $this->getCommandBus()->handle(new SetProductShopsCommand(
                $this->getSharedStorage()->get($productReference),
                $this->getSharedStorage()->get($data['source shop']),
                $this->referencesToIds($data['shops'])
            ));
        } catch (InvalidProductShopAssociationException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that I cannot unassociate product from all shops
     */
    public function assertLastExceptionIsEmptyProductShopAssociation(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::EMPTY_SHOPS_ASSOCIATION
        );
    }

    /**
     * @Then I should get error that I cannot unassociate product from source shop
     */
    public function assertLastExceptionIsSourceShopMissingInShopAssociation(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::SOURCE_SHOP_MISSING_IN_SHOP_ASSOCIATION
        );
    }

    /**
     * @Then I should get error that source shop is not associated to product
     */
    public function assertLastExceptionIsSourceShopIsNotAssociated(): void
    {
        $this->assertLastErrorIs(
            InvalidProductShopAssociationException::class,
            InvalidProductShopAssociationException::SOURCE_SHOP_NOT_ASSOCIATED
        );
    }
}
