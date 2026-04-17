<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
