<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\File\Converter;

use SplFileInfo;

/**
 * Interface FileConverterInterface defines a file converter.
 */
interface FileConverterInterface
{
    /**
     * Converts a file to a different format.
     *
     * @param SplFileInfo $sourceFile file to convert
     *
     * @return SplFileInfo converted file
     */
    public function convert(SplFileInfo $sourceFile);
}
