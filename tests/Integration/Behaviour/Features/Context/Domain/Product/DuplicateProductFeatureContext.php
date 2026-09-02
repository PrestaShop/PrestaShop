<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
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
     * @When I duplicate product :productReference to a :newProductReference for shops :shopReferences
     *
     * @param string $productReference
     * @param string $newProductReference
     * @param string $shopReferences
     */
    public function duplicateForShopCollection(string $productReference, string $newProductReference, string $shopReferences): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference),
            ShopCollection::shops($this->referencesToIds($shopReferences))
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
