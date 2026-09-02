<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationSuppliersHandlerInterface;

#[AsCommandHandler]
class UpdateCombinationSuppliersHandler extends AbstractProductSupplierHandler implements UpdateCombinationSuppliersHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param CombinationRepository $combinationRepository
     * @param ProductSupplierRepository $productSupplierRepository
     * @param ProductSupplierUpdater $productSupplierUpdater
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        ProductSupplierRepository $productSupplierRepository,
        ProductSupplierUpdater $productSupplierUpdater
    ) {
        parent::__construct($productSupplierRepository);
        $this->combinationRepository = $combinationRepository;
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationSuppliersCommand $command): array
    {
        $combinationId = $command->getCombinationId();
        $productId = $this->combinationRepository->getProductId($combinationId);

        $productSuppliers = [];
        foreach ($command->getCombinationSuppliers() as $productSupplierDTO) {
            $productSuppliers[] = $this->loadEntityFromDTO($productSupplierDTO);
        }

        return $this->productSupplierUpdater->updateSuppliersForCombination(
            $productId,
            $combinationId,
            $productSuppliers
        );
    }
}
