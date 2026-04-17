<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Security\OpenSsl;

use RuntimeException;
use Throwable;

use function openssl_random_pseudo_bytes;

/**
 * Wrapper around the openssl_random_pseudo_bytes function so it can be tested.
 */
class OpenSSL implements OpenSSLInterface
{
    public function getBytes(int $length): string
    {
        // Try catch needed here because it can not work on some systems
        // @see https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
        try {
            return \openssl_random_pseudo_bytes($length);
        } catch (Throwable) {
            throw new RuntimeException('OpenSSL is not supported');
        }
    }
}
