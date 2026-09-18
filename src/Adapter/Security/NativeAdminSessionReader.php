<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security;

use ErrorException;
use PrestaShop\PrestaShop\Core\Security\AdminSessionReaderInterface;

/**
 * Reads the PHP session shared by the standard BO and FO entry points.
 */
final class NativeAdminSessionReader implements AdminSessionReaderInterface
{
    public function read(): ?array
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            return null;
        }

        $sessionId = $_COOKIE[session_name()] ?? null;
        if (!is_string($sessionId) || !preg_match('/\A[a-zA-Z0-9,-]{1,256}\z/', $sessionId)) {
            return null;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            return hash_equals(session_id(), $sessionId) && isset($_SESSION) ? $_SESSION : null;
        }
        if (headers_sent()) {
            return null;
        }

        // The legacy FO bridge can already have in-memory bags without having
        // opened the PHP session. Preserve these bags and all changed settings.
        $hadPreviousSession = array_key_exists('_SESSION', $GLOBALS);
        $previousSession = $hadPreviousSession ? $_SESSION : [];
        $previousId = session_id();
        $options = [
            'cache_limiter' => '',
            'gc_probability' => '0',
            'use_cookies' => '0',
            'use_strict_mode' => '1',
            'use_trans_sid' => '0',
        ];
        $previousOptions = [];
        foreach ($options as $name => $value) {
            $previousOptions[$name] = ini_get('session.' . $name);
            ini_set('session.' . $name, $value);
        }

        set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            session_id($sessionId);
            // No write/updateTimestamp, garbage collection, cache headers or cookie.
            if (!session_start(['read_and_close' => true]) || !hash_equals($sessionId, session_id())) {
                return null;
            }

            return $_SESSION;
        } catch (ErrorException) {
            return null;
        } finally {
            session_id($previousId);
            if ($hadPreviousSession) {
                $_SESSION = $previousSession;
            } else {
                unset($_SESSION);
            }
            foreach ($previousOptions as $name => $value) {
                ini_set('session.' . $name, $value);
            }
            restore_error_handler();
        }
    }
}
