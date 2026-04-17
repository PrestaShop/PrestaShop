<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
