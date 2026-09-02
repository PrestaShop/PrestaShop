<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\SetSuppliersHandlerInterface;

/**
 * Handles @see SetSuppliersCommand using legacy object model
 */
#[AsCommandHandler]
class SetSuppliersHandler implements SetSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param ProductSupplierUpdater $productSupplierUpdater
     */
    public function __construct(
        ProductSupplierUpdater $productSupplierUpdater
    ) {
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetSuppliersCommand $command): array
    {
        return $this->productSupplierUpdater->associateSuppliers($command->getProductId(), $command->getSupplierIds());
    }
}
