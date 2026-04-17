<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductDuplicator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see DuplicateProductCommand
 */
#[AsCommandHandler]
final class DuplicateProductHandler implements DuplicateProductHandlerInterface
{
    /**
     * @var ProductDuplicator
     */
    private $productDuplicator;

    /**
     * @param ProductDuplicator $productDuplicator
     */
    public function __construct(
        ProductDuplicator $productDuplicator
    ) {
        $this->productDuplicator = $productDuplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DuplicateProductCommand $command): ProductId
    {
        return $this->productDuplicator->duplicate($command->getProductId(), $command->getShopConstraint());
    }
}
