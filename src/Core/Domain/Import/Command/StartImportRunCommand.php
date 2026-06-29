<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ColumnMapping;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\EntityType;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportOptions;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Starts a new import run from an uploaded file and the chosen options/mapping.
 *
 * The handler persists the run and returns its {@see \PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId}.
 */
final class StartImportRunCommand
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var int
     */
    private $entityType;

    /**
     * @var string
     */
    private $langIso;

    /**
     * @var array<string, mixed>
     */
    private $options;

    /**
     * @var array<int, string>
     */
    private $dataMapping;

    /**
     * @var bool
     */
    private $validateOnly;

    /**
     * @var ShopConstraint|null
     */
    private $shopConstraint;

    /**
     * @param array<string, mixed> $options
     * @param array<int, string> $dataMapping
     *
     * @throws ImportRunConstraintException
     */
    public function __construct(
        string $filename,
        int $entityType,
        string $langIso,
        array $options = [],
        array $dataMapping = [],
        bool $validateOnly = false,
        ?ShopConstraint $shopConstraint = null
    ) {
        if ('' === $filename) {
            throw new ImportRunConstraintException('Import filename cannot be empty.', ImportRunConstraintException::INVALID_FILENAME);
        }
        if ('' === $langIso) {
            throw new ImportRunConstraintException('Import language ISO code cannot be empty.', ImportRunConstraintException::INVALID_LANG_ISO);
        }

        $this->filename = $filename;
        $this->entityType = $entityType;
        $this->langIso = $langIso;
        $this->options = $options;
        $this->dataMapping = $dataMapping;
        $this->validateOnly = $validateOnly;
        $this->shopConstraint = $shopConstraint;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getEntityType(): EntityType
    {
        return new EntityType($this->entityType);
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

    public function getImportOptions(): ImportOptions
    {
        return new ImportOptions(
            !empty($this->options['truncate']),
            !empty($this->options['match_ref']),
            !empty($this->options['forceIDs']),
            !empty($this->options['regenerate']),
            (string) ($this->options['separator'] ?? ';'),
            (string) ($this->options['multiple_value_separator'] ?? ','),
            (int) ($this->options['skip'] ?? 0)
        );
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

    public function isValidateOnly(): bool
    {
        return $this->validateOnly;
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }
}
