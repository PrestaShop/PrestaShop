<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use SplFileInfo;

/**
 * Interface FileOpenerInterface describes an import file opener.
 *
 * @deprecated since 9.3, the import engine normalizes uploads once via
 * CsvImportFileNormalizer and opens working files directly
 */
interface FileOpenerInterface
{
    /**
     * @param SplFileInfo $file
     *
     * @return mixed file handle
     */
    public function open(SplFileInfo $file);
}
