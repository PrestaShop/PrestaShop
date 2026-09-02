<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Sample;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Interface SampleFileProviderInterface defines contract for sample import file provider.
 */
interface SampleFileProviderInterface
{
    /**
     * Get sample import file.
     *
     * @param string $sampleFileName
     *
     * @return File|null File if files was found or null otherwise
     */
    public function getFile($sampleFileName);
}
