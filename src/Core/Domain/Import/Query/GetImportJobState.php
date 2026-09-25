<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Query;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;

/**
 * Reads the full state of an import job without advancing it.
 *
 * For a client arriving at a job it is not driving. One already polling ContinueImportJob gets the
 * same shape back from every batch.
 */
final class GetImportJobState
{
    private readonly ImportJobUuid $importJobUuid;

    /**
     * @throws ImportJobConstraintException
     */
    public function __construct(string $importJobUuid)
    {
        $this->importJobUuid = new ImportJobUuid($importJobUuid);
    }

    public function getImportJobUuid(): ImportJobUuid
    {
        return $this->importJobUuid;
    }
}
