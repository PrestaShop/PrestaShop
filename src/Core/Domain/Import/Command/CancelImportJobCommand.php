<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;

/**
 * Cancels an import job that has not reached a terminal status.
 *
 * Does not undo what earlier batches wrote: the handler writes the status only, and a batch in
 * flight notices at its next status probe.
 */
final class CancelImportJobCommand
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
