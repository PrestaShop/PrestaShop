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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateShippingFeatureContext extends AbstractShippingFeatureContext
{
    /**
     * @When I assign product :productReference with following carriers:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function setProductCarriersForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->setCarriers($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I assign product :productReference with following carriers for shop :shopReference:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function setProductCarriersForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $this->setCarriers($productReference, $table, ShopConstraint::shop((int) $this->getSharedStorage()->get($shopReference)));
    }

    /**
     * @When I assign product :productReference with following carriers for all shops:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function setProductCarriersForAllShops(string $productReference, TableNode $table): void
    {
        $this->setCarriers($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function setCarriers(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
        $carrierReferences = $this->getCarrierReferenceIds(array_keys($table->getRowsHash()));

        $this->getCommandBus()->handle(new SetCarriersCommand(
            (int) $this->getSharedStorage()->get($productReference),
            $carrierReferences,
            $shopConstraint
        ));
    }
}
