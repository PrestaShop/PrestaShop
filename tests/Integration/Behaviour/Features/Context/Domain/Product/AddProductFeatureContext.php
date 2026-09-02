<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use Product;

class AddProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I add product :productReference with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function addProductToDefaultShop(string $productReference, TableNode $table): void
    {
        $this->addProduct($productReference, $this->getDefaultShopId(), $table);
    }

    /**
     * @When I add product :productReference to shop :shopReference with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function addProductToShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get($shopReference);
        $this->addProduct($productReference, $shopId, $table);
    }

    /**
     * @Then I should get error that stock available quantity is invalid
     *
     * @return void
     */
    public function assertLastErrorIsInvalidStockAvailableQuantity(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::INVALID_QUANTITY
        );
    }

    /**
     * @param string $productReference
     * @param int $shopId
     * @param TableNode $table
     */
    private function addProduct(string $productReference, int $shopId, TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        try {
            $productId = $this->getCommandBus()->handle(new AddProductCommand(
                $data['type'],
                $shopId,
                $data['name'] ?? []
            ));

            $this->getSharedStorage()->set($productReference, $productId->getValue());
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
        // Fix issue related to modules hooked on `actionProductSave` and calling `Product::priceCalculation()`
        // leading to cache issues later
        Product::resetStaticCache();
    }
}
