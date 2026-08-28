<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

/**
 * Mapped-row cell helpers shared by the product row orchestrator and its
 * steps. The using class must expose the engine ValueParser as
 * $this->valueParser.
 */
trait ProductRowValueTrait
{
    /**
     * Whether the column is mapped AND carries a non-blank value.
     *
     * Deliberately NOT !empty(): "0" is a legitimate imported value (disabling a
     * boolean field, a zero price or dimension, low_stock_alert...), and !empty()
     * would silently skip those cells.
     *
     * @param array<string, string> $row
     */
    protected function hasValue(array $row, string $field): bool
    {
        return '' !== ($row[$field] ?? '');
    }

    /**
     * @param array<string, string> $row
     */
    protected function isVirtual(array $row): bool
    {
        return true === $this->valueParser->parseBoolean($row['is_virtual'] ?? '');
    }
}
