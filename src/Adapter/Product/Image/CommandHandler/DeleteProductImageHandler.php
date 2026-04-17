<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\Update\ProductImageUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\DeleteProductImageHandlerInterface;

/**
 * Handles @see DeleteProductImageCommand
 */
#[AsCommandHandler]
class DeleteProductImageHandler implements DeleteProductImageHandlerInterface
{
    /**
     * @var ProductImageUpdater
     */
    private $productImageUpdater;

    public function __construct(
        ProductImageUpdater $productImageUpdater
    ) {
        $this->productImageUpdater = $productImageUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(DeleteProductImageCommand $command): void
    {
        $this->productImageUpdater->deleteImage($command->getImageId());
    }
}
