<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

interface PrestaShopLoggerInterface
{
    public const DEBUG = 0;
    public const INFO = 1;
    public const WARNING = 2;
    public const ERROR = 3;

    /**
     * @param string $message
     * @param int $level
     *
     * @return void
     */
    public function log($message, $level = self::DEBUG);

    /**
     * @param string $message
     *
     * @return void
     */
    public function logDebug($message);

    /**
     * @param string $message
     *
     * @return void
     */
    public function logInfo($message);

    /**
     * @param string $message
     *
     * @return void
     */
    public function logWarning($message);

    /**
     * @param string $message
     *
     * @return void
     */
    public function logError($message);
}
