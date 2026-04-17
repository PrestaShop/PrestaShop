<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssignProductToCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AssignProductToCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAssignProductToCategoryException;

/**
 * Adds a category to a product
 *
 * @internal
 */
#[AsCommandHandler]
final class AssignProductToCategoryHandler extends AbstractObjectModelHandler implements AssignProductToCategoryHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param AssignProductToCategoryCommand $command
     */
    public function handle(AssignProductToCategoryCommand $command)
    {
        $this->assignProductToCategory($command);
    }

    /**
     * @param AssignProductToCategoryCommand $command
     *
     * @throws CannotAssignProductToCategoryException
     */
    private function assignProductToCategory(AssignProductToCategoryCommand $command)
    {
        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());
        $product->addToCategories($command->getCategoryId()->getValue());
        if (false === $product->save()) {
            throw new CannotAssignProductToCategoryException(sprintf('Failed to add category to product %d', $command->getProductId()->getValue()));
        }
    }
}
