<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
