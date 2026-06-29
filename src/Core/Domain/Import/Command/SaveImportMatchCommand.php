<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportMatchConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ColumnMapping;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\EntityType;

/**
 * Saves a reusable named data-matching configuration. The handler returns its new id.
 */
final class SaveImportMatchCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array<int, string>
     */
    private $dataMapping;

    /**
     * @var int
     */
    private $entityType;

    /**
     * @var int
     */
    private $skipRows;

    /**
     * @param array<int, string> $dataMapping
     *
     * @throws ImportMatchConstraintException
     */
    public function __construct(string $name, array $dataMapping, int $entityType, int $skipRows = 0)
    {
        if ('' === $name) {
            throw new ImportMatchConstraintException('Import match name cannot be empty.', ImportMatchConstraintException::INVALID_NAME);
        }

        $this->name = $name;
        $this->dataMapping = $dataMapping;
        $this->entityType = $entityType;
        $this->skipRows = $skipRows;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, string>
     */
    public function getDataMapping(): array
    {
        return $this->dataMapping;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getColumnMapping(): ColumnMapping
    {
        return new ColumnMapping($this->dataMapping);
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getEntityType(): EntityType
    {
        return new EntityType($this->entityType);
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }
}
