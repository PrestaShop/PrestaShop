<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use Symfony\Component\Uid\Uuid;

/**
 * Identifies an import job.
 *
 * Uuid, not Id: it wraps an externally generated token rather than an auto-increment key, and it
 * appears in URLs and polling calls where a sequential id would leak. Generated as v7 so the
 * time-ordered prefix appends to the clustered primary key instead of scattering inserts — free to
 * choose only while the table has no rows.
 */
final class ImportJobUuid
{
    private readonly string $value;

    /**
     * @throws ImportJobConstraintException
     */
    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new ImportJobConstraintException(
                sprintf('Import job uuid "%s" is not a valid UUID.', $value),
                ImportJobConstraintException::INVALID_ID
            );
        }

        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self((string) Uuid::v7());
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
