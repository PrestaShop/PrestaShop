<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\ProductDeleter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DeleteProductHandlerInterface;

/**
 * Handles @see DeleteProductCommand using legacy object model
 */
#[AsCommandHandler]
class DeleteProductHandler implements DeleteProductHandlerInterface
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
    public function handle(DeleteProductCommand $command): void
    {
        $this->productDeleter->deleteByShopConstraint(
            $command->getProductId(),
            $command->getShopConstraint()
        );
    }
}
