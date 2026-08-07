<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Applies the run's column-to-field mapping to a raw record. Entity-agnostic:
 * the mapping only knows column indexes and field names, so every entity
 * importer shares this service.
 */
class RowMapper
{
    /**
     * @param array<int, string> $record raw record cells, as read by the file reader
     *
     * @return array<string, string> field name => trimmed cell value; ignored
     *                               columns dropped; when the same field is
     *                               mapped to several columns the last one wins
     */
    public function map(array $record, ImportRunContext $context): array
    {
        $mappedRow = [];
        foreach ($context->getFieldMapping() as $columnIndex => $fieldName) {
            if (ImportRunContext::COLUMN_IGNORED === $fieldName || '' === $fieldName) {
                continue;
            }

            $mappedRow[$fieldName] = trim($record[$columnIndex] ?? '');
        }

        return $mappedRow;
    }
}
