<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util;

class PHPCli
{
    public static function isPHPCli(): bool
    {
        return defined('STDIN') || (strtolower(PHP_SAPI) == 'cli' && empty($_SERVER['REMOTE_ADDR']));
    }
}
