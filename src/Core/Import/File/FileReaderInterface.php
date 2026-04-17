<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use Generator;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use SplFileInfo;

/**
 * Interface FileReaderInterface describes a file reader.
 */
interface FileReaderInterface
{
    /**
     * Read the file.
     *
     * @param SplFileInfo $file
     *
     * @return Generator
     *
     * @throws UnreadableFileException
     */
    public function read(SplFileInfo $file);
}
