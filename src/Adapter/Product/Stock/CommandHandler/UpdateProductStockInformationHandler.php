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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler\UpdateProductStockInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

/**
 * Handles @see UpdateProductStockInformationCommand using legacy object model
 */
final class UpdateProductStockInformationHandler implements UpdateProductStockInformationHandlerInterface
{
    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @var MovementReasonRepository
     */
    private $movementReasonRepository;

    /**
     * @param ProductStockUpdater $productStockUpdater
     * @param MovementReasonRepository $movementReasonRepository
     */
    public function __construct(
        ProductStockUpdater $productStockUpdater,
        MovementReasonRepository $movementReasonRepository
    ) {
        $this->productStockUpdater = $productStockUpdater;
        $this->movementReasonRepository = $movementReasonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductStockInformationCommand $command): void
    {
        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = new StockModification(
                $command->getDeltaQuantity(),
                $this->movementReasonRepository->getIdForEmployeeEdition($command->getDeltaQuantity() > 0)
            );
        }

        $this->productStockUpdater->update(
            $command->getProductId(),
            new ProductStockProperties(
                $command->getPackStockType(),
                $stockModification,
                $command->getOutOfStockType(),
                $command->getMinimalQuantity(),
                $command->getLocation(),
                $command->getLowStockThreshold(),
                $command->isLowStockAlertEnabled(),
                $command->getLocalizedAvailableNowLabels(),
                $command->getLocalizedAvailableLaterLabels(),
                $command->getAvailableDate()
            ),
            $command->getShopConstraint()
        );
    }
}
