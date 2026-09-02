<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractGetStockMovementsHandler
{
    /**
     * @var StockAvailableRepository
     */
    protected $stockAvailableRepository;

    /**
     * @var StockMovementRepository
     */
    protected $stockMovementRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        StockMovementRepository $stockMovementRepository,
        TranslatorInterface $translator
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->stockMovementRepository = $stockMovementRepository;
        $this->translator = $translator;
    }

    /**
     * @return StockMovement[]
     */
    protected function getStockMovements(StockId $stockId, int $offset, int $limit): array
    {
        $lastStockMovements = $this->stockMovementRepository->getLastStockMovements(
            $stockId,
            $offset,
            $limit
        );

        $apiClients = $this->stockMovementRepository->getApiClientsByStockMovementIds(
            array_merge([], ...array_map(
                static function (array $historyRow): array {
                    return explode(',', (string) $historyRow['id_stock_mvt_list']);
                },
                array_values($lastStockMovements)
            ))
        );

        return array_map(
            function (array $historyRow) use ($apiClients): StockMovement {
                return $historyRow['grouping_type'] === StockMovement::EDITION_TYPE
                    ? $this->createEditionStockMovement($historyRow, $apiClients)
                    : $this->createOrdersStockMovement($historyRow, $apiClients)
                ;
            },
            $lastStockMovements
        );
    }

    /**
     * Extracts the api clients related to the row's stock movements, as two aligned
     * deduplicated lists [ids[], names[]].
     *
     * @param array<string, string|int|null> $historyRow
     * @param array<int, array<string, mixed>> $apiClients indexed by stock movement id
     *
     * @return array{0: int[], 1: string[]}
     */
    protected function extractApiClients(array $historyRow, array $apiClients): array
    {
        $apiClientIds = [];
        $apiClientNames = [];
        foreach (explode(',', (string) $historyRow['id_stock_mvt_list']) as $stockMovementId) {
            $apiClient = $apiClients[(int) $stockMovementId] ?? null;
            if (null === $apiClient || in_array($apiClient['id_api_client'], $apiClientIds, true)) {
                continue;
            }
            $apiClientIds[] = $apiClient['id_api_client'];
            $apiClientNames[] = $apiClient['client_name'];
        }

        return [$apiClientIds, $apiClientNames];
    }

    /**
     * @param array<string, string|int|null> $historyRow
     * @param array<int, array<string, mixed>> $apiClients indexed by stock movement id
     *
     * @return StockMovement
     */
    protected function createEditionStockMovement(array $historyRow, array $apiClients = []): StockMovement
    {
        // A movement can have no employee (e.g. when it was created through the Admin API)
        if (empty($historyRow['employee_firstname']) && empty($historyRow['employee_lastname'])) {
            $employeeName = null;
        } else {
            $employeeName = $this->translator->trans('%firstname% %lastname%', [
                '%firstname%' => $historyRow['employee_firstname'],
                '%lastname%' => $historyRow['employee_lastname'],
            ],
                'Admin.Global'
            );
        }

        [$apiClientIds, $apiClientNames] = $this->extractApiClients($historyRow, $apiClients);

        return StockMovement::createEditionMovement(
            $historyRow['date_add_min'],
            (int) $historyRow['id_stock_mvt_min'],
            (int) $historyRow['id_stock_list'],
            (int) $historyRow['id_order_list'],
            (int) $historyRow['id_employee_list'],
            $employeeName,
            (int) $historyRow['delta_quantity'],
            $apiClientIds,
            $apiClientNames
        );
    }

    /**
     * @param array<string, string|int|null> $historyRow
     * @param array<int, array<string, mixed>> $apiClients indexed by stock movement id
     *
     * @return StockMovement
     */
    protected function createOrdersStockMovement(array $historyRow, array $apiClients = []): StockMovement
    {
        [$apiClientIds, $apiClientNames] = $this->extractApiClients($historyRow, $apiClients);

        return StockMovement::createOrdersMovement(
            $historyRow['date_add_min'],
            $historyRow['date_add_max'],
            explode(',', $historyRow['id_stock_mvt_list']),
            explode(',', $historyRow['id_stock_list']),
            explode(',', $historyRow['id_order_list']),
            explode(',', $historyRow['id_employee_list']),
            (int) $historyRow['delta_quantity'],
            $apiClientIds,
            $apiClientNames
        );
    }
}
