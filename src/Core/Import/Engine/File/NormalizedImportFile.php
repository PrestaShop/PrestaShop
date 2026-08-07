<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\File;

use SplFileInfo;

/**
 * Result of normalizing an upload into a working file. The record count is
 * measured during the normalization pass itself (which reads every record
 * anyway), so nothing downstream ever re-reads the file just to count:
 * the count travels with the run's frozen config (ImportRunContext,
 * ps_import_run.total_rows).
 */
class NormalizedImportFile
{
    public function __construct(
        public readonly SplFileInfo $workingFile,
        public readonly int $dataRecordCount,
    ) {
    }
}
