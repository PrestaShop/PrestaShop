<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security;

use PhpEncryption;
use Throwable;

/**
 * Reads the Back Office base path from a valid legacy Admin cookie.
 *
 * The path is routing metadata only and must never be treated as employee authentication.
 */
final class AdminPathProvider
{
    public function getPath(): ?string
    {
        try {
            $cipher = new PhpEncryption(_NEW_COOKIE_KEY_);
        } catch (Throwable) {
            return null;
        }

        foreach ($_COOKIE as $name => $value) {
            if (!is_string($name) || !preg_match('/\APrestaShop-[a-f0-9]{32}\z/', $name) || !is_string($value) || $value === '') {
                continue;
            }

            try {
                $content = $cipher->decrypt($value);
            } catch (Throwable) {
                continue;
            }

            if (!is_string($content) || $content === '') {
                continue;
            }

            $parts = explode('¤', $content);
            $checksum = explode('|', (string) array_pop($parts));

            if (count($checksum) !== 2 || $checksum[0] !== 'checksum') {
                continue;
            }

            if (!hash_equals(hash('sha256', _COOKIE_IV_ . implode('¤', $parts) . '¤'), $checksum[1])) {
                continue;
            }

            $fields = [];

            foreach ($parts as $part) {
                $pair = explode('|', $part);
                if (count($pair) !== 2 || isset($fields[$pair[0]])) {
                    continue 2;
                }
                $fields[$pair[0]] = $pair[1];
            }

            $path = $fields['admin_path'] ?? null;

            if (is_string($path) && preg_match('#\A/(?!/)[A-Za-z0-9._~!$&\'()*+,;=:@%/-]*/\z#', $path)) {
                return $path;
            }
        }

        return null;
    }
}
