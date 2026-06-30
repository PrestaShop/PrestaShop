<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Query;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId;

/**
 * Gets everything the wizard needs about an import run: its status, progression and frozen config.
 *
 * Single read for the progress modal (load / refresh / resume); during active polling the per-batch
 * {@see \PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportBatchReport} already carries live
 * progress, so no extra query per tick is required. Replaces the previously-separate
 * GetImportRunForEditing + GetImportRunProgress.
 */
final class GetImportRunState
{
    /**
     * @var string
     */
    private $importRunId;

    /**
     * @throws ImportRunConstraintException
     */
    public function __construct(string $importRunId)
    {
        $this->importRunId = $importRunId;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getImportRunId(): ImportRunId
    {
        return new ImportRunId($this->importRunId);
    }
}
