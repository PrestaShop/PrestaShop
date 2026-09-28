<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\BatchLimit;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\EntityType;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\FieldMapping;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Starts an import job from a source file and the wizard's frozen configuration.
 *
 * The source is a path, not a basename in the import directory, so one API operation can carry
 * file and configuration together. The handler confines it to the files the import directory lists
 * and to the temp directories PHP writes uploads to (upload_tmp_dir, the system temp): a path
 * otherwise lets a caller read any file, and imported content is quoted back in messages.
 *
 * The "truncate" option is not permission-checked here: it deletes the entity's data in every shop,
 * so the caller must restrict it to super admins, as the legacy page does.
 *
 * Validation is eager, so an instance is always well-formed; environment checks belong to the
 * handler.
 */
final class StartImportJobCommand
{
    /**
     * Mirrors the file_name column: the name is reported as the caller gave it, so a longer one is
     * refused rather than silently shortened into something that names no real file.
     */
    private const MAX_FILE_NAME_LENGTH = 255;

    private readonly EntityType $entityType;

    private readonly string $langIso;

    private readonly FieldMapping $fieldMapping;

    private readonly string $fileName;

    /**
     * @param array<int, string> $fieldMapping column index => field name, "no" for an ignored column
     * @param array<string, mixed> $options what the job does (truncate, dryRun, batchLimit, …).
     *                                      Unknown keys are kept verbatim, so a module importer's
     *                                      own options survive every batch
     * @param string|null $fileName the name the report shows for the file, as the client uploaded
     *                              it; defaults to the path's basename, which for an API upload is
     *                              PHP's temp name
     *
     * @throws ImportJobConstraintException
     */
    public function __construct(
        private readonly string $sourceFilePath,
        string $entityType,
        string $langIso,
        private readonly ShopConstraint $shopConstraint,
        array $fieldMapping,
        private readonly array $options = [],
        private readonly string $csvSeparator = ';',
        private readonly string $multipleValueSeparator = ',',
        private readonly int $skipRows = 1,
        ?string $fileName = null,
    ) {
        // realpath() throws a ValueError on a null byte rather than failing
        if ('' === $sourceFilePath || str_contains($sourceFilePath, "\0")) {
            throw new ImportJobConstraintException(
                'Import source file path cannot be empty or contain a null byte.',
                ImportJobConstraintException::INVALID_SOURCE_PATH
            );
        }
        $fileName ??= basename($sourceFilePath);
        if ('' === $fileName || mb_strlen($fileName) > self::MAX_FILE_NAME_LENGTH) {
            throw new ImportJobConstraintException(
                sprintf('Import file name must be 1 to %d characters long.', self::MAX_FILE_NAME_LENGTH),
                ImportJobConstraintException::INVALID_FILE_NAME
            );
        }
        if (!preg_match('/^[a-z]{2}$/i', $langIso)) {
            throw new ImportJobConstraintException(
                sprintf('Import language "%s" is not a two-letter ISO code.', $langIso),
                ImportJobConstraintException::INVALID_LANG_ISO
            );
        }
        // fgetcsv() takes a single-character delimiter
        if (1 !== strlen($csvSeparator)) {
            throw new ImportJobConstraintException(
                sprintf('CSV separator "%s" must be exactly one character.', $csvSeparator),
                ImportJobConstraintException::INVALID_CSV_SEPARATOR
            );
        }
        // no length bound: it lives in a JSON blob, and str_getcsv takes whatever it is given
        if ('' === $multipleValueSeparator) {
            throw new ImportJobConstraintException(
                'Multiple value separator cannot be empty.',
                ImportJobConstraintException::INVALID_MULTIPLE_VALUE_SEPARATOR
            );
        }
        if ($skipRows < 0) {
            throw new ImportJobConstraintException(
                'Number of skipped rows cannot be negative.',
                ImportJobConstraintException::INVALID_SKIP_ROWS
            );
        }
        // the one option with a range: everything else in the bag is a flag or a module's own
        if (array_key_exists('batchLimit', $options)) {
            if (!is_int($options['batchLimit'])) {
                throw new ImportJobConstraintException(
                    'Import batch limit must be an integer.',
                    ImportJobConstraintException::INVALID_BATCH_LIMIT
                );
            }
            new BatchLimit($options['batchLimit']);
        }

        $this->entityType = new EntityType($entityType);
        $this->langIso = strtolower($langIso);
        $this->fieldMapping = new FieldMapping($fieldMapping);
        $this->fileName = $fileName;

        if (!$this->fieldMapping->hasMappedColumn()) {
            throw new ImportJobConstraintException(
                'Column mapping must feed at least one field.',
                ImportJobConstraintException::INVALID_COLUMN_MAPPING
            );
        }
    }

    public function getSourceFilePath(): string
    {
        return $this->sourceFilePath;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getEntityType(): EntityType
    {
        return $this->entityType;
    }

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getFieldMapping(): FieldMapping
    {
        return $this->fieldMapping;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCsvSeparator(): string
    {
        return $this->csvSeparator;
    }

    public function getMultipleValueSeparator(): string
    {
        return $this->multipleValueSeparator;
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }
}
