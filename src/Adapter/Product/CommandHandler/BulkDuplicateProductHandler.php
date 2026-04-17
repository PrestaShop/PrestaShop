<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductDuplicator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles command which deletes addresses in bulk action
 */
#[AsCommandHandler]
class BulkDuplicateProductHandler extends AbstractBulkHandler implements BulkDuplicateProductHandlerInterface
{
    /**
     * @var ProductDuplicator
     */
    private $productDuplicator;

    /**
     * @param ProductDuplicator $productDuplicator
     */
    public function __construct(ProductDuplicator $productDuplicator)
    {
        $this->productDuplicator = $productDuplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDuplicateProductCommand $command): array
    {
        return $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDuplicateProductCommand $command
     *
     * @return ProductId
     */
    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        return $this->productDuplicator->duplicate($productId, $command->getShopConstraint());
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDuplicateProductException();
    }
}
