<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewException;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShopBundle\Entity\AdminGridView;

class GridViewCsvExporter
{
    public const CHUNK_SIZE = 1000;

    private const SENSITIVE_KEY_PARTS = [
        'password',
        'passwd',
        'secure_key',
        'token',
        'api_key',
        'client_secret',
    ];

    /**
     * @param GridFactoryProvider $gridFactoryProvider
     * @param GridViewSearchCriteriaFactory $searchCriteriaFactory
     */
    public function __construct(
        private readonly GridFactoryProvider $gridFactoryProvider,
        private readonly GridViewSearchCriteriaFactory $searchCriteriaFactory,
    ) {
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return array{headers: array<string, string>, rows_provider: callable(int, int): list<array<string, string>>}
     *
     * @throws GridViewException when no grid factory is registered for the view's grid
     */
    public function export(AdminGridView $gridView): array
    {
        $gridId = $gridView->getGridConfiguration()->getGridId();
        $gridFactory = $this->gridFactoryProvider->getFactory($gridId);

        if (null === $gridFactory) {
            throw new GridViewException(
                sprintf('No grid factory is registered for grid "%s"', $gridId),
                GridViewException::UNSUPPORTED_GRID
            );
        }

        $probeGrid = $this->buildGrid($gridFactory, $gridView, 0, 1);
        $headers = $this->buildHeaders($probeGrid, $probeGrid->getData()->getRecords()->all());

        return [
            'headers' => $headers,
            'rows_provider' => function (int $offset, int $limit) use ($gridFactory, $gridView, $headers): array {
                $records = $this->buildGrid($gridFactory, $gridView, $offset, $limit)->getData()->getRecords()->all();

                $rows = [];
                foreach ($records as $record) {
                    $row = [];
                    foreach (array_keys($headers) as $field) {
                        $row[$field] = $this->sanitizeValue($record[$field] ?? null);
                    }
                    $rows[] = $row;
                }

                return $rows;
            },
        ];
    }

    /**
     * @param GridFactoryInterface $gridFactory
     * @param AdminGridView $gridView
     * @param int $offset
     * @param int $limit
     *
     * @return GridInterface
     */
    private function buildGrid(GridFactoryInterface $gridFactory, AdminGridView $gridView, int $offset, int $limit): GridInterface
    {
        return $gridFactory->getGrid($this->searchCriteriaFactory->build($gridView, [
            'offset' => $offset,
            'limit' => $limit,
        ]));
    }

    /**
     * @return array<string, string> column label indexed by record field
     */
    private function buildHeaders(GridInterface $grid, array $records): array
    {
        $firstRecord = $records[0] ?? null;

        $headers = [];
        foreach ($grid->getDefinition()->getColumns() as $column) {
            $field = $column->getId();

            if ($this->isSensitiveField($field)) {
                continue;
            }

            if (null !== $firstRecord && !array_key_exists($field, $firstRecord)) {
                continue;
            }

            $headers[$field] = '' !== $column->getName() ? $column->getName() : $field;
        }

        return $headers;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    private function isSensitiveField(string $field): bool
    {
        $field = strtolower($field);

        foreach (self::SENSITIVE_KEY_PARTS as $sensitiveKeyPart) {
            if (str_contains($field, $sensitiveKeyPart)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function sanitizeValue(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (!is_scalar($value)) {
            $value = (string) json_encode($value);
        }

        $value = (string) $value;

        if ('' !== $value && !is_numeric($value) && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            $value = "'" . $value;
        }

        return $value;
    }
}
