<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Encoding;

/**
 * Class CharsetEncoding defines file chartset encoding constants.
 */
final class CharsetEncoding
{
    public const UTF_8 = 'utf-8';
    public const ISO_8859_1 = 'iso-8859-1';

    /**
     * This class is not meant to be instantiated as it is used to access encoding constants only.
     */
    private function __construct()
    {
    }
}
