<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\SchebTwoFactor;

use PhpEncryption;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class TotpSecretEncryptor
{
    public function __construct(
        #[Autowire('%new_cookie_key%')]
        private string $newCookieKey
    ) {
    }

    public function encrypt(string $plain): string
    {
        $cipherTool = new PhpEncryption($this->newCookieKey);

        return $cipherTool->encrypt($plain);
    }

    public function decrypt(string $encoded): string
    {
        $cipherTool = new PhpEncryption($this->newCookieKey);

        return $cipherTool->decrypt($encoded);
    }
}
