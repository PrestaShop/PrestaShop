<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductsPositionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class UpdatePositionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product position in category :categoryReference with following values:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function updateProductPosition(string $categoryReference, TableNode $table): void
    {
        $data = $this->localizeByColumns($table);
        $positions = [];
        foreach ($data as $datum) {
            $productId = $this->getSharedStorage()->get($datum['product_reference']);
            $positions[$productId] = [
                'rowId' => $productId,
                'oldPosition' => $datum['old_position'],
                'newPosition' => $datum['new_position'],
            ];
        }
        try {
            $command = new UpdateProductsPositionsCommand($positions, $this->getSharedStorage()->get($categoryReference));
            $this->getCommandBus()->handle($command);
        } catch (PositionException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then products in category :categoryReference should have the following positions:
     *
     * @param string $categoryReference
     * @param TableNode $tableNode
     */
    public function assertPositionInformation(string $categoryReference, TableNode $tableNode): void
    {
        $products = $this->localizeByColumns($tableNode);
        $productRepository = CommonFeatureContext::getContainer()->get(ProductRepository::class);
        $categoryId = new CategoryId($this->getSharedStorage()->get($categoryReference));
        foreach ($products as $product) {
            $productId = new ProductId($this->getSharedStorage()->get($product['product_reference']));
            // @todo: getPositionInCategory seems to only be used for tests, the method shouldn't exist if it is ONLY for tests,
            //        maybe there is other way to assert position?
            Assert::assertSame((int) $product['position'], $productRepository->getPositionInCategory($productId, $categoryId));
        }
    }
}
