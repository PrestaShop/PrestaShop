<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductTypeUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductTypeHandlerInterface;

/**
 * Handle @see UpdateProductTypeCommand
 */
#[AsCommandHandler]
class UpdateProductTypeHandler implements UpdateProductTypeHandlerInterface
{
    /**
     * @var ProductTypeUpdater
     */
    private $productTypeUpdater;

    /**
     * @param ProductTypeUpdater $productTypeUpdater
     */
    public function __construct(
        ProductTypeUpdater $productTypeUpdater
    ) {
        $this->productTypeUpdater = $productTypeUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(UpdateProductTypeCommand $command): void
    {
        $this->productTypeUpdater->updateType($command->getProductId(), $command->getProductType());
    }
}
