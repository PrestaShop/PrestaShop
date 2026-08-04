<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;

/**
 * Maps each CSV column index to the entity field it feeds (the "data matching" of step 2).
 *
 * The special value "no" marks a column that is ignored.
 */
final class ColumnMapping
{
    public const IGNORED_COLUMN = 'no';

    /**
     * @var array<int, string> column index => field name
     */
    private $mapping;

    /**
     * @param array<int, string> $mapping
     *
     * @throws ImportRunConstraintException
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $columnIndex => $field) {
            if (!is_int($columnIndex) || $columnIndex < 0) {
                throw new ImportRunConstraintException(
                    'Column mapping keys must be non-negative integer column indexes.',
                    ImportRunConstraintException::INVALID_COLUMN_MAPPING
                );
            }
            if (!is_string($field) || '' === $field) {
                throw new ImportRunConstraintException(
                    'Column mapping values must be non-empty field names.',
                    ImportRunConstraintException::INVALID_COLUMN_MAPPING
                );
            }
        }

        $this->mapping = $mapping;
    }

    /**
     * @return array<int, string>
     */
    public function getValue(): array
    {
        return $this->mapping;
    }

    /**
     * Returns the column index feeding the given field, or null if the field is not mapped.
     */
    public function getColumnForField(string $field): ?int
    {
        $column = array_search($field, $this->mapping, true);

        return false === $column ? null : $column;
    }
}
