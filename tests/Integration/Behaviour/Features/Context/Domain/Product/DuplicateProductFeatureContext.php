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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class DuplicateProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I duplicate product :productReference to a :newProductReference
     *
     * @param string $productReference
     * @param string $newProductReference
     */
    public function duplicateForDefaultShop(string $productReference, string $newProductReference): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->getDefaultShopId())
        ));

        $this->getSharedStorage()->set($newProductReference, $newProductId->getValue());
    }

    /**
     * @When I duplicate product :productReference to a :newProductReference for shop :shopReference
     *
     * @param string $productReference
     * @param string $newProductReference
     * @param string $shopReference
     */
    public function duplicateForShop(string $productReference, string $newProductReference, string $shopReference): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->referenceToId($shopReference))
        ));

        $this->getSharedStorage()->set($newProductReference, $newProductId->getValue());
    }

    /**
     * @When I duplicate product :productReference to a :newProductReference for all shops
     *
     * @param string $productReference
     * @param string $newProductReference
     */
    public function duplicateForAllShops(string $productReference, string $newProductReference): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::allShops()
        ));

        $this->getSharedStorage()->set($newProductReference, $newProductId->getValue());
    }

    /**
     * @When I duplicate product :productReference to a :newProductReference for shop group :shopGroupReference
     *
     * @param string $productReference
     * @param string $newProductReference
     * @param string $shopGroupReference
     */
    public function duplicateForShopGroup(string $productReference, string $newProductReference, string $shopGroupReference): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shopGroup($this->referenceToId($shopGroupReference))
        ));

        $this->getSharedStorage()->set($newProductReference, $newProductId->getValue());
    }

    /**
     * @When I bulk duplicate following products:
     *
     * @param TableNode $productsList
     */
    public function bulkDuplicate(TableNode $productsList): void
    {
        $productIds = [];
        foreach ($productsList->getColumnsHash() as $productInfo) {
            $productIds[] = $this->getSharedStorage()->get($productInfo['reference']);
        }

        try {
            $newProductIds = $this->getCommandBus()->handle(new BulkDuplicateProductCommand($productIds, ShopConstraint::shop($this->getDefaultShopId())));
        } catch (ProductException $e) {
            $this->setLastException($e);

            return;
        }

        /**
         * @var int $oldProductId
         * @var ProductId $newProductId
         */
        foreach ($newProductIds as $oldProductId => $newProductId) {
            foreach ($productsList->getColumnsHash() as $productInfo) {
                $productReferenceId = $this->getSharedStorage()->get($productInfo['reference']);
                if ($productReferenceId === $oldProductId) {
                    $this->getSharedStorage()->set($productInfo['copy_reference'], $newProductId->getValue());
                }
            }
        }
    }
}
