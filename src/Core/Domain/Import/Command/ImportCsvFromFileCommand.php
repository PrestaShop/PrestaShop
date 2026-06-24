<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Entity;

/**
 * Thin façade command running a full CSV import for a given entity type.
 *
 * It hides which executor runs underneath (the modern Importer for entity types
 * that own a handler, or the legacy AdminImportController row methods for the
 * others) so that consumers never need to know about that split.
 */
final class ImportCsvFromFileCommand
{
    /**
     * @var string name of the CSV file located in the import directory
     */
    private $filename;

    /**
     * @var int one of Entity::TYPE_*
     */
    private $entityType;

    /**
     * @var string language ISO code used for multilingual fields
     */
    private $langIso;

    /**
     * @var array<string, mixed> import options, e.g.:
     *                           - data_mapping: array<int, string> column index => entity field name
     *                           - truncate: bool
     *                           - match_ref: bool
     *                           - forceIDs: bool
     *                           - regenerate: bool
     *                           - separator: string
     *                           - multiple_value_separator: string
     *                           - skip: int
     */
    private $options;

    /**
     * @var bool when true, the import only validates rows and persists nothing
     */
    private $validateOnly;

    /**
     * @var ShopConstraint|null shop context the import runs against (null inherits the BO context)
     */
    private $shopConstraint;

    /**
     * @param string $filename
     * @param int $entityType
     * @param string $langIso
     * @param array<string, mixed> $options
     * @param bool $validateOnly
     * @param ShopConstraint|null $shopConstraint
     *
     * @throws ImportException when the entity type is not supported
     */
    public function __construct(
        string $filename,
        int $entityType,
        string $langIso,
        array $options = [],
        bool $validateOnly = false,
        ?ShopConstraint $shopConstraint = null
    ) {
        if (!in_array($entityType, Entity::AVAILABLE_TYPES, true)) {
            throw new ImportException(sprintf('Import entity type "%d" is not supported.', $entityType));
        }

        $this->filename = $filename;
        $this->entityType = $entityType;
        $this->langIso = $langIso;
        $this->options = $options;
        $this->validateOnly = $validateOnly;
        $this->shopConstraint = $shopConstraint;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getEntityType(): int
    {
        return $this->entityType;
    }

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * @return array<int, string> column index => entity field name
     */
    public function getDataMapping(): array
    {
        return (array) $this->getOption('data_mapping', []);
    }

    public function isValidateOnly(): bool
    {
        return $this->validateOnly;
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }
}
