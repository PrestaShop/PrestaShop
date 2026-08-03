<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;

/**
 * Applies the run's column-to-field mapping to a raw data row.
 */
final class ProductRowMapper
{
    /**
     * @return array<string, string> field name => trimmed cell value; ignored
     *                               columns dropped; when the same field is
     *                               mapped to several columns the last one wins
     */
    public function map(DataRowInterface $dataRow, ImportRunContext $context): array
    {
        $mappedRow = [];
        foreach ($context->getFieldMapping() as $columnIndex => $fieldName) {
            if (ImportRunContext::COLUMN_IGNORED === $fieldName || '' === $fieldName) {
                continue;
            }

            $value = $dataRow->offsetExists($columnIndex) ? (string) $dataRow[$columnIndex]->getValue() : '';
            $mappedRow[$fieldName] = trim($value);
        }

        return $mappedRow;
    }
}
