<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Export\FileWriter;

use PrestaShop\PrestaShop\Core\Export\Data\ExportableDataInterface;
use SplFileInfo;

/**
 * Interface FileWriterInterface.
 */
interface FileWriterInterface
{
    /**
     * Write data to file.
     *
     * @param string $fileName
     * @param ExportableDataInterface $data
     * @param string $separator
     *
     * @return SplFileInfo
     */
    public function write(string $fileName, ExportableDataInterface $data, string $separator): SplFileInfo;
}
