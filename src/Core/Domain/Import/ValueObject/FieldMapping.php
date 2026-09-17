<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;

/**
 * Maps each CSV column index to the entity field it feeds.
 *
 * The value {@see self::COLUMN_IGNORED} marks an ignored column, so an all-ignored mapping is
 * still a valid object — emptiness is the command's concern.
 */
final class FieldMapping
{
    /**
     * Mirrors ImportJobContext::COLUMN_IGNORED, which the engine reads.
     */
    public const COLUMN_IGNORED = 'no';

    /**
     * @var array<int, string> column index => field name
     */
    private readonly array $mapping;

    /**
     * @param array<int, string> $mapping
     *
     * @throws ImportJobConstraintException
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $columnIndex => $field) {
            if (!is_int($columnIndex) || $columnIndex < 0) {
                throw new ImportJobConstraintException(
                    'Column mapping keys must be non-negative integer column indexes.',
                    ImportJobConstraintException::INVALID_COLUMN_MAPPING
                );
            }
            if (!is_string($field) || '' === $field) {
                throw new ImportJobConstraintException(
                    'Column mapping values must be non-empty field names.',
                    ImportJobConstraintException::INVALID_COLUMN_MAPPING
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
     * Returns the column index feeding the given field, or null when the field is not mapped.
     */
    public function getColumnForField(string $field): ?int
    {
        $column = array_search($field, $this->mapping, true);

        return false === $column ? null : $column;
    }

    /**
     * Whether at least one column feeds a field; an all-ignored mapping would import nothing.
     */
    public function hasMappedColumn(): bool
    {
        foreach ($this->mapping as $field) {
            if (self::COLUMN_IGNORED !== $field) {
                return true;
            }
        }

        return false;
    }
}
