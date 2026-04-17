<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use Psr\Log\LoggerInterface;
use Throwable;

trait SafeLoggerTrait
{
    protected readonly LoggerInterface $logger;

    protected function logError(string $message): void
    {
        try {
            $this->logger->error($message);
        } catch (Throwable) {
            // Prevent the logger from raising an exception and breaking the cache clear
        }
    }

    protected function logWarning(string $message): void
    {
        try {
            $this->logger->warning($message);
        } catch (Throwable) {
            // Prevent the logger from raising an exception and breaking the cache clear
        }
    }

    protected function logInfo(string $message): void
    {
        try {
            $this->logger->info($message);
        } catch (Throwable) {
            // Prevent the logger from raising an exception and breaking the cache clear
        }
    }

    protected function logDebug(string $message): void
    {
        try {
            $this->logger->debug($message);
        } catch (Throwable) {
            // Prevent the logger from raising an exception and breaking the cache clear
        }
    }
}
