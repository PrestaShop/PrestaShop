<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Monolog\Logger;
use PrestaShopLogger;
use Psr\Log\LoggerInterface;

/**
 * Class that bridge the legacy implementation of Logger with Psr Logger interface.
 */
class LegacyLogger implements LoggerInterface
{
    public function emergency($message, array $context = []): void
    {
        $this->log(Logger::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = []): void
    {
        $this->log(Logger::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = []): void
    {
        $this->log(Logger::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = []): void
    {
        $this->log(Logger::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = []): void
    {
        $this->log(Logger::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = []): void
    {
        $this->log(Logger::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = []): void
    {
        $this->log(Logger::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = []): void
    {
        $this->log(Logger::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []): void
    {
        switch ($level) {
            case Logger::EMERGENCY:
            case Logger::ALERT:
            case Logger::CRITICAL:
                $pslevel = PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR;
                break;
            case Logger::ERROR:
                $pslevel = PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR;
                break;
            case Logger::WARNING:
                $pslevel = PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING;
                break;
            case Logger::NOTICE:
            case Logger::INFO:
                $pslevel = PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE;
                break;
            case Logger::DEBUG:
            default:
                $pslevel = PrestaShopLogger::LOG_SEVERITY_LEVEL_DEBUG;
        }

        $error_code = !empty($context['error_code']) ? $context['error_code'] : null;
        $object_type = !empty($context['object_type']) ? $context['object_type'] : null;
        $object_id = !empty($context['object_id']) ? $context['object_id'] : null;
        $allow_duplicate = !empty($context['allow_duplicate']) ? $context['allow_duplicate'] : null;

        PrestaShopLogger::addLog($message, $pslevel, $error_code, $object_type, $object_id, $allow_duplicate);
    }
}
