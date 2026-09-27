<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util;

use Symfony\Component\Dotenv\Dotenv;

/**
 * Loads the .env files of the project root for the entry points. Every variable they set has a default, so a shop
 * without them (removed, or not brought over by an update) still starts.
 */
final class DotEnvLoader
{
    public static function load(string $path): void
    {
        // DO NOT use putEnv
        $dotEnv = (new Dotenv())->usePutenv(false);
        if (is_file($path) || is_file($path . '.dist')) {
            $dotEnv->loadEnv($path);
        } elseif (is_file($path . '.local')) {
            // loadEnv() refuses to run without .env or .env.dist, but .env.local alone is still a source
            $dotEnv->load($path . '.local');
        }
    }
}
