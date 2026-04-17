<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Feature;

use Configuration;

/**
 * Defines if token in urls are disabled.
 */
final class TokenInUrls
{
    public const DISABLED = 'disabled';
    public const ENV_VAR = '_TOKEN_';

    /**
     * @return bool
     */
    public static function isDisabled()
    {
        return (bool) Configuration::get('PS_SECURITY_TOKEN') === false || getenv(self::ENV_VAR) === self::DISABLED;
    }
}
