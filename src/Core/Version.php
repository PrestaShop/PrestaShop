<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core;

/**
 * This class contains the current prestashop version constants.
 * This will be updated everytime we release a new version.
 */
final class Version
{
    public const VERSION = '9.2.0';
    public const MAJOR_VERSION_STRING = '9';
    public const MAJOR_VERSION = 9;
    public const MINOR_VERSION = 2;
    public const RELEASE_VERSION = 0;

    // This class should not be instanciated
    private function __construct()
    {
    }
}
