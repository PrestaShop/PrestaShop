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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Position\ValueObject\RowPosition;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductsPositionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductsPositionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductPositionException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;

/**
 * Updates category position using legacy object model
 */
#[AsCommandHandler]
class UpdateProductsPositionsHandler implements UpdateProductsPositionsHandlerInterface
{
    /**
     * @var PositionDefinition
     */
    private $positionDefinition;

    /**
     * @var PositionUpdateFactoryInterface
     */
    private $positionUpdateFactory;

    /**
     * @var GridPositionUpdaterInterface
     */
    private $positionUpdater;

    public function __construct(
        PositionDefinition $positionDefinition,
        PositionUpdateFactoryInterface $positionUpdateFactory,
        GridPositionUpdaterInterface $positionUpdater
    ) {
        $this->positionDefinition = $positionDefinition;
        $this->positionUpdateFactory = $positionUpdateFactory;
        $this->positionUpdater = $positionUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductsPositionsCommand $command): void
    {
        $positionsData = [
            'positions' => $this->convertPositions($command->getPositions()),
            'parentId' => $command->getCategoryId()->getValue(),
        ];

        try {
            $positionUpdate = $this->positionUpdateFactory->buildPositionUpdate($positionsData, $this->positionDefinition);
            $this->positionUpdater->update($positionUpdate);
        } catch (PositionUpdateException|PositionDataException $e) {
            throw new CannotUpdateProductPositionException($e->getMessage(), 0, $e);
        }
    }

    private function convertPositions(array $positions): array
    {
        return array_map(function (RowPosition $rowPosition): array {
            return [
                'rowId' => $rowPosition->getRowId(),
                'oldPosition' => $rowPosition->getOldPosition(),
                'newPosition' => $rowPosition->getNewPosition(),
            ];
        }, $positions);
    }
}
