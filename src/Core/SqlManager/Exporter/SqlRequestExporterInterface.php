<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\SqlManager\Exporter;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use SplFileInfo;

/**
 * Interface SqlRequestExporterInterface defines contract for SqlRequest exporter.
 */
interface SqlRequestExporterInterface
{
    /**
     * Export SqlRequest query execution result to file.
     *
     * @param SqlRequestId $sqlRequestId
     * @param SqlRequestExecutionResult $sqlRequestExecutionResult
     *
     * @return SplFileInfo
     */
    public function exportToFile(SqlRequestId $sqlRequestId, SqlRequestExecutionResult $sqlRequestExecutionResult);
}
