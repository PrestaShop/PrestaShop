<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetCombinationSuppliersHandlerInterface;

#[AsQueryHandler]
class GetCombinationSuppliersHandler extends AbstractProductSupplierHandler implements GetCombinationSuppliersHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository,
        CombinationRepository $combinationRepository
    ) {
        parent::__construct($productSupplierRepository);
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCombinationSuppliers $query): array
    {
        $combinationId = $query->getCombinationId();

        return $this->getProductSuppliersInfo(
            $this->combinationRepository->getProductId($combinationId),
            $combinationId
        );
    }
}
