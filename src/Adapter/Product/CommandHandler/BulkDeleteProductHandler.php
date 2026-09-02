<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\ProductDeleter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Handles command which deletes products in bulk action
 */
#[AsCommandHandler]
final class BulkDeleteProductHandler extends AbstractBulkHandler implements BulkDeleteProductHandlerInterface
{
    /**
     * @var ProductDeleter
     */
    private $productDeleter;

    public function __construct(
        ProductDeleter $productDeleter
    ) {
        $this->productDeleter = $productDeleter;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDeleteProductCommand|null $command
     *
     * @return void
     */
    protected function handleSingleAction(ProductId $productId, $command = null): void
    {
        if (!($command instanceof BulkDeleteProductCommand)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected argument $command of type "%s". Got "%s"',
                    BulkDeleteProductCommand::class,
                    var_export($command, true)
                ));
        }

        $this->productDeleter->deleteByShopConstraint(
            $productId,
            $command->getShopConstraint()
        );
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDeleteProductException();
    }
}
