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
 * Gets the progress snapshot of an import run (to poll the progress modal).
 */
final class GetImportRunProgress
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
