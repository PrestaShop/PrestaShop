<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Utils;

use ErrorException;
use Throwable;

trait SafeUnserializeTrait
{
    protected function safelyUnserialize(string $serializedToken): mixed
    {
        $token = null;
        $prevUnserializeHandler = ini_set('unserialize_callback_func', self::class . '::handleSafeUnserializeCallback');
        $prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler) {
            if (__FILE__ === $file) {
                throw new ErrorException($msg, 0x37313BC, $type, $file, $line);
            }

            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
        });

        try {
            $token = unserialize($serializedToken);
        } catch (ErrorException $e) {
            if (0x37313BC !== $e->getCode()) {
                return null;
            }
        } catch (Throwable) {
            return null;
        } finally {
            restore_error_handler();
            ini_set('unserialize_callback_func', $prevUnserializeHandler);
        }

        return $token;
    }

    /**
     * @internal
     */
    public static function handleSafeUnserializeCallback(string $class): never
    {
        throw new ErrorException('Class not found: ' . $class, 0x37313BC);
    }
}
